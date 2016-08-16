<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
/**
* 留言控制器
*/
class LiuyanController extends RestController
{
	/*
	 * 获取首页留言
	 */
	public function Get_Index_Liuyan(){
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
						$get=\Think\Liuyan::GetIndexLiuyan();
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
	 * 获取全部留言
	 */
	public function Get_All_Liuyan(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			$num=I('post.num');
			if(empty($appkey)){
				$arr['status']=1001;
			}else{
				if(empty($secretkey)){
					$arr['status']=1002;
				}else{
					$res=\Think\OAuth::CheckKey($appkey,$secretkey);
					if($res==1){
						$get=\Think\Liuyan::GetAllLiuyan($num);
						$arr['status']=1000;
						$arr['num']=$get['num'];
						$arr['value']=$get['value'];
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
	 * 发布留言
	 */
	public function set_Liuyan(){
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
			$checkKey=\Think\OAuth::CheckKey($appkey,$secretkey);
			if($checkKey==1){
				$uid=I('post.userid');
				$username=I('post.username');
				$text=I('post.text');
				$access_token=I('post.access_token');
				$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
				$userid=$token[0];
				if(empty($uid)){
					$arr['status']=1106;
				}elseif(empty($text)){
					$arr['status']=1107;
				}elseif (count($text)>100) {
					$arr['status']=1108;
				}elseif(empty($access_token)){
					$arr['status']=1007;
				}else{
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$check=\Think\OAuth::CheckToken($uid,$access_token);
						if($check==-1){
							$arr['status']=1013;
						}elseif($check==-2){
							$arr['status']=1014;
						}elseif($check==-3){
							$arr['status']=1016;
						}elseif ($check==1) {
							$res=\Think\Liuyan::SetLiuyan($uid,$username,$text);
							if($res==1){
								$arr['status']=1000;
							}else{
								$arr['status']=1103;
							}
						}
					}
				}
			}elseif ($checkKey==-1) {
				$arr['status']=1008;
			}elseif ($checkKey==-2) {
				$arr['status']=1009;
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}

	/*
	 * 发布小纸条
	 */
	public function set_notes(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
			}elseif(empty($secretkey)){
				$arr['status']=1002;
			}else{
				$senderid=I('post.senderid');
				$sendername=I('post.sendername');
				$recipientid=I('post.recipientid');
				$recipientname=I('post.recipientname');
				$text=I('post.text');
				$checkKey=\Think\OAuth::CheckKey($appkey,$secretkey);
				if($checkKey==1){
					$set=\Think\Liuyan::Setnotes($senderid,$sendername,$recipientid,$recipientname,$text);
					if($set==1){
						$arr['status']=1000;
					}else{
						$arr['status']=1103;
					}
				}elseif($checkKey==-1){
					$arr['status']=1008;
				}elseif($checkKey==-2){
					$arr['status']=1009;
				}
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	}
}