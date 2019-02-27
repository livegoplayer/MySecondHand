<?php
/**
 * 商品图片模型
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 21:15
 */

namespace app\common\model;

class GoodsImages extends Base
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
     * 根据商品id获取商品图片信息
     * @param $goods_id
     * @param $image_count
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsImages($goods_id,$image_count)
    {
        $data = [
            'goods_id'      => $goods_id,
            'status'        => config('code.com'),
        ];

        $field = [
            'id',
            'image_order',
            'url',
        ];

        $order = [
            'image_order'   => 'asc',
            'create_time'   => 'desc'
        ];

        return $this->field($field)
            ->limit($image_count)
            ->order($order)
            ->where($data)
            ->select();
    }
}