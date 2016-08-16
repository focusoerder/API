<?php
//加密算法如下：
function encrypt($data, $key)
{
    $char=null;
    $str=null;

    $key = md5($key);
    $x  = 0;
    $len = strlen($data);
    $l  = strlen($key);
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
         $x = 0;
        }
        $char = $char. $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++)
    {
        $str = $str. chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}


//解密算法如下：
function decrypt($data, $key)
{
    $char=null;
    $str=null;
    
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
         $x = 0;
        }
        $char = $char. substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++)
    {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
        {
            $str = $str. chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }
        else
        {
            $str = $str. chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}

//随机生成6位字符串
function generatePassword($length=6) { 
    $chars = array_merge(range(0,9), 
                        range('a','z'), 
                        range('A','Z')); 
    shuffle($chars);  
    $password= ''; 
    for($i=0; $i<$length; $i++) { 
        $password.= $chars[$i]; 
    } 
    return $password; 
}

// 获取用户ip
function getIP()
{
global $ip;

if (getenv("HTTP_CLIENT_IP"))
$ip = getenv("HTTP_CLIENT_IP");
else if(getenv("HTTP_X_FORWARDED_FOR"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if(getenv("REMOTE_ADDR"))
$ip = getenv("REMOTE_ADDR");
else
$ip = "Unknow";

return $ip;
} 

/**

 * 获取文件类型

 * @param string $filename 文件名称

 * @return string 文件类型

 */

function getFileType($filename) {

return substr($filename, strrpos($filename, '.') + 1);

}


//外层加密
function enstrhex($str,$key) { 
/* 开启加密算法/ */ 
$td = mcrypt_module_open('twofish', '', 'ecb', ''); 
/* 建立IV，并检测key 的长度*/ 
$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
$ks = mcrypt_enc_get_key_size($td); 
/* 生成key */ 
$keystr = substr(md5($key), 0, $ks); 
/* 初始化加密程序*/ 
mcrypt_generic_init($td, $keystr, $iv); 
/* 加密, $encrypted 保存的是已经加密后的数据*/ 
$encrypted = mcrypt_generic($td, $str); 
/*检测解密句柄，并关闭模块*/ 
mcrypt_module_close($td); 
/*转化为16进制*/ 
$hexdata = bin2hex($encrypted); 
//返回
return $hexdata; 
} 

//外层解密
 function destrhex($str,$key) { 
/* 开启加密算法/ */ 
$td = mcrypt_module_open('twofish', '', 'ecb', ''); 
/*建立IV，并检测key 的长度*/ 
$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
$ks = mcrypt_enc_get_key_size($td); 
/* 生成key */ 
$keystr = substr(md5($key), 0, $ks); 
/* 初始化加密模块，用以解密*/ 
mcrypt_generic_init($td, $keystr, $iv); 
/* 解密*/ 
$encrypted = pack( "H*", $str); 
$decrypted = mdecrypt_generic($td, $encrypted); 
/* 检测解密句柄，并关闭模块*/ 
mcrypt_generic_deinit($td); 
mcrypt_module_close($td); 
/* 返回原始字符串*/ 
return $decrypted;  
}