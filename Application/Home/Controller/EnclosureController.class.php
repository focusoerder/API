<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
/**
* 附件控制器
*/
class EnclosureController extends RestController
{
	/*
	 * 上传附件
	 */
	public function upload_enclosure(){
		session_start();
		$y=date('Ym',time());
        $d=date('d',time());
        //判断是否存在以当前年份为名的目录，没有就创建
        if(!is_dir(C('Image_URL').$y.'/')) {
            mkdir(C('Image_URL').$y. '/',077,ture);
        }
        //判断是否存在以当前月份为名的目录，没有就创建
        if(!is_dir(C('Image_URL').$y.'/'.$d.'/')) {
            mkdir(C('Image_URL').$y. '/'.$d.'/',077,ture);
        }
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
					$uid=I('post.user_id');
					$access_token=I('post.access_token');
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$userid=$token[0];
					$checktoken=\Think\OAuth::CheckToken($userid,$access_token);
					if($checktoken==-1){
						$arr['status']=1013;
					}elseif($checktoken==-2){
						$arr['status']=1014;
					}elseif($checktoken==-3){
						$arr['status']=1016;
					}elseif($checktoken==1){
						$upload = new \Think\Upload();             // 实例化上传类
        				$upload->maxSize   =   2097152;                 // 设置附件上传大小 50M
        				$upload->exts      =   array('chm', 'pdf', 'zip', 'rar', 'tar', 'gz', 'bzip2', 'gif', 'jpg', '				jpeg', 'png','docx','txt','xlsx','xls');// 设置附件上传类型
        				$upload->rootPath  =   C('Image_URL').$y. '/'.$d.'/';     // 设置附件上传根目录
        				$upload->savePath  =   '';                // 设置附件上传（子）目录
        				$upload->replace   =   false;              // 覆盖
        				$upload->saveName  =   time().generatePassword(8);                // 文件名称
        				//$upload->saveExt   =   "rar";
        				$upload->autoSub   =   false;				
        				$info   =   $upload->upload();
        				$filename=$_FILES['image']['name'];
        				$filesize=$_FILES['image']['size'];
        				if(!$info) {// 上传错误提示错误信息
        					// $arr['status']=1115;
        				 //    $arr['value']=$upload->getError();
        				      $this->error($upload->getError());

        				}else{// 上传成功
        					$name=$upload->rootPath.$upload->saveName.'.'.getFileType($filename);
        				   	$up=\Think\Enclosure::UploadEnclosure($uid,$filename,$name,$filesize);
        				    $arr['status']=1000;
        				    $arr['aid']=$up;
        				    if(getFileType($filename)=='gif' || getFileType($filename)=='jpg' || getFileType($filename)=='jpeg' || getFileType($filename)=='png'){
        				    	//$_SESSION[$uid.'typeid']=2;
        				    	S($uid.'typeid',2);
        				    }elseif(getFileType($filename)=='chm' || getFileType($filename)=='pdf' || getFileType($filename)=='zip' || getFileType($filename)=='rar' || getFileType($filename)=='tar' ||
        				    	getFileType($filename)=='gz' || getFileType($filename)=='bzip2' || getFileType($filename)=='docx' || getFileType($filename)=='txt' || getFileType($filename)=='xlsx' || getFileType($filename)=='xls'){	
        				    	//$_SESSION[$uid.'typeid']=1;
        				    	S($uid.'typeid',1);

        				    }

        				    
        				}
					}
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

  //   /*
  //    * 获取附件
  //    */
  //   public function get_enclosure(){
  //   	if($_SERVER['REQUEST_METHOD']=='POST'){
		// 	$appkey=I('post.appkey');
		// 	$secretkey=I('post.secretkey');
		// 	if(empty($appkey)){
		// 		$arr['status']=1001;
		// 	}elseif(empty($secretkey)){
		// 		$arr['status']=1002;
		// 	}else{
		// 		$res=\Think\OAuth::CheckKey($appkey,$secretkey);
		// 		if($res==1){
		// 			$uid=I('post.user_id');
		// 			$access_token=I('post.access_token');
		// 			$checktoken=1;//\Think\OAuth::CheckToken($uid,$access_token);
		// 			if($checktoken==-1){
		// 				$arr['status']=1013;
		// 			}elseif($checktoken==-2){
		// 				$arr['status']=1014;
		// 			}elseif($checktoken==-3){
		// 				$arr['status']=1016;
		// 			}elseif($checktoken==1){
		// 				$tid=I('post.tid');
		// 				$get=\Think\Enclosure::GetEnclosure($aid,$tid);
		// 				if($get){
		// 					$arr['status']=1000;
		// 					$arr['value']=$get;
		// 				}else{
		// 					$arr['status']=1116;
		// 				}
		// 			}
		// 		}elseif($res==-1){
		// 			$arr['status']=1008;
		// 		}elseif($res==-2){
		// 			$arr['status']=1009;
		// 		}
		// 	}
		// }else{
		// 	$arr['status']=1012;
		// }
		// $this->response($arr,'json');
  //   }
	
}