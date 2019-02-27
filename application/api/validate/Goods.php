<?php
/**
 * 用户动态相关验证
 * User: xjyplayer
 * Date: 2019/1/25
 * Time: 13:14
 */

namespace app\api\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        "user_id"           => "require|number",
        'name'              => 'require',
        'description'       => 'require',
        'location_id'       => 'require|number',
        'location_detail'   => 'require',
        'image_count'       => 'require|number',
        'goods_id'          => 'require|number'
    ];

    protected $message = [         //提示的错误信息
    ];

    protected $scene = [        //验证场景
        'goods_get'      => ['user_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'one_goods_get'  => ['good_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'goods_add'      => [
            'name',
            'price',
            'description',
            'location_id',
            'location_detail',
            'image_count',
        ],
        'goods_edit'        => [
            'goods_id'
        ],
        'goods_delete'      => [            //彻底删除
            'user_id',
            'goods_id'
        ],
        'goods_operating'      => [            //管理操作
            'user_id',
            'goods_id'
        ],
        'get_goods_by_status'       => [            //获取特定状态的商品
            'user_id',
        ],
        'goods_search'       => [            //获取特定商品
        ],


    ];

    protected $field = [
        "user_id"           => "商品所属用户id",
        'name'              => '商品名',
        'description'       => '商品描述',
        'location_id'       => '商品位置',
        'location_detail'   => '商品位置详细描述',
        'image_count'       => '商品图片数量',
        'goods_id'          => '商品id'
    ];
}
