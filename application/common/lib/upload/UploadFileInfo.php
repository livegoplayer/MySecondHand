<?php
/**
 * 获取upload信息
 * User: xjyplayer
 * Date: 2019/2/26
 * Time: 17:18
 */

namespace app\common\lib\upload;

use think\Exception;
use think\Request;

abstract class UploadFileInfo
{
    private static $file_name_pre;
    private static $ext;
    private static $filename;
    private static $tmp_path;

    public static function init()
    {
        //验证传递方式
        if(empty($_FILES['file']['tmp_name'])){
            throw new Exception("你还未上传文件");
        }
        //得到临时文件路径
        self::$tmp_path = $_FILES['file']['tmp_name'];
        //得到原文件名
        self::$file_name_pre = $_FILES['file']['name'];
        //得到扩展名
        $array = explode('.',self::$file_name_pre);
        self::$ext = end($array);
        //定义保存的文件名
        self::$filename = date("Y").date("m").substr(md5(self::$tmp_path),0,5).date('YmdHis').mt_rand(10000,99999).".".self::$ext;
    }

    /**
     * 获得随机文件名
     * @return mixed
     * @throws Exception
     */
    public static function getFilename()
    {
        if(empty(self::$filename)) {
            self::init();
        }
        return self::$filename;
    }

    /**
     * 获得文件路径
     * @return mixed
     * @throws Exception
     */
    public static function getFilePath()
    {
        if(empty(self::$tmp_path)) {
            self::init();
        }
        return self::$filename;
    }

    /**
     * 本地存储
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public static function save(Request $request)
    {
        $file = $request -> file('file');
        $path = 'upload';
        $info = $file -> move($path);
        if($info){
            return '//'.$info->getPathname();
        }else{
            throw new Exception('文件保存出错');
        }
    }
}