<?php
/**
 * 用户动态评论表
 * User: xjyplayer
 * Date: 2019/1/27
 * Time: 19:13
 */
namespace app\common\model;

class UserDynamicReply extends Base
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
     * 根据动态id获取第一级用户回复
     * @param $dynamic_id
     * @param $limit
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDynamicReply($dynamic_id,$limit)
    {
        $data = [
            'dynamic_id'    => $dynamic_id,
            'reply_type'    => config('code.reply_to_main'),
            'status'        => config('code.com'),
        ];

        $field = [
            'id',
            'user_id',
            'content',
            'favor_count',
        ];

        $order = [
            'favor_count'   => 'desc',
            'create_time'   => 'asc',
        ];

        return $this->where($data)
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select();
    }

    /**
     * 根据动态id获取非第一级用户回复
     * @param $dynamic_id
     * @param $reply_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDynamicReplyReply($dynamic_id,$reply_id)
    {
        $data = [
            'dynamic_id'    => $dynamic_id,
            'reply_type'    => config('code.reply_to_reply'),
            'reply_id'      => $reply_id,
            'status'        => config('code.com'),
        ];

        $field = [
            'id',
            'user_id',
            'reply_id',
            'content',
            'favor_count',
        ];

        $order = [
            'create_time'   => 'asc',
        ];

        return $this->where($data)
            ->field($field)
            ->order($order)
            ->select();
    }

    /**
     * 根据id获取user_id
     * @param $reply_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserID($reply_id){
        $data = [
            'id'            => $reply_id,
            'status'        => config('code.com'),
        ];

        $field = [
            'user_id',
        ];

        return $this->where($data)
            ->field($field)
            ->find();
    }

    /**
     * 删除id相关条目
     * @param $reply_id
     * @return bool
     * @throws \Exception
     */
    public function remove($reply_id)
    {
        $data = [
            'id'            => $reply_id,
        ];
        return $this->where($data)
            ->delete();
    }

    /**关于递增操作的封装
     * @param array|string $id
     * @param int $name
     * @param int $offset
     * @return bool
     * @throws \think\Exception
     */
    public function userDynamicReplyInc($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setInc($name,$offset);
    }

    /**关于递减操作的封装
     * @param array|string $id  id
     * @param int $name         字段名
     * @param int $offset       增加或者减少值
     * @return bool
     * @throws \think\Exception
     */
    public function userDynamicReplyDec($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setDec($name,$offset);
    }
}