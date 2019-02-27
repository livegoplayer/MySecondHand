<?php
/**
 * qiniuyu上传图片.
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 14:42
 */
namespace app\common\lib\upload\qiniuyun;

use think\Exception;

class UploadImage
{
    public function uploadImage()
    {
        try{
            $url = MyOssClient::getInstance()->uploadFile();
        }catch(\Exception $e){
            throw new Exception('上传图片出错');
        }
        return $url;
    }
}