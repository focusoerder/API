<?php
namespace Think;
/**
*  用户类
*/
class User
{
	/*
	 * 验证用户id与用户名是否一致
	 */
	static function CheckUserOnly($uid,$username){
		$user=M('common_member');
		$sql=$user->field("username")->where('uid=%d',array($uid))->find();
		if(empty($sql['username'])){
			return -1;//用户不存在
		}elseif($sql['username']!=$username) {
			return -2;//用户信息不一致
		}
		return 1;
	}

	/*
	 * 验证用户登录
	 */
	static public function CheckUser($username,$password){
		$ucenter_failedlogins=M('ucenter_failedlogins');
		$where['ip']=getIP();
		$checktime=$ucenter_failedlogins->field('count,lastupdate')->where($where)->find();
		$user=M('ucenter_members');
		$sql=$user->where("username='".$username."'")->find();
		if(empty($sql['username'])){
			return -1;
		}
		if(empty($checktime['count'])){
			if($sql['password']!=md5(md5($password).$sql['salt'])){
				$data['ip']=getIP();
				$data['count']=1;
				$data['lastupdate']=time();
				$add=$ucenter_failedlogins->data($data)->add();
				return -2;
			}
		}elseif($checktime['count']<5){
			$get=$ucenter_failedlogins->where($where)->find();
			if($sql['password']!=md5(md5($password).$sql['salt'])){
				$get=$ucenter_failedlogins->where($where)->find();
				$data['lastupdate'] = time();
				$data['count']=$get['count']+1;
				$ucenter_failedlogins->where(array('ip'=>$where['ip']))->save($data);
				return -2;
			}
		}elseif($checktime['lastupdate']<strtotime("-15 minute")){
			if($sql['password']!=md5(md5($password).$sql['salt'])){
				return -2;
			}else{
				$data['count']=0;
				$add=$ucenter_failedlogins->data($data)->where($where)->save();
			}
		}else{
			return -3;
		}
		return $sql['uid'];
	}

	/*
	 * 获取用户的组别做帖子权限控制
	 */
	static public function GetGroup(){
		$usergroup=M('common_usergroup cu');
		$sql=$usergroup->field("cu.groupid,cu.grouptitle")->join("left join pre_common_usergroup_field p on p.groupid=cu.groupid")->where("readaccess >0")->order("readaccess asc")->select();
		return($sql);
	}

	/*
	 * 通过用户id获取请阅读权限
	 */
	static public function GetOAuth($uid){
		$user=M('common_member');
		$sql=$user->field("adminid,groupid")->where("uid=%d",array($uid))->find();
		$usergroup=M('common_usergroup cu');
		$query=$usergroup->field("p.readaccess")->join("left join pre_common_usergroup_field p on p.groupid=cu.groupid")->where("p.groupid=%d",array($sql['groupid']))->find();
		$arr['adminid']=$sql['adminid'];
		$arr['readaccess']=$query['readaccess'];
		return($arr);

	}

	/*
	 * 根据组别id获取该组别的阅读权限
	 */
	static public function GetReadaccess($groupid){
		$usergroup_field=M('common_usergroup_field');
		$sql=$usergroup_field->field("readaccess")->where("groupid=%d",array($groupid))->find();
		return $sql['readaccess'];
	}
}