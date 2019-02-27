<?php
/**
 * 城市的区域模型
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 14:50
 */

namespace app\common\model;

use think\Model;

class Region extends Model
{

    /**
     * 获取某城市的所有区域
     * @param $cid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegion($cid)
    {
        $data = [
            'cid'   => $cid
        ];

        $field = [
            'rid',
            'rname'
        ];

        return $this->where($data)
            ->field($field)
            ->select();
    }

    /**
     * 根据城市名获取区域信息
     * @param $cname
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegionByName($cname)
    {
        $data = [
            'cname'   => $cname
        ];

        $field = [
            'rid',
            'rname'
        ];

        return $this->where($data)
            ->field($field)
            ->select();
    }

    /**
     * 根据rid获取区县信息
     * @param $rid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegionInfo($rid)
    {
        $data = [
            'rid'   => $rid
        ];

        $field = [
            'rid',
            'cid',
            'pname',
            'cname',
            'rname'
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }

}