<?php
/**
 * 商品评论点赞相关
 * User: xjyplayer
 * Date: 2019/2/19
 * Time: 13:39
 */

namespace app\common\model;


class GoodsCommentsFavor extends Base
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
     * @param $comment_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFavor($user_id,$comment_id)
    {
        $data = [
            'user_id'   => $user_id,
            'comment_id'  => $comment_id,
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
     * 删除相关条目
     * @param $user_id
     * @param $comment_id
     * @return bool
     * @throws \Exception
     */
    public function remove($user_id,$comment_id)
    {
        $data = [
            'comment_id'            => $comment_id,
            'user_id'               => $user_id
        ];

        return $this->where($data)
            ->delete();
    }
}