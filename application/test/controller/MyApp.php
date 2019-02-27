<?php
/**
 * 测试用控制器
 * User: xjyplayer
 * Date: 2019/1/20
 * Time: 12:53
 */
namespace app\test\controller;

use app\common\lib\auth\IAuth;

class MyApp
{
    public function getJWTToken($user_id = 12){
        $date = [
            'user_id'   => '12',
        ];
        echo IAuth::createJWTToken($date);
    }

    public function getURLToken($user_id =12){
        $data  = [
            'location_id'   => 13001002,
            'page'          => 1,
            'size'          => 2
        ];
        echo IAuth::createURLToken($data);
    }
}