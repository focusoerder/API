<?php
namespace Home\Controller;
use Think\Controller\RestController;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true'); 
header("Content-Type: application/json;charset=utf-8");
/**
* 帖子控制器
*/
class PostController extends RestController
{	
	/*
	 * 首页获取精华帖子
	 */
	public function get_Essence(){
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
						$get=\Think\Post::Get_Essence();
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
	 * 获取首页的最新帖子
	 */
	public function get_New(){
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
						$get=\Think\Post::Get_New_Post();
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
	 * 获取全部精华帖子
	 */
	public function Get_All_Essence(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			$num=I('post.num');
			if(empty($num)){
				$num=1;
			}
			if(empty($appkey)){
				$arr['status']=1001;
			}else{
				if(empty($secretkey)){
					$arr['status']=1002;
				}else{
					$res=\Think\OAuth::CheckKey($appkey,$secretkey);
					if($res==1){
						$get=\Think\Post::GetAllEssence($num);
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
	 * 获取前200条最新帖子
	 */
	public function Get_All_New(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			$num=I('post.num');
			if(empty($num)){
				$num=1;
			}
			if(empty($appkey)){
				$arr['status']=1001;
			}else{
				if(empty($secretkey)){
					$arr['status']=1002;
				}else{
					$res=\Think\OAuth::CheckKey($appkey,$secretkey);
					if($res==1){
						$get=\Think\Post::GetAllNew($num);
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
	 * 发布帖子
	 */
	public function post_thread(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			if(empty($appkey)){
				$arr['status']=1001;
			}elseif(empty($secretkey)){
				$arr['status']=1002;
			}else{
				$classify=I('post.classify');
				$title=I('post.title');
				$text=I('post.text');
				$groupid=I('post.readperm');
				if(empty($groupid)){
					$readperm=0;
				}else{
					$readperm=\Think\User::GetReadaccess($groupid);
				}
				$access_token=I('post.access_token');
				$uid=I('post.userid');
				$username=I('post.username');
				$fid=I('post.fid');
				$tags=I('post.tags');
				$aid=I('post.aid');
				$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
				$userid=$token[0];
				$res=\Think\OAuth::CheckKey($appkey,$secretkey);
				if($res==1){
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$checktoken=\Think\OAuth::CheckToken($uid,$access_token);
						if($checktoken==1){
							if(empty($title)){
								$arr['status']=1113;
							}elseif(count($title)>80){
								$arr['status']=1114;
							}elseif(empty($text)){
								$arr['status']=1107;
							}elseif(empty($fid)){
								$arr['status']=1105;
							}else{
								$post=\Think\Post::PostThread($fid,$classify,$title,$text,$readperm,$username,$uid,$tags,$aid);
								if($post==1){
									$arr['status']=1000;
								}else{
									$arr['status']=1103;
								}
							}
						}elseif($checktoken==-1){
							$arr['status']=1013;
						}elseif($checktoken==-2){
							$arr['status']=1014;
						}elseif($checktoken==-3){
							$arr['status']=1016;
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

	/*
	 * 获取该模块的主题分类
	 */
	public function get_type(){
		if($_SERVER['REQUEST_METHOD']=='POST'){
			$appkey=I('post.appkey');
			$secretkey=I('post.secretkey');
			$fid=I('post.fid');
			if(empty($fid)){
				$arr['status']=1105;
			}
			if(empty($appkey)){
				$arr['status']=1001;
			}elseif(empty($secretkey)){
				$arr['status']=1002;
			}else{
				$res=\Think\OAuth::CheckKey($appkey,$secretkey);
				if($res==1){
					$get=\Think\Post::GetType($fid);
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

	/*
	 * 回帖
	 */
	public function reply_thread(){
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
					$fid=I('post.fid');
					$tid=I('post.tid');
					$username=I('post.username');
					$uid=I('post.user_id');
					$text=I('post.text');
					$access_token=I('post.access_token');
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$userid=$token[0];
					if(empty($tid)){
						$arr['status']=1117;
						$this->response($arr,'json');
					}
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$checktoken=\Think\OAuth::CheckToken($uid,$access_token);
						if($checktoken==1){
							if(empty($text)){
								$arr['status']=1107;
							}elseif(empty($fid)){
								$arr['status']=1105;
							}else{
								$post=\Think\Post::RreplyThread($uid,$username,$fid,$tid,$text);
								if($post==1){
									$arr['status']=1000;
								}else{
									$arr['status']=1103;
								}
							}
						}elseif($checktoken==-1){
							$arr['status']=1013;
						}elseif($checktoken==-2){
							$arr['status']=1014;
						}elseif($checktoken==-3){
							$arr['status']=1016;
						}
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
	 * 编辑帖子
	 */
	public function update_thread(){
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
					$username=I('post.username');
					$title=I('post.title');
					$text=I('post.text');
					$access_token=I('post.access_token');
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$userid=$token[0];
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$checktoken=\Think\OAuth::CheckToken($uid,$access_token);
						if($checktoken==1){
							$tid=I('post.tid');
							$pid=I('post.pid');
							$fid=I('post.fid');
							$typeid=I('post.classify');
							$groupid=I('post.readperm');
							$tags=I('post.tags');
							if(empty($tid)){
								$arr['status']=1117;
								$this->response($arr,'json');
							}
							if(empty($pid)){
								$arr['pid']=1118;
								$this->response($arr,'json');
							}if(empty($fid)){
								$arr['pid']=1105;
								$this->response($arr,'json');
							}
							if(empty($groupid)){
								
							}else{
								$readperm=\Think\User::GetReadaccess($groupid);
							}
							$get=\Think\Post::UpdateThread($uid,$tid,$pid,$fid,$typeid,$title,$text,$readperm,$tags);
							if($get==1){
								$arr['status']=1000;
							}elseif($get==-2){
								$arr['status']=1119;
							}else{
								$arr['status']=1103;
							}
						}elseif($checktoken==-1){
							$arr['status']=1013;
						}elseif($checktoken==-2){
							$arr['status']=1014;
						}elseif($checktoken==-3){
							$arr['status']=1016;
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

	/*
	 * 获取主题帖子
	 */
	public function get_thread(){
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
					$username=I('post.username');
					$access_token=I('post.access_token');
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$userid=$token[0];
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$checktoken=\Think\OAuth::CheckToken($uid,$access_token);
						if($checktoken==1){
							$tid=I('post.tid');
							$pid=I('post.pid');
							$fid=I('post.fid');
							if(empty($tid)){
								$arr['status']=1117;
							}elseif(empty($fid)){
								$arr['status']=1105;
							}elseif(empty($pid)){
								$arr['status']=1118;
							}else{
								$get=\Think\Post::GetThread($uid,$tid,$pid,$fid);
								if($get){
									$arr['status']=1000;
									$arr['value']=$get;
								}elseif($get==-1){
									$arr['status']=1119;
								}else{
									$arr['status']=1116;
								}
							}
						}elseif($checktoken==-1){
							$arr['status']=1013;
						}elseif($checktoken==-2){
							$arr['status']=1014;
						}elseif($checktoken==-3){
							$arr['status']=1016;
						}
					}
				}elseif($res==-1){
					$arr['status']=1008;
				}elseif($res==-2){
					$arr['status']=1009;
				}
			}
		}else{
			$arr['status']=1102;
		}
		$this->response($arr,'json');
	}

	/*
	 * 获取该帖子以及回帖
	 */
	public function get_thread_post(){
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
					$username=I('post.username');
					$num=I('post.num');
					$access_token=I('post.access_token');
					$token=explode('-',decrypt(substr($access_token,0,-6),C('KEY')));
					$userid=$token[0];
					$checkuser=\Think\User::CheckUserOnly($userid,$username);
					if($checkuser==-1){
						$arr['status']=1109;
					}elseif($checkuser==-2){
						$arr['status']=1110;
					}elseif($checkuser==1){
						$checktoken=1;//\Think\OAuth::CheckToken($uid,$access_token);
						if($checktoken==1){
							$fid=I('post.fid');
							$tid=I('post.tid');
							$getpost=\Think\Post::GetThreadPost($uid,$fid,$tid,$num);
							if($getpost==-1){
								$arr['status']=1120;
							}elseif($getpost){
								$arr['status']=1000;
								$arr['value']=$getpost;
							}else{
								$arr['status']=1116;
							}
						}elseif($checktoken==-1){
							$arr['status']=1013;
						}elseif($checktoken==-2){
							$arr['status']=1014;
						}elseif($checktoken==-3){
							$arr['status']=1016;
						}
					}
				}
			}
		}else{
			$arr['status']=1012;
		}
		$this->response($arr,'json');
	} 
}