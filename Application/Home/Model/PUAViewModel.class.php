<?php
namespace Home\Model;
use Think\Model\ViewModel;
class PUAViewModel extends ViewModel {
   public $viewFields = array(
     'PUA'=>array('pid','tid','fid','first','username','userid','title','message','tags',''),
   );
 }