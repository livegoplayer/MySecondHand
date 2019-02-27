<?php
/**
 * 用户动态图片模型
 * User: xjyplayer
 * Date: 2019/1/27
 * Time: 18:03
 */
namespace app\common\model;

class UserDynamicImages extends Base
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
     * 根据动态id获取指定数量的动态图片
     * @param $dynamic_id
     * @param $limit
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDynamicImages($dynamic_id,$limit)
    {
        $data = [
            'dynamic_id'    => $dynamic_id,
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
            ->limit($limit)
            ->order($order)
            ->where($data)
            ->select();
    }

}