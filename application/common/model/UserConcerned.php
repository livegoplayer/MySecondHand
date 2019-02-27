<?php
/**
 * 用户关注表模型
 * User: xjyplayer
 * Date: 2019/1/22
 * Time: 12:51
 */

namespace app\common\model;

class UserConcerned extends Base
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
            'to_user_id'   => $to_user_id,
        ];

        return $this->allowField(true)
            ->where($data)
            ->delete();
    }

    /**
     * 查看是否有该对关系的关注关系
     * @param $from_user_id
     * @param $to_user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkConcern($from_user_id,$to_user_id)
    {
        $data = [
            'from_user_id'  => $from_user_id,
            'to_user_id'    => $to_user_id,
            'status'        => config('code.com')
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
     * 获取用户关注的用户数目
     * @param $user_id
     * @return float|string
     */
    public function getConcernedUserCount($user_id)
    {
        $data = [
            'from_user_id'  => $user_id,
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
     * 获取指定用户的关注用户信息
     * @param $user_id
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getConcernedUser($user_id,$from,$size)
    {
        $data = [
            'from_user_id'  => $user_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'to_user_id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->limit($from,$size)
            ->where($data)
            ->select();
    }

    /**
     * 获取用户关注的用户数目
     * @param $user_id
     * @return float|string
     */
    public function getConcernUserCount($user_id)
    {
        $data = [
            'to_user_id'    => $user_id,
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
     * 获取指定用户的关注用户信息
     * @param $user_id
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getConcernUser($user_id,$from,$size)
    {
        $data = [
            'to_user_id'    => $user_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'from_user_id'
        ];

        return $this->allowField(true)
            ->field($field)
            ->limit($from,$size)
            ->where($data)
            ->select();
    }

}