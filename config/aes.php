<?php
/**
 * Aes.php的配置文件
 * User: mortal
 * Date: 19-1-9
 * Time: 上午11:03
 */

return [
    //api加密安全相关
    'keySalt' => "xjyplayer",  //aes加密密钥加密盐
    "ivSalt" => "app",   //aes加密偏移向量加密盐
    "method" => "AES-128-CFB",
    "padding" => 0,                    //填充方式0为PKCS7填充，1为0填充
];