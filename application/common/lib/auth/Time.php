<?php
/**
 * 得到特殊的时间戳.
 * User: xjyplayer
 * Date: 2018/11/24
 * Time: 17:03
 */

namespace app\common\lib\auth;


class Time
{
    /**
     * 返回指定位数的时间戳
     * @param int $length 不传或者传递小于等于10 的数字返回time() ,大于10 则返回time()+ 对应位数的毫秒数
     * @return string
     */
    public static function getTime($length = 10)
    {
        list($microTime,$time) = explode(" ",microtime());
        if($length>10){
            return $time.explode(".",$microTime*pow(10,$length-10))[0];
        }else{
            return $time;
        }
    }


    /**
     * 是否过时
     * @param $time
     * @param int $timeout
     * @param int $offset
     * @return bool|int|string
     * @throws \Exception
     */
    public static function isTimeOut($time , $timeout = 100, $offset =0){
        if(strlen($time) >= 10){
            $time = substr($time,0,10);
        }elseif (strlen($time) < 10){
            exception("请传入标准时间戳");
        }
        return (time() + $offset - $time > $timeout) ? true : false ;
    }
}
