<?php
/**
 * 商品点赞相关验证器
 * User: xjyplayer
 * Date: 2019/2/19
 * Time: 19:55
 */
namespace app\api\validate;

use think\Validate;

class GoodsFavor extends Validate
{
    protected $rule = [
        'goods_id'   => 'require|number',
    ];

    protected $message = [         //提示的错误信息
        'goods_id.require'  => '商品id不能为空',               //验证变量.验证规则 => 提示信息
        'goods_id.number'   => '商品id必须为数字',               //验证变量.验证规则 => 提示信息
    ];

    protected $scene = [        //验证场景
        'favor'             =>  ['goods_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_favor'        =>  ['goods_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
    ];

    protected $field = [
        'goods_id'  => '商品id'
    ];
}