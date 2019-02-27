<?php
/**
 * 用户动态相关验证
 * User: xjyplayer
 * Date: 2019/1/25
 * Time: 13:14
 */

namespace app\api\validate;

use think\Validate;

class UserDynamic extends Validate
{
    protected $rule = [
        "user_id"           => "require|number",
        'location_id'       => 'require|number',
        'location_detail'   => 'require',
        'image_count'       => 'require|number',
        'image_urls'        => 'require'
    ];

    protected $message = [         //提示的错误信息
        'user_id.require'    => '被关注用户id不能为空',               //验证变量.验证规则 => 提示信息
        'user_id.number'     => '被关注用户id参数错误'
    ];

    protected $scene = [        //验证场景
        'user_dynamic_get'      => ['user_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'user_dynamic_add'      => [
            'content',
            'location_id',
            'location_detail',
            'image_count',
        ],
        'user_dynamic_edit'      => [
            'dynamic_id'
        ],
        'user_dynamic_delete'      => [
            'user_id',
            'dynamic_id'
        ],
    ];

    protected $field = [
        'user_id'           => '需要获取动态的用户id',
        'location_id'       => '所属地区id',
        'location_detail'   => '地区详细介绍',
        'image_count'       => '动态图片数目',
        'image_urls'        => '动态图片数组'
    ];
}
