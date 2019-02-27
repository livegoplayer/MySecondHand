<?php
/**
 * aliyun上传图片.
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 14:42
 */
namespace app\common\lib\upload\aliyun;

use think\Exception;

class UploadImage
{
    public function uploadImage()
    {
        try{
            $url = MyOssClient::getInstance(true)->uploadFile();
        }catch(\Exception $e){
            throw new Exception('上传图片出错');
        }
        return $url;
    }
}