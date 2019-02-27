<?php
/**
 * 保存所有标准状态码
 * User: xjyplayer
 * Date: 2019/1/11
 * Time: 20:21
 */

return [
    //访问返回码
    //访问失败
    'error'     => 0,
    //成功访问
    'success'   => 1,
    //禁止访问
    'deny'      => -1,

    //业务逻辑码
    //正常状态
    'com'       => 1,
    //等待审核状态
    'rev'       => 0,
    //删除状态
    'del'       => -1,

    //回复相关
    'reply_to_main'     =>  0,
    'reply_to_reply'    =>  1,

    //分页相关
    'list_rows'              =>  10,
];