<?php
namespace Think;

/**
*  oauth模型
*/
class OAuth {
	//检测key
	static public function CheckKey($appkey,$secretkey){
		$client=M('focus_ouath_client');
		$sql=$client->field('secretkey,redirect_url')->where("appkey='".$appkey."'")->find();
		if(empty($sql['secretkey'])){
			return -1;
		}elseif($sql['secretkey']!=$secretkey){
			return -2;
		}else{
			return 1;
		}
	}

	static public function GetURL($appkey,$secretkey){
		$client=M('focus_ouath_client');
		$where['appkey']=$appkey;
		$where['secretkey']=$secretkey;
		$sql=$client->field('redirect_url')->where($where)->find();
		return $sql['redirect_url'];
	}

	//生成key
	static public function SetKey($data){
		$client=M('focus_ouath_client');
		$time=date('Y-m-d H:i:s',time());
		$appkey=md5($data['en_name'].$time);
		$secretkey=md5($data['cn_name'].$time.$data['en_name']).md5($data['redirect_url']);
		$data['appkey']=$appkey;
		$data['secretkey']=$secretkey;
		$data['create_time']=$time;
		$sql=$client->data($data)->add();
		$query=$client->field('appkey,secretkey')->where('id=%d',array($sql))->find();
		if($query){
			return ($query);
		}else{
			return -1;
		}
	}

	//生成token
	static public function GetRefreshToken($username,$user_id){
		$time=time();
		$expires_in=date("Y-m-d H:i:s", strtotime("+1 months", $time));
		$atoken_expires=date("Y-m-d H:i:s", strtotime("+20 minute", $time));
		$refresh_token=encrypt($username.'-'.$time.'-'.$user_id,C('KEY')).generatePassword(6);
		$access_token=encrypt($user_id.'-'.$time,C('KEY')).generatePassword(6);
		$token=M('focus_ouath_token');
		$query=$token->field("id")->where("user_id=%d",array($user_id))->find();
		$data['refresh_token']=$refresh_token;
		$data['access_token']=$access_token;
		$data['expires_in']=$expires_in;
		$data['atoken_expires']=$atoken_expires;
		if(empty($query['id'])){
			$data['user_id']=$user_id;
			$sql=$token->data($data)->add();
			if(empty($sql)){
				return -1;
			}
			$query1=$token->field('refresh_token,access_token')->where('id=%d',array($sql))->find();
			return($query1);
		}else{
			$sql=$token->data($data)->where("user_id=%d",array($user_id))->save();
			if($sql==='flase'){
				return -1;
			}
			$query1=$token->field('refresh_token,access_token')->where('user_id=%d',array($user_id))->find();
			return($query1);
		}
	}

	//检测access_token
	static public function CheckToken($id,$access_token){
		$ouath_token=M('focus_ouath_token');
		$time=date("Y-m-d H:i:s",time());;
		$sql=$ouath_token->field("access_token,atoken_expires")->where('user_id=%d',array($id))->find();
		if(empty($sql['access_token'])){
			return -1;//access_token非法
		}else{
			if($time<$sql['atoken_expires']){
				if($sql['access_token']==$access_token){
					return 1;
				}else{
					return -3;//access_token验证失败
				}
			}else{
				return -2;//access_token过期
			}
		}
		
	}

	//检测refresh_token
	static public function CheckRefreshToken($id,$refresh_token){
		$ouath_token=M('focus_ouath_token');
		$time=date("Y-m-d H:i:s",time());;
		$sql=$ouath_token->field("refresh_token,expires_in")->where('user_id=%d',array($id))->find();
		if(empty($sql['refresh_token'])){
			return -1;//refresh_token非法
		}else{
			if($time<$sql['expires_in']){
				if($sql['refresh_token']==$refresh_token){
					return 1;
				}else{
					return -3;//refresh_token验证失败
				}
			}else{
				return -2;//refresh_token过期
			}
		}
		
	}

	//重新生成access_token
	static function SetAccessToken($user_id){
		$ouath_token=M('focus_ouath_token');
		$time=time();
		$atoken_expires=date("Y-m-d H:i:s", strtotime("+20 minute", $time));
		$access_token=encrypt($user_id.'-'.$time,C('KEY')).generatePassword();
		$data['access_token']=$access_token;
		$data['atoken_expires']=$atoken_expires;
		$sql=$ouath_token->data($data)->where("user_id=%d",array($user_id))->save();
		if($sql!=='flase'){
			$query=$ouath_token->field("access_token")->where("user_id=%d",array($user_id))->find();
			return $query['access_token'];
		}else{
			return -1;
		}
	}
}