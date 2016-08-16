<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
/**
* 用户控制器
*/
class UserController extends RestController
{
	/*
	 * 获取用户组别
	 */
	public function get_group(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
			}elseif(empty($secretkey)){
				$arr['status']=1002;
			}else{
				$res=\Think\OAuth::CheckKey($appkey,$secretkey);
				if($res==1){
					$get=\Think\User::GetGroup();
					$arr['status']=1000;
					$arr['value']=$get;
				}elseif($res==-1){
					$arr['status']=1008;
				}elseif($res==-2){
					$arr['status']=1009;
				}
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}

	
}