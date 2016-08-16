    <?php    
    $privkeypass = '111111'; //私钥密码    
    $pfxpath = "./test.pfx"; //密钥文件路径    
    $priv_key = file_get_contents($pfxpath); //获取密钥文件内容    
    $data = "test"; //加密数据测试test    
         
    //私钥加密    
    openssl_pkcs12_read($priv_key, $certs, $privkeypass); //读取公钥、私钥    
    $prikeyid = $certs['pkey']; //私钥    
    openssl_sign($data, $signMsg, $prikeyid,OPENSSL_ALGO_SHA1); //注册生成加密信息    
    $signMsg = base64_encode($signMsg); //base64转码加密信息    
         
         
    //公钥解密    
    $unsignMsg=base64_decode($signMsg);//base64解码加密信息    
    openssl_pkcs12_read($priv_key, $certs, $privkeypass); //读取公钥、私钥    
    $pubkeyid = $certs['cert']; //公钥    
    $res = openssl_verify($data, $unsignMsg, $pubkeyid); //验证    
    echo $res; //输出验证结果，1：验证成功，0：验证失败    
    ?>  