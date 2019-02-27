<?php
/**
 * 用户关注参数验证
 * User: xjyplayer
 * Date: 2019/1/22
 * Time: 12:41
 */

namespace app\api\validate;

use think\Validate;

class GoodsCollection extends Validate
{
    protected $rule = [
        'user_id'       => 'require|number',
        'goods_id'      => 'require|number',
    ];

    protected $message = [         //提示的错误信息
        'goods_id.require'    => '被关注用户id不能为空',               //验证变量.验证规则 => 提示信息
        'goods_id.number'     => '被关注用户id参数错误',
        'user_id.require'  => '关注用户id不能为空',               //验证变量.验证规则 => 提示信息
        'user_id.number'   => '关注用户id参数错误'
    ];

    protected $scene = [        //验证场景
        'collection'        => ['goods_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_collection'   => ['goods_id'],
        'get_collected'     => ['user_id'],
        'get_collection'    => ['goods_id']
    ];

    protected $field = [
        'goods_id'      => '被收藏商品id',
        'user_id'       => '收藏商品id'
    ];
}