<?php
/**
 * 获取所有的市信息
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 14:41
 */
namespace app\common\model;

use think\Model;

class City extends Model
{
    /**
     * 获取特定省的城市信息
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCity($pid)
    {
        $data = [
            'pid'   => $pid
        ];

        $field = [
            'cid',
            'cname'
        ];

        return $this->where($data)
            ->field($field)
            ->select();
    }

    /**
     * 根据省名称获取city
     * @param $pname
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCityByName($pname)
    {
        $data = [
            'pname'  => $pname,
        ];

        $field = [
            'cid',
            'cname'
        ];

        return $this->where($data)
            ->field($field)
            ->select();
    }

    /**
     * 根据cid获取城市信息
     * @param $cid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCityInfo($cid)
    {
        $data = [
            'cid'   => $cid
        ];

        $field = [
            'cid',
            'cname',
            'pid',
            'pname',
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }

}