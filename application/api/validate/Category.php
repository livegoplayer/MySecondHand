<?php
/**
 * 类别的验证
 * User: xjyplayer
 * Date: 2019/2/21
 * Time: 14:05
 */

namespace app\api\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'name'          =>  'require|number',
        'category_id'   =>  'require|number',
        'parent_id'     =>  'require|number',
        'list_order'    =>  'require|number',
    ];

    protected $message = [         //提示的错误信息
        '' => '',               //验证变量.验证规则 => 提示信息
    ];

    protected $scene = [        //验证场景
        'get_child'             => ['parent_id'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'get_category_detail'   => ['category_id'],
        'get_category_goods'    => ['category_id'],
        'add_category'          => [
            'parent_id',
            'name',
        ],
    ];

    protected $field = [
        'name'          => '分类名',                //设置变量描述信息，错误信息汇报会采用该信息
        'parent_id'     => '父栏目id',
        'category_id'   =>  '类别id',
        'list_order'    =>  '排序序号',
    ];
}