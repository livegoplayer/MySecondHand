<?php
/**
 * 省信息模型
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 14:29
 */
namespace app\common\model;

use think\Model;

class Province extends Model
{
    /**
     * 获取所有的省信息
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProvince()
    {
        $field = [
            'pid',
            'pname'
        ];

        return $this->field($field)
            ->select();
    }

    /**
     * 获取特定pid的省信息
     * @param $pid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProvinceInfo($pid)
    {
        $data = [
            'pid' => $pid
        ];

        $field = [
            'pid',
            'pname'
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }
}