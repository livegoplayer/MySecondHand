<?php
/**
 * 管理OssClient对象的类，单例模式
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 15:42
 */
namespace app\common\lib\upload\qiniuyun;

use app\common\lib\upload\UploadFileInfo;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\UploadManager;
use Qiniu\Zone;
use think\Exception;

class MyOssClient
{
    private static $MyOSSClient;
    /**
     * 公钥
     * @var mixed
     */
    private $access_key;
    /**
     * 密钥
     * @var
     */
    private $secret_key;
    /**
     * 存储空间
     * @var
     */
    private $bucket;
    /**
     * url
     * @var
     */
    private $url;

    private function __construct()
    {
        $this->init();
    }

    private function init(){
        //在这里配置信息
        $full_config_string         = 'qiniu.';
        $this->bucket               = config($full_config_string.'bucket');
        $this->access_key           = config($full_config_string.'ak');
        $this->secret_key           = config($full_config_string.'sk');
        $this->url                  = config($full_config_string.'url');
    }

    /**
     * 单例模式入口
     * @return MyOssClient
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (empty(self::$MyOSSClient)) {
            self::$MyOSSClient = new self();
        }

        if (self::$MyOSSClient->checkConfig() == false){
            throw new Exception('请完善参数配置文件');
        }

        return self::$MyOSSClient;
    }

    /**
     * 检查配置信息
     * @return bool
     */
    private function checkConfig()
    {
        if(empty($this->bucket)|| empty($this->access_key) || empty($this->bucket) || empty($this->secret_key) || empty($this->url)){
            return false;
        }
        return true;
    }

    /**
     * 上传图片,返回图片url
     * @return null
     * @throws Exception
     */
    public function uploadFile()
    {
        //获取随机文件名
        $file_path      = UploadFileInfo::getFilePath();
        $new_file_name  = UploadFileInfo::getFilename();
        //1. 定义鉴权类
        $auth = new Auth($this->access_key, $this->secret_key);
        //2. 生成上传token(上传凭证),绑定上传空间
        $token = $auth->uploadToken($this->bucket);
        //3. 生成上传管理对象，进行上传处理动作
        //这两句在命名空间正确填写的时候不需要，但是错误填写的时候需要用来调试，最好加上
        $zone = Zone::zone0();
        $config = new Config($zone);
        $uploadMgr = new UploadManager($config);
        //定义保存的文件名，尽量不要重复
        $key = $new_file_name;
        try{
            $res = $uploadMgr->putFile($token,$key,$file_path);
        }catch(\Exception $e){
            throw new Exception($e->getFile().$e->getLine().$e->getMessage());
        }

        //4. 处理返回结果
        $img_url = null;
        if(!empty($res[0]["key"])) {
            $img_url = config('qiniu.url') . '//' . $res[0]["key"];
        }
        return $img_url;
    }

    /**
     * 客户端上传专用
     * @return string
     */
    public function getToken()
    {
        // 构建鉴权对象
        $auth = new Auth($this->access_key, $this->secret_key);

        // 生成上传Token
        $token = $auth->uploadToken($this->bucket);
        return $token;
    }

}