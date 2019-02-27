<?php
/**
 * Aes对称加密解密单例封装
 * 依赖TP5config管理对象
 * User: xjyplayer
 * Date: 2018/11/23
 * Time: 20:27
 */

namespace app\common\lib\auth;

class Aes
{
    /**
     * @var null 密钥
     */
    private $key = null;
    /**
     * @var null 偏移
     */
    private $iv = null;
    /**
     * @var null
     */
    private $method = null;
    /**
     * @var mixed|null
     */
    private $padding = null;

    /**
     * 保存单例
     * @var null
     */
    private static $aes_instance = null;

    /**
     * 单例模式唯一入口
     * @return Aes|null
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (static::$aes_instance != null) {
            return self::$aes_instance;
        }
        self::$aes_instance = new self();
        return self::$aes_instance;
    }

    /**
     * Aes constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        $this->key = $this->keyParse(config("aes.keySalt"), openssl_cipher_iv_length(config("app.method")));
        $this->iv =  $this->ivParse(config("aes.ivSalt"), openssl_cipher_iv_length(config("app.method")));
        $this->method = config("aes.method");
        $this->padding = config("aes.padding");
        if (empty($this->key)||empty($this->iv)||empty($this->method)||!isset($this->padding)) {
            Exception("请配置好aes中加密相关参数");
        }
    }

    /**
     * 加密函数
     * @param $data
     * @return null|string
     * @throws \Exception
     */
    public function encrypt($data)
    {
        if (empty($data)) {
            Exception("传入加密字符串为空");
        }
        if (in_array($this->method, openssl_get_cipher_methods())) {
            $encrycode = openssl_encrypt($data, $this->method, $this->key, intval($this->padding), $this->iv);
        } else {
            Exception("请配置好aes中的密钥相关参数");
        }
        if (!empty($encrycode)) {
            $encrycode = base64_encode($encrycode);
            return $encrycode;
        }
        Exception("加密失败");
        return null;
    }


    /**
     * 解密函数
     * @param $encrycode
     * @return bool|string
     * @throws \Exception
     */
    public function decrypt($encrycode)
    {
        if (empty($encrycode)) {
            Exception("传入解密字符串为空");
        }
        $decrycode = base64_decode($encrycode);
        if (in_array($this->method, openssl_get_cipher_methods())) {
            $decrycode = openssl_decrypt($decrycode, $this->method, $this->key, intval($this->padding), $this->iv);
        } else {
            Exception("请配置好aes中的密钥相关参数");
        }
        if (!empty($encrycode)) {
            return $decrycode;
        } else {
            Exception("解密失败");
        }
        return null;
    }

    /**
     * 密钥加密盐处理
     * @param $keySalt
     * @param $length
     * @return bool|string
     */
    public static function keyParse($keySalt, $length = 16)
    {
        return substr(md5($keySalt), 0, $length);
    }

    /**
     * iv加密盐处理
     * @param $ivSalt
     * @param $length
     * @return bool|string
     */
    public static function ivParse($ivSalt, $length =16)
    {
        return substr(md5($ivSalt), 0, $length);
    }
}
