<?php
/**
 * 收尾工作
 * User: xjyplayer
 * Date: 2019/1/21
 * Time: 18:52
 */

namespace app\http\middleware;

use app\api\lib\exception\APIException;
use app\common\lib\auth\IAuth;
use think\Middleware;

class JWTClear extends Middleware
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        $jwt_token = $request->jwt_token;
        //删除token记录
        if($response->getCode() == 200) {
            if(config("app_debug") != true){
                IAuth::clearOldToken($jwt_token);
            }
            //刷新token
            $user_id = $request->user_id;
            $udate = [
                'user_id' => $user_id,
            ];
            try{
                $access_token = IAuth::createJWTToken($udate);
            }catch(\Exception $e){
                throw new APIException('刷新token失败,请重新请求');
            }

            $data = $response->getData();
            if(!isset($data['refresh_token'])){
                try{
                    $data['data']['refresh_token'] = $access_token;
                }catch(\Exception $e){
                    throw new APIException('可能你没有设置返回值');
                }
            }
            $response->data($data);
        }
        return $response;
    }
}