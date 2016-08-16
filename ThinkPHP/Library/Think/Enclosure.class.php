<?php
namespace Think;
/**
*  附件类
*/
class Enclosure
{
	/*
	 * 上传附件
  	 */	
  	static function UploadEnclosure($uid,$filename,$name,$filesize){
  		$attachment=M('forum_attachment');
  		$unused=M('forum_attachment_unused');
  		$data['uid']=$uid;
  		$data['tableid']=127;
  		$sql=$attachment->data($data)->add();
  		$da['aid']=$sql;
  		$da['uid']=$uid;
  		$da['dateline']=time();
  		$da['filename']=$filename;
  		$da['filesize']=$filesize;
  		$da['attachment']=$name;
  		$query=$unused->data($da)->add();
  		$set=$unused->field("aid,attachment")->where("aid=%d",array($sql))->find();
  		$set['attachment']=C('XM_URL').$set['attachment'];
  		return ($set);
  	}

  	// /*
  	//  * 获取附件
  	//  */
  	// static function GetEnclosure($uid){
  	// 	$attachment=M('forum_attachment');
  	// 	$unuserd=M('forum_attachment_unused');
  	// 	$time=time();
  	// 	$times=date('Y-m-d',strtotime("-1 day", $time));
  	// 	$where['uid']=$uid;
  	// 	$where['dateline']=array('gt',$times);
  	// 	$aid=$attachment->field("tableid")->where($where)->select();
  	// 	$i=0;
  	// 	foreach ($aid as $key => $value) {
  	// 		$set=$attachment->field("tableid")->where("aid=%d",array($value))->find();
  	// 		$field="aid,filename,attachment";
  	// 		if($set['tableid']==127){
  	// 			$sql=$unuserd->field($field)->where("aid=%d",array($value))->find();
  	// 			$arr[$i]=$sql;
  	// 		}else{
  	// 			$forum_attachment=M("forum_attachment_".$set['tableid']);
  	// 			$sql=$forum_attachment->field($field)->where("aid=%d",array($value))->find();
  	// 			$arr[$i]=$sql;
  	// 		}
  	// 		$i++;
  	// 	}
  	// 	return($arr);
  	// }
  	
  	/*
  	 * 
  	 */
}