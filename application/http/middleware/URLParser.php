<?php
/**
 * 解析请求中的jwt参数
 * User: xjyplayer
 * Date: 2019/1/14
 * Time: 21:15
 */

namespace app\http\middleware;


use app\api\lib\exception\APIException;
use app\common\lib\auth\IAuth;

class URLParser
{
    public function handle($request, \Closure $next)
    {
        $data = [];
        //判断参数
        if ($request->param('jwt')) {
            $jwt = $request->param('jwt');
        }

        //解析参数
        if(!empty($jwt)) {
//        echo $jwt;
            try{
                $data = (array)IAuth::parseURLToken($jwt);
            }catch(\Exception $e){
                throw new APIException('jwt格式错误',403);
            }
        }

        //传递参数
        $request -> data = $data;
        return $next($request);
    }
}