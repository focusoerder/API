<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'focusforum',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'pre_',    // 数据库表前缀
    
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
    'DEFAULT_MODULE'       =>    'Home', 
    'XM_URL'               =>    'http://foucs.oauthserver.cn',
    'KEY'                  =>    'www.focusorder.com',
    'Image_URL'            =>     '../data/attachment/forum/',
    'OUTER_KEY '           =>     'www.fukeside.com',

    'LOG_RECORD'=>true,  // 进行日志记录
    'LOG_RECORD_LEVEL'       =>   array('EMERG','ALERT','CRIT','ERR','WARN','NOTIC','INFO','DEBUG','SQL'),  // 允许记录的日志级别
    'SESSION_AUTO_START' =>true,
     'DB_FIELDS_CACHE'=> false, //数据库字段缓存
    // 'SHOW_RUN_TIME' =>  false,  //运行时间显示
    // 'SHOW_ADV_TIME' =>  false,  //显示详细的运行时间
    // 'SHOW_DB_TIMES' =>  false,  //显示数据库的操作次数
    // 'SHOW_CACHE_TIMES'=>false,  //显示缓存操作次数
    // 'SHOW_USE_MEM'  =>  false,  //显示内存开销

    // 'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息

    'URL_ROUTER_ON'   => true,
 
     //为rest相关操作设置路由，并设置默认路由返回404
    'URL_ROUTE_RULES'=>array(
        array('set/key','OAuth/set_key'),//生成对应的appkey和secretkey
        array('check/key','OAuth/check_key'),//验证appkey和secretkey
        array('set/token','OAuth/check_oauth'),//生成access_token和refresh_token
        array('set/access_token','OAuth/set_access_token'),//重新生成access_token
        array('get/navigat','Navigat/get_Navigat'),//获取导航模块
        array('get/thread','Navigat/get_Thread'),//获取某个模块的帖子标题
        array('get/essence','Post/get_Essence'),//获取首页的精华帖模块
        array('get/new','Post/get_New'),//获取首页的最新帖模块
        array('get/allessence','Post/Get_All_Essence'),//获取全部精华帖子
        array('get/allnew','Post/Get_All_New'),//获取前200条最新帖子
        array('get/liuyan','Liuyan/Get_Index_Liuyan'),//获取首页留言
        array('get/allliuyan','Liuyan/Get_All_Liuyan'),//获取全部留言
        array('post/liuyan','Liuyan/set_Liuyan'),//发布留言
        array('get/usergroup','User/get_group'),//获取用户组别
        array('post/ thread','Post/post_thread'),//发帖
        array('get/type','Post/get_type'),//获取主题分类
        array('reply/thread','Post/reply_thread'),//回帖
        array('upload/enclosure','Enclosure/upload_enclosure'),//上传附件
        array('get/enclosure','Enclosure/get_enclosure'),//获取附件
        array('get/thread_post','Post/get_thread_post'),//获取帖子以及附件
    )
);