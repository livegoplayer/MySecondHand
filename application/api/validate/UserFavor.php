<?php
/**
 * 用户点赞请求验证
 * User: xjyplayer
 * Date: 2019/1/23
 * Time: 16:33
 */

namespace app\api\validate;

use think\Validate;

class UserFavor extends Validate
{
    protected $rule = [
        'to_user_id' => 'require|number'
    ];

    protected $message = [         //提示的错误信息
        'to_user_id.require'    => '被点赞用户id不能为空',               //验证变量.验证规则 => 提示信息
        'to_user_id.number'     => '被点赞用户id参数错误'
    ];

    protected $scene = [        //验证场景
        'favor'       => ['to_user_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_favor'  => ['to_user_id']
    ];

    protected $field = [
        'to_user_id' => '被点赞用户id'
    ];
}