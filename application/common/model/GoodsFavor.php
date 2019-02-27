<?php
/**
 * 商品点赞模型
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 21:38
 */
namespace app\common\model;

class GoodsFavor extends Base
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
     * 查看该商品是否被当前用户点赞
     * @param $goods_id
     * @param $user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFavor($goods_id,$user_id)
    {
        $data = [
            'goods_id'      => $goods_id,
            'user_id'       => $user_id,
            'status'        => config('code.com'),
        ];

        $field = [
            'id'
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }

    /**
     * 查询并且删除指定字段
     * @param $user_id
     * @param $goods_id
     * @return bool
     * @throws \Exception
     */
    public function remove($user_id,$goods_id)
    {
        $data = [
            'user_id'       => $user_id,
            'goods_id'      => $goods_id,
        ];

        return $this->allowField(true)
            ->where($data)
            ->delete();
    }
}