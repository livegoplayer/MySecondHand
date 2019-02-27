<?php
/**
 * 用户信息参数验证
 * User: xjyplayer
 * Date: 2019/1/21
 * Time: 12:25
 */

namespace app\api\validate;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'user_id'    => 'require|number',
    ];

    protected $message = [         //提示的错误信息
        'user_id.require' => '用户id不能为空'
    ];

    protected $scene = [        //验证场景
        'we_chat_user_info'     => [
            'user_id',
        ],
        'we_chat_user_update'   => [
            'user_id'
        ]
    ];

    protected $field = [
        'user_id' => '用户id'
    ];
}