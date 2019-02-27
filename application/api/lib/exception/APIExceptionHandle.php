<?php
/**
 * APIException的处理类
 * User: xjyplayer
 * Date: 2019/1/14
 * Time: 22:13
 */

namespace app\api\lib\exception;

use think\exception\Handle;
use Exception;

class APIExceptionHandle extends Handle
{

    /**
     * 重写的异常处理方法
     * @param Exception $e
     * @return \think\Response|\think\response\Json
     * @throws APIException
     */
    public function render(Exception $e)
    {
        //启用调试模式
        if(config("app_debug") == true){
            return parent::render($e);
        }
        //自定义异常逻辑
        if($e instanceof APIException){
            $message = $e->getMsg();
            $http_code = $e->getHttpcode();
            $status = $e->getStatus();
            $data = $e->getDataArray();
            return api_result($data,$message,$status,$http_code);
        }
        //默认内部异常
        throw new APIException($e->getMessage());
    }
}

