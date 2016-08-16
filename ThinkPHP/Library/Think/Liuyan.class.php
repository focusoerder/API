<?php
namespace Think;
/**
* 留言类
*/
class Liuyan
{
	/*
	 * 获取首页留言
	 */
	static function GetIndexLiuyan(){
		$liuyan=M('cnyinglan_liuyan_info');
		$field="uid,username,message,adddate";
		$sql=$liuyan->field($field)->order("adddate desc")->limit(10)->select();
		foreach ($sql as $key => $value) {
			$sql[$key]['adddate']=date('Y-m-d',$value['adddate']);
			$sql[$key]['message']=str_replace('[img]',"<img src='",$value['message']);
			$sql[$key]['message']=str_replace('[/img]',"'/>",$sql[$key]['message']);
		}
		return($sql);
	}

	/*
	 * 获取全部留言
	 */
	static function GetAllLiuyan($num){
		$n=20;
		$liuyan=M('cnyinglan_liuyan_info');
		$max=$liuyan->count();
		if($num>$max){
			$num=$max;
		}
	    $start_id = ( $num - 1 ) * $n;
		$field="uid,username,message,adddate";
		$sql=$liuyan->field($field)->order("adddate desc")->limit($start_id,$n)->select();
		foreach ($sql as $key => $value) {
			$sql[$key]['adddate']=date('Y-m-d H:i:s',$value['adddate']);
		}
		$arr['value']=$sql;
		$arr['num']=$max;
		return($arr);
	}

	/*
	 * 发布留言
	 */
	static function SetLiuyan($uid,$username,$text){
		$liuyan=M('cnyinglan_liuyan_info');
		$data['uid']=$uid;
		$data['username']=$username;
		$data['text']=$text;
		$data['diancan']=0;
		$data['paizhuan']=0;
		$data['adddate']=time();
		$sql=$liuyan->data($data)->add();
		if($sql){
			return 1;
		}else{
			return -1;
		}
	}

	/*
	 * 发送小纸条
	 */
	static function Setnotes($senderid,$sendername,$recipientid,$recipientname,$text){
		$ucenter_pm_lists=M('ucenter_pm_lists');
		$data['authorid']=$senderid;
		$data['pmtype']=1;
		$data['subject']=$text;
		$data['members']=2;
		$data['min_max']= ($senderid<$recipientid) ? $senderid.'_'.$recipientid : $recipientid.'_'.$senderid ;
		$data['dateline']=time();
		$countid=count($senderid);
		$countname=count($sendername);
		$counttext=count($text);
		$data['lastmessage']="a:3:{s:12:\"lastauthorid\";s:".$countid.":\"".$senderid."\";s:10:\"lastauthor\";s:".$countname.":\"".$sendername."\";s:11:\"lastsummary\";s:".$counttext.":\"".$text."\";}";
		$sql=$ucenter_pm_lists->data($data)->add();
		if($sql){
			$ucenter_pm_indexes=M('ucenter_pm_indexes');
			$da['plid']=$sql;
			$query=$ucenter_pm_indexes->data($da)->add();
			if ($query) {
				$ucenter_pm_members=M('ucenter_pm_members');
				$d['plid']=$sql;
				$d['uid']=$senderid;
				$d['isnew']=1;
				$d['pmnum']=1;
				$d['lastupdate']=time();
				$da['lastdateline']=time();
				$querypm_sender=$ucenter_pm_members->data($d)->add();
				$d['uid']=$recipientid;
				$querypm_recipient=$ucenter_pm_members->data($d)->add();
				$messages=M('ucenter_pm_messages_'.substr($sql, -1));
				$datas['pmid']=$query;
				$datas['plid']=$sql;
				$datas['authorid']=$senderid;
				$datas['message']=$text;
				$datas['dateline']=time();
				$sqlpm=$messages->data($datas)->add();
				$check=$messages->where("pmid=%d",array($query))->find();
				if($check){
					return 1;
				}else{
					return -1;
				}
			}else{
				return -1;
			}
		}else{
			return -1;
		}
	}
}