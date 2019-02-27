<?php
/**
 * 用户点赞模型
 * User: xjyplayer
 * Date: 2019/1/23
 * Time: 16:26
 */
namespace app\common\model;

class UserFavor extends Base
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
     * 查询并且删除指定字段
     * @param $from_user_id
     * @param $to_user_id
     * @return bool
     * @throws \Exception
     */
    public function remove($from_user_id,$to_user_id)
    {
        $data = [
            'from_user_id'  => $from_user_id,
            'to_user_id'    => $to_user_id,
        ];

        return $this->allowField(true)
            ->where($data)
            ->delete();
    }

    /**
     * 查看是否有该对关系的点赞关系
     * @param $from_user_id
     * @param $to_user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFavor($from_user_id,$to_user_id)
    {
        $data = [
            'from_user_id'  => $from_user_id,
            'to_user_id'    => $to_user_id,
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



}