<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * api接口返回函数
 * @param $status
 * @param string $message
 * @param array $data
 * @param int $http_code
 * @return \think\response\Json
 */
function api_result($data=[], $message='请求成功',$status = 1,$http_code = 200)
{
    $data = [
        'status' => intval($status),
        'message' => $message,
        'data' => $data,
    ];
    return json($data,$http_code);
}
