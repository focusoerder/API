<?php
namespace Think;
/**
* 导航类
*/
class Navigat{
	/*
	 * 获取导航
	 */
	static function GetNavi(){
		$forum=M('forum_forum');
		$where['status']=1;
		$where['type']='group';
		$where['fup']=0;
		$field="fid,fup,name,displayorder";
		$sql=$forum->field($field)->where($where)->group("displayorder")->select();
		$arr=$sql;
		foreach ($sql as $key => $value) {
			$query=$forum->field($field)->where('fup=%d',array($value['fid']))->select();
			$arr[$key]['sub']=$query;
		}
		return($arr);
	}

	/*
	 * 获取某个模块的所有主题帖标题
	 */
	static function GetThread($fid,$uid,$num=1){
		$n=20;
		$users=M('common_member');
		$thread=M('forum_thread');
		$forumfield=M('forum_forumfield');
		$userssql=$users->field('adminid,groupid')->where("uid=%d",array($uid))->find();
		$fieldsql=$forumfield->field('viewperm')->where("fid=%d",array($fid))->find();
		$field="tid,fid,author,dateline,subject,views";
		$where['fid']=$fid;
		$where['displayorder']=array('egt',0);
		$count=$thread->where($where)->count();
		$max = ceil($count/$n);
		if($num>$max){
			$num=$max;
		}
		$start_id = ( $num - 1 ) * $n;
		if(empty($fieldsql['viewperm'])){
			$sql=$thread->field($field)->where($where)->order("displayorder desc")->limit($start_id,$n)->select();
		}else{
			$arr=explode("\t",trim($fieldsql['viewperm']));
			if(empty($userssql['adminid'])){
				foreach ($arr as $key => $value) {
					if($userssql['groupid']==$value){
						$sql=$thread->field($field)->where($where)->order("displayorder desc")->limit($start_id,$n)->select();
					}else{
						return -1;
					}
				}
			}else{
				$sql=$thread->field($field)->where($where)->order("displayorder desc")->limit($start_id,$n)->select();
			}

		}
		foreach ($sql as $key => $value) {
			$sql[$key]['dateline']=date('Y-m-d',$value['dateline']);
		}
		$arr['value']=$sql;
		$arr['number']=$max;
		return($arr);
	}
}