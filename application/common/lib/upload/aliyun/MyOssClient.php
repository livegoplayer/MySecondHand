<?php
/**
 * 管理OssClient对象的类，单例模式
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 15:42
 */
namespace app\common\lib\upload\aliyun;

use app\common\lib\upload\UploadFileInfo;
use OSS\OssClient;
use think\Exception;

class MyOssClient
{
    private static $MyOSSClient;
    /**
     * 访问域名，分内网访问和外网访问
     * @var mixed
     */
    private $endpoint;
    /**
     * 访问地域
     * @var
     */
    private $region;
    /**
     * 存储空间
     * @var
     */
    private $bucket;
    /**
     * 以下是访问密钥
     * @var
     */
    private $access_key_id;
    private $access_key_secret;
    /**
     * url前缀
     * @var
     */
    private $url_pre;

    private function __construct($isInternal = false)
    {
        $full_config_string         = 'aliyun.oss.image.';
        $this->endpoint             = $isInternal ? 'https://'.config($full_config_string.'outer_endpoint') : 'https://'.config($full_config_string.'inner_endpoint');
        $this->init();
    }

    private function init(){
        //在这里配置信息
        $full_config_string         = 'aliyun.oss.image.';
        $this->region               = config($full_config_string.'region');
        $this->bucket               = config($full_config_string.'bucket');
        $this->access_key_id        = config($full_config_string.'access_key_id');
        $this->access_key_secret    = config($full_config_string.'access_key_secret');
        $this->url_pre    = 'https://'.$this->bucket.'.'.$this->endpoint.'/';
    }

    /**
     * 单例模式入口
     * @param bool $isInternal
     * @return MyOssClient
     * @throws \Exception
     */
    public static function getInstance($isInternal = false)
    {
        if (empty(self::$MyOSSClient)) {
            self::$MyOSSClient = new self($isInternal);
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
    public function checkConfig()
    {
        if(empty($this->endpoint)|| empty($this->region) || empty($this->bucket) || empty($this->access_key_secret) || empty($this->access_key_id)){
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
        //建立连接对象
        try{
            $ossClient = new OssClient($this->access_key_id, $this->access_key_secret, $this->endpoint, false);
        }catch(\Exception $e){
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        //获取随机文件名
        $file_path      = UploadFileInfo::getFilePath();
        $new_file_name  = UploadFileInfo::getFilename();
        try{
            $res = $ossClient->uploadFile($this->bucket, $new_file_name, $file_path);
            if (!$res) {
                throw new Exception('文件上传出错');
            }
        }catch(\Exception $e){
            throw new Exception('文件上传出错');
        }

        $url = $this->url_pre.$new_file_name;
        return $url;
    }

    /**
     * 修改endpoint，并且提供链式操作
     * @param bool $isInternal
     * @return $this
     */
    public function setInternal($isInternal = false)
    {
        $full_config_string         = 'aliyun.oss.image.';
        $this->endpoint             = $isInternal ? config($full_config_string.'outer_endpoint') : config($full_config_string.'inner_endpoint');
        return $this;
    }

    /**
     * 返回access_key_id
     * @return mixed
     */
    public function getAccessKeyID()
    {
        return $this->access_key_id;
    }

    /**
     * 返回access_key_secret
     * @return mixed
     */
    public function getAccessKeySecret()
    {
        return $this->access_key_secret;
    }
}