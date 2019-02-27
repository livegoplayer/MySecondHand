<?php
/**
 * 用户动态点赞状态表模型
 * User: xjyplayer
 * Date: 2019/1/27
 * Time: 18:50
 */
namespace app\common\model;

class UserDynamicFavor extends Base
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
     * 查看是否有该对关系的点赞关系
     * @param $user_id
     * @param $dynamic_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFavor($user_id,$dynamic_id)
    {
        $data = [
            'user_id'       => $user_id,
            'dynamic_id'    => $dynamic_id,
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
     * 查询并且删除指定字段
     * @param $user_id
     * @param $dynamic_id
     * @return bool
     * @throws \Exception
     */
    public function remove($user_id,$dynamic_id)
    {
        $data = [
            'user_id'  => $user_id,
            'dynamic_id'   => $dynamic_id,
        ];

        return $this->allowField(true)
            ->where($data)
            ->delete();
    }
}