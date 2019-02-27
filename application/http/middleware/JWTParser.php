<?php
/**
 * Created by PhpStorm.
 * User: xjyplayer
 * Date: 2019/1/21
 * Time: 12:38
 */

namespace app\http\middleware;


use app\api\lib\exception\APIException;
use app\common\lib\auth\IAuth;
use app\common\lib\auth\Time;
use think\Middleware;

class JWTParser extends Middleware
{
    public function handle($request, \Closure $next)
    {
        //参数获取
        if ($request->header()) {
            $header = $request->header();
        }

//        header参数验证
        $validate = Validate('api/Header');
        if(!$validate->scene('header')->check($header)){
            throw new APIException($validate->getError(),403);
        };

        //解析参数
        $user_id = [];

//        echo json_encode($header);
        $access_token = $header['access-token'];
        if(empty($access_token)) {
            throw new APIException('token错误', 403);
        }

        try{
            $jwt_token = $access_token;
            $access_token   = (array)IAuth::parseAccessToken($access_token);
        }catch(\Exception $e){
            throw new APIException('token错误',403);
        }

//        dump($access_token);

        //验证参数是否正确
        $validate = Validate('header');
        if(!$validate->scene('access_token')->check($access_token)){
            throw new APIException($validate->getError(),403);
        };

        //验证是否过期
        if($access_token['exp'] < Time::getTime()){
            if($access_token['login_exp'] < Time::getTime())
            {
                throw new APIException('token过期,需要重新登录',403);
            }else{
                try{
                    $sdata = [
                        'user_id' => $access_token['user_id']
                    ];
                    $refresh_token = IAuth::createJWTToken($sdata);
                }catch(\Exception $e){
                    throw new APIException('token过期,刷新token失败,请重新请求');
                }
                throw new APIException('token过期,新的token已经发放',403,0,['$refresh_token' => $refresh_token]);
            }
        }



        //验证是否唯一
        if(!IAuth::checkToken($jwt_token)){
            throw new APIException('token已经失效');
        }

        //传递参数
        $request -> user_id     = $access_token['user_id'];
        $request -> jwt_token   = $jwt_token;
        //用户是否存在
        try{
            $res = model('common/User')->checkUserID($access_token['user_id']);
            if(empty($res)){
                throw new APIException('token出错',403);
            }
        }catch(\Exception $e){
            throw new APIException('token出错',403);
        }

        return $next($request);
    }
}