<?php
/**
 * 用户回复相关验证
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 9:18
 */

namespace app\api\validate;

use think\Validate;

class GoodsComments extends Validate
{
    protected $rule = [
        'user_id'       =>  'require|number',
        'goods_id'      =>  'require|number',
        'reply_type'    =>  'require|number',
        'reply_id'      =>  'require|number',
        'content'       =>  'require'
    ];

    protected $message = [         //提示的错误信息
        'user_id.require'       => '变量传入不规范',
        'user_id.number'        => '变量传入不规范',
        'goods_id.require'      => '变量传入不规范',
        'goods_id.number'       => '变量传入不规范',
        'reply_type.require'    => '变量传入不规范',
        'reply_type.number'     => '变量传入不规范',
        'reply_id.require'      => '变量传入不规范',
        'reply_id.number'       => '变量传入不规范',
        'content.require'       => '变量传入不规范'

    ];

    protected $scene = [        //验证场景
        'goods_comments'        => [
            'goods_id',
            'reply_type',
            'reply_id' ,
            'content' ,
        ],

        'goods_comments_delete' => [
            'user_id',
            'comment_id'
        ],

    ];

    protected $field = [
        'user_id'       =>  '评论用户id',                //设置变量描述信息，错误信息汇报会采用该信息
        'goods_id'      =>  '所评论动态id',
        'reply_type'    =>  '回复类型',
        'reply_id'      =>  '回复的目标',
        'content'       =>  '回复的内容'
    ];
}