<?php
/**
 * 用户关注参数验证
 * User: xjyplayer
 * Date: 2019/1/22
 * Time: 12:41
 */

namespace app\api\validate;

use think\Validate;

class UserConcerned extends Validate
{
    protected $rule = [
        'to_user_id'    => 'require|number',
        'from_user_id'  => 'require|number',
    ];

    protected $message = [         //提示的错误信息
        'to_user_id.require'    => '被关注用户id不能为空',               //验证变量.验证规则 => 提示信息
        'to_user_id.number'     => '被关注用户id参数错误',
        'from_user_id.require'  => '关注用户id不能为空',               //验证变量.验证规则 => 提示信息
        'from_user_id.number'   => '关注用户id参数错误'
    ];

    protected $scene = [        //验证场景
        'concern'       => ['to_user_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_concern'  => ['to_user_id'],
        'get_concerned' => ['from_user_id'],
        'get_concern'   => ['from_user_id']
    ];

    protected $field = [
        'to_user_id'    => '被关注用户id',
        'from_user_id'  => '关注用户id'
    ];
}