<?php
/**
 * Created by PhpStorm.
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 11:47
 */

namespace app\api\validate;


use think\Validate;

class UserDynamicReplyFavor extends Validate
{
    protected $rule = [
        'reply_id' => 'require|number'
    ];

    protected $message = [         //提示的错误信息
        'reply_id.require'    => '被点赞回复id不能为空',               //验证变量.验证规则 => 提示信息
        'reply_id.number'     => '被点赞回复id参数错误'
    ];

    protected $scene = [        //验证场景
        'favor'       => ['reply_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_favor'  => ['reply_id']
    ];

    protected $field = [
        'reply_id' => '被点赞回复id'
    ];
}