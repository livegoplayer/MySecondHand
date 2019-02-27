<?php
/**
 * 密钥处理授权相关类.用户登录token生成
 * User: xjyplayer
 * Date: 2018/11/24
 * Time: 15:25
 */

namespace app\common\lib\auth;


use think\Exception;
use think\facade\Cache;

abstract class IAuth
{
    /**
     * 解析sign密文
     * @param $string
     * @return mixed
     * @throws \Exception
     */
    public static function signParse($string)
    {
        $decrycode = Aes::getInstance()->decrypt($string);
        return Parser::strToArray($decrycode);
    }

    /**
     * 加密sign
     * @param $data
     * @return string
     * @throws \Exception
     */
    public static function signCreate($data)
    {
        $string = Parser::ArrayToStr($data);
        return Aes::getInstance()->encrypt($string);
    }

    /**
     * 生成一个access_token
     * @param $data
     * @return null|string
     * @throws Exception
     */
    public static function createJWTToken($data)
    {
        //组装当前的token
        try {
            $access_token = MyJWT::getInstance('access_token')->JWTEncode($data);
            self::setTokenHolder($access_token);
            return $access_token;
        } catch (\Exception $e) {
            throw new Exception(__METHOD__."第".__LINE__."行".$e->getMessage());
        }
    }

    /**
     * 解析access_token
     * @param $jwt
     * @return null|object
     * @throws Exception
     */
    public static function parseAccessToken($jwt)
    {
        try {
            return MyJWT::getInstance('access_token')->JWTDecode($jwt);
        } catch (\Exception $e) {
            throw  new Exception(__METHOD__."第".__LINE__."行".$e->getMessage());
        }
    }

    /**
     * 清除特定的Token缓存
     * @param $access_token
     */
    public static function clearOldToken($access_token){
        self::clearTokenHolder($access_token);
    }

    /**
     * 检查指定的缓存是否存在
     * @param $access_token
     * @return boolean
     */
    public static function checkToken($access_token){
        return self::checkTokenHolder($access_token);
    }

    /**
     * 生成一个JWT类型的url_token
     * @param $data
     * @return null|string
     * @throws Exception
     */
    public static function createURLToken($data)
    {
        try {
            $access_token =  MyJWT::getInstance('url_token')->JWTEncode($data);
            return $access_token;
        } catch (\Exception $e) {
            throw new Exception(__METHOD__."第".__LINE__."行".$e->getMessage());
        }
    }

    /**
     * 解析url_token
     * @param $data
     * @return object
     * @throws Exception
     */
    public static function parseURLToken($data)
    {
        try {
            return MyJWT::getInstance('url_token')->JWTDecode($data);
        } catch (\Exception $e) {
            throw new Exception(__METHOD__."第".__LINE__."行".$e->getMessage());
        }
    }

    /**
     * 设置token缓存的方法
     * @param $access_token
     */
    private static function setTokenHolder($access_token){
        //一个小时的有效期
        Cache::set($access_token,1,config('jwt.access_token.payload.token_timeout'));
    }

    /**
     * 删除缓存的方法
     * @param $access_token
     */
    private static function clearTokenHolder($access_token){
        //删除缓存
        Cache::rm($access_token);
    }

    /**
     * 检查缓存是否存在的方法
     * @param $access_token
     * @return boolean
     */
    private static function checkTokenHolder($access_token){
        return Cache::has($access_token);
    }
}
