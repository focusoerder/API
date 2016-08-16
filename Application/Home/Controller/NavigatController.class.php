<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
/**
* 导航控制器
*/
class NavigatController extends RestController
{
	/*
	 * 获取导航
	 */
	public function get_Navigat(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
			}else{
				if(empty($secretkey)){
					$arr['status']=1002;
				}else{
					$res=\Think\OAuth::CheckKey($appkey,$secretkey);
					if($res==1){
						$get=\Think\Navigat::GetNavi();
						$arr['status']=1000;
						$arr['value']=$get;
					}elseif($res==-1){
						$arr['status']=1008;
					}elseif($res==-2){
						$arr['status']=1009;
					}
				}
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}

	/*
	 * 获取模块的相关帖子标题
	 */
	public function get_Thread(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$access_token=I('post.access_token');
			$fid=I('post.fid');
			$num=I('post.num');
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
			$checkKey=\Think\OAuth::CheckKey($appkey,$secretkey);
			if($checkKey==1){
				if(empty($num)){
					$num=1;
				}
				if(empty($fid)){
					$arr['status']=1105;
				}
				if(empty($access_token)){
					$arr['status']=1007;
				}else{
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$uid=$token[0];
					if(is_int($uid)){
						$arr['status']=1013;
						$this->response($arr,'json');
					}
					$check=\Think\OAuth::CheckToken($uid,$access_token);
					if($check==-1){
						$arr['status']=1013;
					}elseif($check==-2){
						$arr['status']=1014;
					}elseif($check==-3){
						$arr['status']=1016;
					}elseif ($check==1) {
						$res=\Think\Navigat::GetThread($fid,$uid,$num);
						$arr['status']=1000;
						$arr['num']=$res['number'];
						$arr['value']=$res['value'];
					}
				}
			}elseif($checkKey==-1){
				$arr['status']=1008;
			}elseif($checkKey==-2){
				$arr['status']=1009;
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}
}