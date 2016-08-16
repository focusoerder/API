<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
class OAuthController extends RestController{
	//验证请求是否为合作伙伴发出,是就重定向到验证
	public function check_key(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
				$this->response($arr,'json');
			}
			if(empty($secretkey)){
				$arr['status']=1002;
				$this->response($arr,'json');
			}
			$res=\Think\OAuth::CheckKey($appkey,$secretkey);
			if($res==1){
				//session('appkey',1111);
				// $_SESSION["appkey"]=$appkey;
				// cookie('appkey','asda');
				S('appkey',$appkey);
				S('secretkey',$secretkey);
				$_SESSION['type'] = 'apply';
				$arr['status']=1000;
				$arr['url']=C('XM_URL').U('OAuth/login');
				//header("refresh:1;url=www.baidu.com");
				//$this->display('OAuth/login');
			}elseif ($res==-1) {
				$arr['ststus']=1008;
			}elseif ($res==-2) {
				$arr['status']=1009;
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}

	//生成对应的key
	public function set_key(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$cn_name=I('post.cn_name');
			$en_name=I('post.en_name');
			$url=I('post.url');
			if(empty($cn_name)){
				$arr['status']=1101;
				$this->response($arr,'json');
			}
			if(empty($en_name)){
				$arr['status']=1102;
				$this->response($arr,'json');
			}
			if(empty($url)){
				$arr['status']=1104;
				$this->response($arr,'json');
			}
			$data['cn_name']=$cn_name;
			$data['en_name']=$en_name;
			$data['redirect_url']=$url;
			$res=\Think\OAuth::SetKey($data);
			if($res==-1){
				$arr['status']=1103;
			}elseif($res) {
				$arr['status']=1000;
				$arr['value']=$res;
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}

	//验证登录并授权
	public function check_oauth(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$username=I('post.username');
			$password=I('post.password');
			//$authority=I('post.authority');
				if(empty($username)){
					$array['status']=1003;
					$this->response($array,'json');
				}
				if(empty($password)){
					$array['status']=1004;
					$this->response($array,'json');
				}
				$res=\Think\User::CheckUser($username,$password);
				if($res==-1){
					$array['status']=1017;
					$this->response($array,'json');
				}elseif($res==-2){
					$array['status']=1018;
					$this->response($array,'json');
				}elseif($res==-3){
					$array['status']=1121;
					$this->response($array,'json');
				}elseif($res){
					// if(empty($authority)){
					// 	$array['status']=1020;
					// 	$this->response($array,'json');
					// }
					$set=\Think\OAuth::GetRefreshToken($username,$res);
					if($set==-1){
						$array['status']=1103;
						$this->response($array,'json');
					}
					$array['status']=1000;
					$access_token=$set['access_token'];
					$refresh_token=$set['refresh_token'];
					$url=\Think\OAuth::GetURL(S('appkey'),S('secretkey'));
					S('appkey',null);
					S('secretkey',null);
					$array['url']=$url."?access_token=".$access_token."&refresh_token=".$refresh_token;
					//header("Location: http://www.baidu.com");
					//$array['vales']['refresh_token']=openssl_private_decrypt($set['refresh_token']);
					//$array['vales']['access_token']=openssl_private_decrypt($set['access_token']);
				}
		}else{
			$array['status']=1012;
		}
		$this->response($array,'json');
	}

	//重新生成access_token
	public function set_access_token(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$refresh_token=I('post.refresh_token');
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
			}elseif(empty($secretkey)){
				$arr['status']=1002;
			}else{
				$checkkey=\Think\OAuth::CheckKey($appkey,$secretkey);
				if($checkkey==1){
					if(empty($refresh_token)){
						$arr['status']=1006;
					}else{
						$token=explode('-',decrypt(substr($refresh_token,0,-6),C('KEY')));
						$uid=$token[2];
						$checktoken=\Think\OAuth::CheckRefreshToken($uid,$refresh_token);
						if($checktoken==1){
							$set_token=\Think\OAuth::SetAccessToken($uid);
							if($set_token){
								$arr['status']=1000;
								$arr['access_token']=$set_token;
							}else{
								$arr['status']=1111;
							}
						}elseif($checktoken==-1){
							$arr['status']=1023;
						}elseif($checktoken==-2){
							$arr['status']=1024;
						}elseif($checktoken==-3){
							$arr['status']=1026;
						}
					}
				}elseif($checkkey==-1){
					$arr['status']=1008;
				}elseif($checkkey==-2){
					$arr['status']=1009;
				}
			}	
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}
} 