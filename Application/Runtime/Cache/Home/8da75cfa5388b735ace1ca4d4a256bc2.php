<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录</title>   
    <!-- Bootstrap -->
    <link href="/focus_sg/Public/bootstrap.min.css" rel="stylesheet">
    <!--你自己的样式文件 -->
    <link href="/focus_sg/Public/login.css" rel="stylesheet">       
    <!-- 以下两个插件用于在IE8以及以下版本浏览器支持HTML5元素和媒体查询，如果不需要用可以移除 -->
    <!--[if lt IE 9]>
    <script src="http://apps.bdimg.com/libs/html5shiv/3.7/html5shiv.js"></script>
    <script src="http://apps.bdimg.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
    <body>
        <div class="container login-content">
            <div class="row">
                <!-- 以后可在此处添加产品logo -->
                <div class="col-lg-offset-4 col-lg-4 col-md-4">
                    <!-- 面板 -->
                    <div class="panel panel-blue">
                        <div class="panel-heading dark-overlay">登录</div>
                        <div class="panel-body">
                            <!-- 表单 -->
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">用户</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="username"  placeholder="用户">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">密码</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="password" placeholder="密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary" id="login">登录</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>      
            </div>
        </div>

        <!-- 页脚 -->
        <div class="container-fluid footer-fluid">
            <div class="contaier">
                <div class="row">
                    <div class="col-lg-12">
                        © All rights reserved 深圳福克斯德信息咨询有限公司
                    </div>
                </div>
            </div>
        </div>
        <!-- 如果要使用Bootstrap的js插件，必须先调入jQuery -->
        <script src="/focus_sg/Public/jquery.js"></script>
        <!-- 包括所有bootstrap的js插件或者可以根据需要使用的js插件调用　-->
        <script>
        // jQuery(function(){
        //     $("#login").click(function(){
        //         window.location.href="www.baidu.com";
        //         var username = document.getElementById("username").value;
        //         var password = document.getElementById("password").value;
        //         // $.ajax({
        //         //     type:'POST',
        //         //     url:'http://foucs.oauthserver.cn/focus_sg/set/token',
        //         //     datatype:'json',
        //         //     data:{
        //         //         username:username,
        //         //         password:password,
        //         //     },
        //         //     success:function(data){
        //         //         alert(data.status);
        //         //         alert(data.url);
                        
        //         //         // location.href=data.url;
        //         //         //self.location.href=data.url;
        //         //         //windows.location.href=data.url;
        //         //         // $(function(){    
        //         //         //     location.href = "www.baidu.com";//location.href实现客户端页面的跳转  
        //         //         // });  
        //         //         // window.open(data.url);
        //         //         // window.opener=null; 
        //         //         // window.close();
        //         //         window.location.href="www.baidu.com";
        //         //     },
        //         //     error:function(jqXHR){
        //         //         alert(2222);
        //         //     }
        //         });
        //     });             
        // });
//            var oLogin = document.getElementById('login');

//     oLogin.onclick = function(){
//     var username = document.getElementById('username').value;
//     var password = document.getElementById('password').value;
//     var request = new XMLHttpRequest();
//     request.open("POST", "http://foucs.oauthserver.cn/focus_sg/set/token");
//     request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
//     var data = {
//         username:username,
//         password:password
//     };
//     debugger
//     request.send(data);
//     request.onreadystatechange = function() {
//         if (request.readyState===4) {
//             if (request.status===200) { 

//                 var data = JSON.parse(request.responseText);

//                 if (data.success) { 

//                     alert(data.status);
//                 } else {
                    
//                 }
//             } else {
//                 alert("发生错误：" + request.status);
//             }
//         } 
//     }
// }

    var oLogin = $('#login');
    oLogin.click(function(){
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;
        $.ajax({
            type:'POST',
            url:'http://foucs.oauthserver.cn/focus_sg/set/token',
            datatype:'json',
            data:{
                username:username,
                password:password
            },
        
            success:function(data){
                alert(data.status);
                if(data.status==1000){
                    alert(data.url);
                    location.href=data.url;
                }
            },  
            error:function(jqXHR){
                alert('向服务器请求失败');
            }
        })
    });

        </script>
    </body>
</html>