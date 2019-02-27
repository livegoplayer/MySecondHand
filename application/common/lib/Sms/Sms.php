<?php
/**
 * 发送短信
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 20:28
 */
namespace app\common\lib\Sms;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use think\facade\Cache;
use think\facade\Log;

class Sms
{
    const LOG_TPL = "alisms:";
    /**
     * 静态变量保存全局的实例
     * @var null
     */
    private static $_instance = null;

    /**
     * 私有的构造方法
     */
    private function __construct() {

    }

    static $acsClient = null;

    /**
     * 静态方法 单例模式统一入口
     */
    public static function getInstance() {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 取得AcsClient,单例写法
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        $product = "Dysmsapi";
        $domain = "dysmsapi.aliyuncs.com";
        $accessKeyId = config("alisms.accessKeyId"); // AccessKeyId
        $accessKeySecret = config("alisms.accessKeySecret");
        $region = "cn-hangzhou";
        $endPointName = "cn-hangzhou";
        if(static::$acsClient == null) {
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 设置短信验证
     * @param int $phone
     * @return bool
     */
    public function sendSms($phone = 0) {
        //生成验证码随机数
        $code = rand(100000, 999999);

        try {
            $request = new SendSmsRequest();
            $request->setPhoneNumbers($phone);
            $request->setSignName(config("alisms.signName"));
            $request->setTemplateCode(config("alisms.templateCode"));
            $request->setTemplateParam(json_encode(array(
                "code" => $code,
                "product" => "dsd"
            ), JSON_UNESCAPED_UNICODE));
            $request->setSmsUpExtendCode("1234567");
            $acsResponse = static::getAcsClient()->getAcsResponse($request);
            // 记录日志
            Log::write(self::LOG_TPL."set-----".json_encode($acsResponse));
            // halt($acsResponse);
            // return $acsResponse;
        }catch (\Exception $e) {
            // 记录日志
            echo $e->getMessage();
            // Log::write(self::LOG_TPL."set-----".json_encode($e->getMessage()));
            return false;
        }
        // halt($acsResponse);
        if($acsResponse->Message == "OK") {
            // 设置验证码失效时间
            Cache::set($phone, $code, config("alisms.identify_time"));
            return true;
        }else {
            Cache::set("18868200385", $code, json_encode($acsResponse->Code));
            // Log::write(self::LOG_TPL."set-----111" . json_encode($acsResponse));
            return false;
        }
    }

    /**
     * 根据手机号码查询验证码是否正常
     * @param int $phone
     * @return bool|mixed
     */
    public function checkSmsIdentify($phone = 0) {
        if(!$phone) {
            return false;
        }
        return Cache::get($phone);
    }
}