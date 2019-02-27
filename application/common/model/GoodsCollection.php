<?php
/**
 * 商品收藏相关
 * User: xjyplayer
 * Date: 2019/2/19
 * Time: 13:01
 */
namespace app\common\model;

class GoodsCollection extends Base
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
     * 查看是否有该对关系的收藏关系
     * @param $user_id
     * @param $goods_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkCollection($user_id,$goods_id)
    {
        $data = [
            'user_id'       => $user_id,
            'goods_id'      => $goods_id,
            'status'        => config('code.com'),
        ];

        $field = [
            'id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->find();
    }

    /**
     * 删除相关条目
     * @param $user_id
     * @param $goods_id
     * @return bool
     * @throws \Exception
     */
    public function remove($user_id,$goods_id)
    {
        $data = [
            'goods_id'            => $goods_id,
            'user_id'               => $user_id
        ];

        return $this->where($data)
            ->delete();
    }

    /**
     * 获取用户关注的商品数目
     * @param $user_id
     * @return float|string
     */
    public function getCollectedGoodsCount($user_id)
    {
        $data = [
            'user_id'       => $user_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();
    }

    /**
     * 获取指定用户的收藏的商品信息
     * @param $user_id
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCollectedGoods($user_id,$from,$size)
    {
        $data = [
            'user_id'  => $user_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'goods_id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->limit($from,$size)
            ->where($data)
            ->select();
    }

    /**
     * 获取用户关注的用户数目
     * @param $goods_id
     * @return float|string
     */
    public function getGoodsCollectionCount($goods_id)
    {
        $data = [
            'goods_id'      => $goods_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();
    }

    /**
     * 获取指定商品的收藏用户信息
     * @param $goods_id
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCollection($goods_id,$from,$size)
    {
        $data = [
            'goods_id'      => $goods_id,
            'status'        => config('code.com')
        ];

        $field = [
            'user_id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->limit($from,$size)
            ->where($data)
            ->select();
    }
}