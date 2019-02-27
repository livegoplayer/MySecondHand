<?php
/**
 * 商品评论相关
 * User: xjyplayer
 * Date: 2019/2/19
 * Time: 13:01
 */
namespace app\common\model;

class GoodsComments extends Base
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
     * 根据商品id获取第一级用户评论
     * @param $goods_id
     * @param $comment_count
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsComments($goods_id,$comment_count)
    {
        $data = [
            'goods_id'    => $goods_id,
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
            ->limit($comment_count)
            ->select();
    }

    /**
     * 根据商品id获取非第一级用户回复
     * @param $goods_id
     * @param $comment_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCommentsReply($goods_id,$comment_id)
    {
        $data = [
            'goods_id'      => $goods_id,
            'reply_type'    => config('code.reply_to_reply'),
            'reply_id'      => $comment_id,
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
     * @param $comment_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserID($comment_id){
        $data = [
            'id'            => $comment_id,
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
     * @param $comment_id
     * @return bool
     * @throws \Exception
     */
    public function remove($comment_id)
    {
        $data = [
            'id'            => $comment_id,
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
    public function goodsCommentsInc($id ,$name,$offset = 1)
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
    public function goodsCommentsDec($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setDec($name,$offset);
    }
}