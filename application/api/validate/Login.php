<?php
/**
 * 验证登录API
 * User: xjyplayer
 * Date: 2019/1/11
 * Time: 20:10
 */
namespace app\api\validate;
use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'openid'    => 'require',
        'avatar'    => 'require|url',
        'nickname'  => 'require',
        'gender'    => 'require|in:0,1,2',
        'access_token' => 'require'
    ];

    protected $message = [         //提示的错误信息
        'openid.require'    => 'openid不能为空',
        'avatar.require'    => '用户头像不能为空',
        'avatar.url'        => '用户头像地址有误',
        'nickname.require'  => '用户昵称不能为空',
        'gender.require'    => '用户性别不能为空',
        'gender.in:0,1,2'   => '用户性别错误',
        'access_token.require' => 'access_token不能为空'
    ];

    protected $scene = [        //验证场景
        'we_chat_login' => [
            'openid',
        ],

        'we_chat_register' => [
            'openid',
            'avatar',
            'nickname',
            'gender'
        ],

        'we_chat_logout' => [
            'access_token'
        ]
    ];

    protected $field = [
        'openid'    => 'openid',
        'avatar'    => '用户头像',
        'nickname'  => '用户昵称',
        'gender'    => '用户性别',
        'access_token' => '操作授权token'
    ];
}