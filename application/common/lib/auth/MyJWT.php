<?php
/**
 * 用来管理和读取jwt.php配置文件参数的类，可以根据需求修改，设计成单例模式
 * User: xjyplayer
 * Date: 19-1-9
 * Time: 下午6:23
 */

namespace app\common\lib\auth;

use think\Exception;
use think\facade\Log;

class MyJWT
{
    /**
     * @var null jwt header_config
     */
    private $header = null;

    /**
     * @var null 存储主要数据的配置
     */
    private $payload = null;

    /**
     * @var mixed|null 测试用
     */
    private $private_key = null;

    /**
     * @var null  加密解密专用密钥
     */
    private $public_key = null;

    /**
     * @var null jwt token type
     */
    private $token_type = null;

    /**
     * @var null Instance for JWT
     */
    private static $MyJWT = null;

    /**
     * @var array Instance array for JWT
     */
    private $token_types = ["url_token","access_token"];

    /**
     * MyJWT constructor.
     * @param $token_type
     */
    private function __construct($token_type)
    {
        $this->private_key = config('private_key');
        $this->init($token_type);
    }

    /**
     * init or re_init.
     * @param $token_type
     */
    private function init($token_type)
    {
        $this->token_type = $token_type;
        $full_type_string = 'jwt.'.$token_type;
        $this->header = config($full_type_string.'.header');
        $this->payload = config($full_type_string.'.payload');

        $this->public_key = config('rsa.public_key');
    }

    /**
     * 单例模式入口方法
     * @param $token_type
     * @return MyJWT|null
     * @throws \Exception
     */
    public static function getInstance($token_type)
    {
        if (empty(self::$MyJWT)) {
            self::$MyJWT = new self($token_type);
        }

        if (!in_array($token_type, (self::$MyJWT)->getTokenTypes())) {
            Log::write(__METHOD__."第".__LINE__."行"."token类型出错");
            Exception('请输入正确的token类型');
        }

        if (empty(self::$MyJWT->getTokenType())) {
            Log::write(__METHOD__."第".__LINE__."行"."token类型出错");
            Exception("这个实例对象在初始化的时候没有赋值token");
        }

        if (self::$MyJWT->getTokenType() != $token_type) {
            self::$MyJWT->init($token_type);
        }
        return self::$MyJWT;
    }

    private function getTokenType()
    {
        return $this->token_type;
    }

    /**
     * 获取支持的JWT token_type 数组
     * @return array
     */
    private function getTokenTypes()
    {
        return $this->token_types;
    }

    /**
     * JWT加密一个php object $data 返回一个JWT_token
     * @param $data
     * @return string
     * @throws Exception
     */
    public function JWTEncode($data)
    {
        if($this->header['type'] == 'access_token'){
            $time = Time::getTime();
            if (!isset($this->payload['token_timeout']) || !isset($this->payload['login_timeout'])) {
                throw new Exception("参数配置错误");
            }
            $data['iat'] = $time;
            $data['exp'] = $this->payload['token_timeout'] + intval($time);
            $data['login_exp'] = $this->payload['login_timeout'] + intval($time);
            return JWT::encode($data, $this->public_key, $this->header['alg'], $this->header['kid'], $this->header);
        }else {
            //这里仅测试用
            $this->private_key = config('rsa.private_key');
            return JWT::encode($data, $this->private_key, $this->header['alg'], $this->header['kid'], $this->header);
        }
    }

    /**
     * 解密一个JWT返回php对象
     * @param $jwt
     * @return object
     */
    public function JWTDecode($jwt)
    {
        return JWT::decode($jwt, $this->public_key, array($this->header['alg']));
    }
}
