<?php
/**
 * 用户动态点赞相关动态
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 8:53
 */

namespace app\api\validate;

use think\Validate;

class GoodsCommentsFavor extends Validate
{
    protected $rule = [
        'comment_id' => 'require|number'
    ];

    protected $message = [         //提示的错误信息
        'comment_id.require'    => '被点赞商品评论id不能为空',               //验证变量.验证规则 => 提示信息
        'comment_id.number'     => '被点赞商品评论id参数错误'
    ];

    protected $scene = [        //验证场景
        'favor'       => ['comment_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'read_favor'  => ['comment_id']
    ];

    protected $field = [
        'dynamic_id' => '被点赞商品评论id'
    ];
}