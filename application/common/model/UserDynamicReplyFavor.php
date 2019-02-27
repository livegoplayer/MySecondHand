<?php
/**
 * 用户评论回复点赞
 * User: xjyplayer
 * Date: 2019/1/28
 * Time: 18:25
 */
namespace app\common\model;

class UserDynamicReplyFavor extends Base
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
     * 根据用户id和评论回复的id查看是否存在该点赞关系
     * @param $user_id
     * @param $reply_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserFavorStatus($user_id,$reply_id)
    {
        $data = [
            'user_id'   => $user_id,
            'reply_id'  => $reply_id,
            'status'    => config('code.com'),
        ];

        $field = [
            'id'
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }

    /**
     * 查看是否有该对关系的点赞关系
     * @param $user_id
     * @param $reply_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFavor($user_id,$reply_id)
    {
        $data = [
            'user_id'   => $user_id,
            'reply_id'  => $reply_id,
            'status'    => config('code.com'),
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
     * @param $reply_id
     * @return bool
     * @throws \Exception
     */
    public function remove($user_id,$reply_id)
    {
        $data = [
            'user_id'       => $user_id,
            'reply_id'      => $reply_id,
        ];

        return $this->allowField(true)
            ->where($data)
            ->delete();
    }
}
