<?php
/**
 * 提供各种Aes加密的解析辅助
 * User: mortal
 * Date: 19-1-9
 * Time: 上午10:23
 */

namespace app\common\lib\auth;

abstract class Parser
{
    /**
     * 数组转url字符串
     * @param $data
     * @return string
     * @throws \Exception
     */
    public static function ArrayToStr($data)
    {
        if (!is_array($data)) {
            Exception("数组为空");
        }
        //数组排序
        ksort($data);
        //数组转字符串
        $string = http_build_query($data);
        return $string;
    }

    /**
     * 字符串转数组
     * @param $string
     * @return mixed
     * @throws \Exception
     */
    public static function strToArray($string)
    {
        if (empty($string)) {
            Exception("字符串为空");
        }
        //字符串转数组
        parse_str($string, $array);
        return $array;
    }
}
