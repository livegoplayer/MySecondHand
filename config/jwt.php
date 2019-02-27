<?php
/**
 * jwt配置
 * User: xjyplayer
 * Date: 19-1-9
 * Time: 下午3:09
 */
return [
    //jwt_url_token
    'url_token' =>      [
        //header数组默认值
        'header' => [
            'des'    =>  'JWT',
            'alg'    =>  'RS256',
            'method' =>  'openssl',
            'kid'    =>  'private_key',
            'type'   =>  'url_token'
        ],
        'payload' => null,
    ],


    //jwt_access_token
    'access_token' =>      [
        //header数组默认值
        'header' => [
            'typ'       => 'JWT',
            'alg'       => 'HS256',
            'method'    => 'hash_hmac',
            'kid'       => 'public_key',
            'type'      =>  'access_token'
        ],
        //存储一些过期时间之类参数
        'payload' => [
            'token_timeout'     => 3600,    //token过期时间
            'login_timeout'     => 7200, //重新登录时间
        ]
    ],
];