<?php
/**
 * header参数验证器
 * User: xjyplayer
 * Date: 2019/1/21
 * Time: 12:43
 */

namespace app\api\validate;
use think\Validate;

class Header extends Validate
{
    protected $rule = [
        'user_id'       => 'require|number',
        'access-token'  => 'require',
        'iat'           => 'require',
        'exp'           => 'require',
        'login_exp'     => 'require',
    ];

    protected $message = [         //提示的错误信息
        'user_id.require'       => '用户id不能为空',
        'user_id.number'        => '用户id错误',
        'access-token.require'  => 'token不能为空',
        'iat.require'           => '创建时间不能为空',
        'exp.require'           => '超时时间不能为空',
        'login_exp.require'     => '登录超时不能为空'
    ];

    protected $scene = [        //验证场景
        'header' => [
            'access-token',
            //todo 添加其他的验证参数
        ],

        'access_token' => [
            'user_id',
            'iat',
            'exp',
            'login_exp',
        ],
    ];

    protected $field = [
        'user_id'       => '用户id',
        'access_token'  => 'token',
        'iat'           => '创建时间',
        'exp'           => '过期时间',
        'login_exp'     => '登录超时时间'
    ];
}