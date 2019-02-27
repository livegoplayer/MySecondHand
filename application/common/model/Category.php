<?php
/**
 * 商品类别相关
 * User: xjyplayer
 * Date: 2019/2/21
 * Time: 13:41
 */

namespace app\common\model;

namespace app\common\model;

class Category extends Base
{
    /**
     * 根据需要重写的add方法
     * @param $data
     * @return mixed
     */
    public function add($data){
        $data['status'] = config('code.com');               //直接正常状态
        $this->allowField(true)->save($data);
        return $this->id;
        //返回id
    }

    /**
     * 获取第一级类别
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFirstCategory()
    {
        $data = [
            'parent_id'     => 0,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'name',
        ];

        return $this->field($field)
            ->where($data)
            ->select();
    }

    /**
     * 获取子类别类别
     * @param $parent_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategory($parent_id)
    {
        $data = [
            'parent_id'     => $parent_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'name',
        ];

        return $this->field($field)
            ->where($data)
            ->select();
    }
}