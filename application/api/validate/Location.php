<?php
/**
 * 获取地理位置信息的验证
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 15:01
 */

namespace app\api\validate;

use think\Validate;

class Location extends Validate
{
    protected $rule = [
        'pid'               =>  'require|number',
        'cid'               =>  'require|number',
        'location_id'       =>  'require|number'
    ];

    protected $message = [         //提示的错误信息
        'pid.require'   =>  '参数提交错误',               //验证变量.验证规则 => 提示信息
        'pid.number'    =>  '参数提交错误',
        'cid.require'   =>  '参数提交错误',
        'cid.number'    =>  '参数提交错误',
        'location_id.require'   => '参数提交错误',
        'location_id.number'    => '参数提交错误'
    ];

    protected $scene = [        //验证场景
        'get_city'                  => ['pid'],           //自定义验证的场景名 => 场景下需要验证的变量数组]
        'get_region'                => ['cid'],
        'parse_location'            => ['location_id'],
        'get_location_user'         => ['location_id'],
        'get_location_user_dynamic' => ['location_id'],
        'get_location_goods'        => ['location_id']
    ];
    
    protected $field = [
        'pid'           => '省id',                //设置变量描述信息，错误信息汇报会采用该信息
        'cid'           => '城市id',
        'location_id'   => '地理位置id'
    ];
}