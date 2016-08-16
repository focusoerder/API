    <?php    
        $dn = array(    
            "countryName" => 'China', //所在国家名称    
            "stateOrProvinceName" => 'guangdong', //所在省份名称    
            "localityName" => 'shenzhen', //所在城市名称    
            "organizationName" => 'fukeside',   //注册人姓名    
            "organizationalUnitName" => 'focusorder', //组织名称    
            "commonName" => 'mySelf', //公共名称    
            "emailAddress" => 'admin@focusorder.com' //邮箱    
        );    
             
        $privkeypass = 'linrongfeng'; //私钥密码    
        $numberofdays = 365;     //有效时长    
        $cerpath = "./test.cer"; //生成证书路径    
        $pfxpath = "./test.pfx"; //密钥文件路径    
             
             
        //生成证书    
        $privkey = openssl_pkey_new();    
        $csr = openssl_csr_new($dn, $privkey);    
        $sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);    
        openssl_x509_export($sscert, $csrkey); //导出证书$csrkey    
        openssl_pkcs12_export($sscert, $privatekey, $privkey, $privkeypass); //导出密钥$privatekey    
        //生成证书文件    
        $fp = fopen($cerpath, "w");    
        fwrite($fp, $csrkey);    
        fclose($fp);    
        //生成密钥文件    
        $fp = fopen($pfxpath, "w");    
        fwrite($fp, $privatekey);    
        fclose($fp);    
        ?>   