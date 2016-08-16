<?php
namespace Think;
/**
* 帖子类
*/
class Post
{
	/*
	 * 获取首页精华帖
	 */
	static function Get_Essence(){
		$item=M('common_block_item cb');
		$where['bid']=74;
		$field="cb.id,cb.idtype,cb.title,cb.url,ft.author,ft.dateline,ft.fid";
		$sql=$item->field($field)->where($where)->join("left join pre_forum_thread ft on ft.tid=cb.id ")->order("cb.displayorder asc")->select();
		$i=0;
		foreach ($sql as $key => $value) {
			if($value['idtype']=='tid'){
				$arr[$i]['tid']=$value['id'];
			}else{
				$parameters=parse_url($value['url']);
				parse_str($parameters['query']);
				$arr[$i]['tid']=$tid;
			}
			$arr[$i]['fid']=$value['fid'];
			$arr[$i]['title']=$value['title'];
			$arr[$i]['author']=$value['author'];
			$arr[$i]['dateline']=date('Y-m-d',$value['dateline']);
			$i++;
		}
		return($arr);
	}

	/*
	 * 获取首页最新帖子
	 */
	static function Get_New_Post(){
		$item=M('common_block_item cb');
		$where['bid']=132;
		$field="cb.id,cb.idtype,cb.title,cb.url,ft.author,ft.dateline,ft.fid";
		$sql=$item->field($field)->where($where)->join("left join pre_forum_thread ft on ft.tid=cb.id ")->order("cb.displayorder asc")->select();
		$i=0;
		foreach ($sql as $key => $value) {
			$arr[$i]['tid']=$value['id'];
			$arr[$i]['fid']=$value['fid'];
			$arr[$i]['title']=$value['title'];
			$arr[$i]['author']=$value['author'];
			$arr[$i]['dateline']=date('Y-m-d',$value['dateline']);
			$i++;
		}
		return($arr);
	}

	/*
	 * 获取全部精华帖子
	 */
	static function GetAllEssence($num){
		$n=25;
		$thread=M('forum_thread');
		$field="tid,fid,author,authorid,subject,dateline";
		$where['digest']=1;
		$count=$thread->where($where)->count();
		$max = ceil($count/$n);
		if($num>$max){
			$num=$max;
		}
		$start_id = ( $num - 1 ) * $n;
		$max = ceil($count/$n);
		$sql=$thread->field($field)->where($where)->limit($start_id,$n)->select();
		foreach ($sql as $key => $value) {
			$sql[$key]['dateline']=date('Y-m-d',$value['dateline']);
		}
		$arr['value']=$sql;
		$arr['num']=$max;
		return($arr);
	}

	/*
	 * 获取前200条最新帖子
	 */
	static function GetAllNew($num){
		$n=25;
		$thread=M('forum_thread');
		$field="tid,fid,author,authorid,subject,dateline";
		$where['digest']=0;
		$count=200;
		$max = ceil($count/$n);
		if($num>$max){
			$num=$max;
		}
		$start_id = ( $num - 1 ) * $n;
		$max = ceil($count/$n);
		$sql=$thread->field($field)->where($where)->limit($start_id,$n)->order("dateline desc")->select();
		foreach ($sql as $key => $value) {
			$sql[$key]['dateline']=date('Y-m-d',$value['dateline']);
		}
		$arr['value']=$sql;
		$arr['num']=$max;
		return($arr);
	}

	/*
	 * 获取该版块的主题分类
	 */
	static function GetType($fid){
		$threadclass=M('forum_threadclass');
		$sql=$threadclass->field("typeid,name")->where("fid=%d",array($fid))->select();
		return($sql);
	}

	/*
	 * 发帖
	 */
	static function PostThread($fid,$classify,$title,$text,$readperm,$username,$uid,$tags,$aid){
		$thread=M('forum_thread');
		$post=M('forum_post');
		$posttable=M('forum_post_tableid');
		$uidtype = S($uid.'typeid');
		$data['fid']=$fid;
		$data['typeid']=$classify;
		$data['readperm']=$readperm;
		$data['author']=$username;
		$data['authorid']=$uid;
		$data['subject']=$title;
		$data['dateline']=time();
		$data['attachment']= (empty($uidtype)) ? 0 : $uidtype ;//看附件接口
		$data['lastpost']=time();
		$data['lastposter']=$username;
		$sql=$thread->data($data)->add();
		if($sql){
			$t=M()->execute("INSERT INTO pre_forum_post_tableid VALUES()");
			$pid=$posttable->field("pid")->order("pid desc")->limit(1)->select();
			$da['pid']=$pid[0]['pid'];
			$da['fid']=$fid;
			$da['tid']=$sql;
			$da['first']=1;
			$da['author']=$username;
			$da['authorid']=$uid;
			$da['subject']=$title;
			$da['dateline']=time();
			$da['message']=$text;
			$da['useip']=getIP();
			$da['port']=$_SERVER['REMOTE_PORT'];
			//$da['attachment'] = (empty($_SESSION[$uid.'typeid'])) ? 0 : $_SESSION[$uid.'typeid'] ;//看附件接口
			$da['attachment'] = (empty($uidtype)) ? 0 : $uidtype ;//看附件接口
			$da['tags']=$tags;
			$query=$post->data($da)->add();
			$checkpid=$post->field("tid")->where("pid=%d",array($da['pid']))->find();
			if($checkpid['tid']){
				if($da['attachment']==0){
					S($uid."typeid",null);
					return 1;
				}else{
					$d['tid']=$checkpid['tid'];
					$d['pid']=$da['pid'];
					$d['tableid']=substr($checkpid['tid'], -1);
					if(empty($aid)){
						return 1;
					}else{
						foreach ($aid as $key => $value) {
							$attachment=M('forum_attachment');
							$imgsql=$attachment->data($d)->where("aid=%d",array($value))->save();
							if($imgsql!=='false'){
								$unused=M('forum_attachment_unused');
								$setimg=$unused->where("aid=%d",array($value))->find();
								$delimg=$unused->where("aid=%d",array($value))->delete();
								$forum_attachment=M('forum_attachment_'.$d['tableid']);
								$setimg['tid']=$checkpid['tid'];
								$setimg['pid']=$da['pid'];
								$insql=$forum_attachment->data($setimg)->add();
								S($uid."typeid",null);
								return 1;
							}else{
								return -1;
							}
						}
					}
				}
			}else{
				$del=$thread->where("tid=%d",array($sql))->delete();
				return -1;
			}
		}else{
			return -1;
		}
	}

	/*
	 * 回帖
	 */
	static function RreplyThread($uid,$username,$fid,$tid,$text){
		$posttable=M('forum_post_tableid');
		$post=M('forum_post');
		$thread=M('forum_thread');
		$t=M()->execute("INSERT INTO pre_forum_post_tableid VALUES()");
		$pid=$posttable->field("pid")->order("pid desc")->limit(1)->select();
		$da['pid']=$pid[0]['pid'];
		$da['fid']=$fid;
		$da['tid']=$tid;
		$da['author']=$username;
		$da['authorid']=$uid;
		$da['dateline']=time();
		$da['message']=$text;
		$da['useip']=getIP();
		$da['port']=$_SERVER['REMOTE_PORT'];
		$da['attachment']=0;//看附件接口
		$query=$post->data($da)->add();
		$checkpid=$post->field("tid")->where("pid=%d",array($da['pid']))->find();
		if(empty($checkpid['tid'])){
			return -1;			
		}else{
			$data['lastpost']=time();
			$data['lastposter']=$username;
			$sql=$thread->data($data)->where("tid=%d",array($checkpid['tid']))->save();
			if($sql!=='flase'){
				return 1;
			}else{
				$del=$post->where("pid=%d",array($da['pid']))->delete();
				return -1;
			}
		}
	}

	/*
	 * 编辑帖子
	 */
	static function UpdateThread($uid,$tid,$pid,$fid,$typeid,$title,$text,$readperm,$tags){
		$post=M('forum_post');
		$thread=M('forum_thread');
		$where['tid']=$tid;
		$where['pid']=$pid;
		$where['fid']=$fid;
		$check=$thread->field("authorid")->where($where)->find();
		if($uid==$check['authorid']){
			$data['subject']=$title;
			if(!empty($readperm)){
				$data['readperm']=$readperm;
			}
			$sql=$thread->data($data)->where($where)->save();
			if($sql!=='flase'){
				$da['subject']=$title;
				$da['message']=$text;
				$da['attachment']=(empty(S($uid."typeid")))? 0 : $uidtype ;//看附件接口;
				$da['tags']=$tags;
				$query=$post->data($da)->where($where)->save();
				if($query!=='flase'){
					return 1;
				}else{
					return -1;
				}
			}else{
				return -1;
			}
		}else{
			return -2;
		}
	}

	/*
	 * 获取帖子
	 */
	static function GetThread($uid,$tid,$pid,$fid){
		$thread=M('forum_thread');
		$where['tid']=$tid;
		$where['pid']=$pid;
		$where['fid']=$fid;
		$check=$thread->field("authorid")->where($where)->find();
		if($uid==$check['authorid']){
			$post=M('forum_post p');
			$field="p.pid,p.tid,p.fid,p.subject,p.message,p.tags";
			$sql=$post->field($field)->where($where)->find();
			return($sql);
		}else{
			return -1;
		}
	}

	/*
	 * 获取帖子以及回帖
	 */
	static function GetThreadPost($uid,$fid,$tid,$num){
		$where['fid']=$fid;
		$where['tid']=$tid;
		$oauth=\Think\User::GetOAuth($uid);
		$thread=M('forum_thread');
		$sql=$thread->field("readperm")->where($where)->find();
		if($oauth['adminid']==0){
			if($sql['readperm']<=$oauth['readaccess']){
				return -1;
			}
		}
		$i=substr($tid, -1);
		$post=M('forum_post');
		$common_member=M('common_member om');
		$forum_attachment=M('forum_attachment_'.$i);
		$field="pid,tid,fid,author,authorid,subject,dateline,message,attachment,tags";
		$query=$post->field($field)->where($where)->select();
		foreach ($query as $key => $value) {
			$getgroup=$common_member->field("cu.grouptitle")->join("left join pre_common_usergroup cu on cu.groupid=om.groupid")->where("om.uid=%d",array($value['authorid']))->find();
			$array[$key]['pid']=$value['pid'];
			$array[$key]['tid']=$value['tid'];
			$array[$key]['fid']=$value['fid'];
			$array[$key]['username']=$value['author'];
			$array[$key]['userid']=$value['authorid'];
			if(!empty($value['subject'])){
				$array[$key]['title']=$value['subject'];
			}
			$array[$key]['time']=$value['dateline'];
			$array[$key]['text']=$value['message'];
			$array[$key]['tags']=$value['tags'];
			$array[$key]['groupname']=$getgroup['grouptitle'];
			if($value['attachment']>0){
				$wheres['tid']=$tid;
				$wheres['pid']=$value['pid'];
				$wheres['uid']=$value['authorid'];
				$getenclosure=$forum_attachment->field("aid,filename,attachment")->where($wheres)->select();
				$array[$key]['enclosure']=$getenclosure;
			}
		}
		return($array);
	}

}