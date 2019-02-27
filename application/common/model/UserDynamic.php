<?php
/**
 * 用户动态表模型
 * User: xjyplayer
 * Date: 2019/1/25
 * Time: 13:49
 */

namespace app\common\model;

class UserDynamic extends Base
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
     * 根据user_id 获取所有动态，时间倒序
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDynamic($user_id,$from,$size){
        $data = [
            'user_id' => $user_id,
            'status'  => 1,
        ];

        $field = [
            'id',
            'user_id',
            'reply_count',
            'image_count',
            'favor_count',
            'content',
            'on_top',
            'create_time',
            'update_time'
        ];

        $order = [
            'on_top'        => 'desc',
            'create_time'   => 'desc',
        ];

        return $this->allowField(true)
            ->field($field)
            ->order($order)
            ->where($data)
            ->limit($from,$size)
            ->select();
    }

    /**
     * 获取某用户的动态条数
     * @param $user_id
     * @return float|string
     */
    public function  getUserDynamicCount($user_id)
    {
        $data = [
            'user_id' => $user_id,
            'status'  => 1,
        ];

        $field = [
            'id',
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();
    }

    /**
     * 获取指定条数的分页用户动态，指定特殊的排序方式
     * @param $order
     * @param $from
     * @param $size
     * @param int $limit_count
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDynamicList($order,$from,$size,$limit_count = 50)
    {
        $data = [
            'status'  => 1,
        ];

        $field = [
            'id',
            'user_id',
            'reply_count',
            'image_count',
            'favor_count',
            'content',
            'on_top',
            'create_time',
            'update_time'
        ];

        //改变限制值
        if($from + $size > $limit_count){
            if($from >= $limit_count){
                return false;
            }else{
                $size = $limit_count - $from;
            }
        }

        return $this->allowField(true)
            ->field($field)
            ->order($order)
            ->where($data)
            ->limit($from,$size)
            ->select();
    }

    /**
     * 获取动态总数，如果小于limit_count则返回，否则返回limit_count
     * @param $limit_count
     * @return float|string
     */
    public function getDynamicListCount($limit_count = 50)
    {
        $data = [
            'status'  => 1,
        ];

        $field = [
            'id',
        ];

        $count = $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();

        return $count > $limit_count ? $limit_count : $count;
    }

    /**
     * 获取某地区的动态
     * @param $order
     * @param $location_id
     * @param $from
     * @param $offset
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLocationUserDynamic($order,$location_id,$from,$offset)
    {
        $field = [
            'id',
            'user_id',
            'reply_count',
            'image_count',
            'favor_count',
            'content',
            'on_top',
            'create_time',
            'update_time'
        ];

        //同省
        if($location_id < 100 && $location_id > 0){
            $pdata = [
                'location_id' => $location_id
            ];

            $cidmin = $location_id * 1000;
            $cidmax = ( $location_id + 1 ) * 1000;
            $cdata = [
                ['location_id','BETWEEN',"$cidmin,$cidmax"],
            ];

            $ridmin = $cidmin * 1000;
            $ridmax = $cidmax * 1000;
            $rdata = [
                ['location_id','BETWEEN',"$ridmin,$ridmax"],
            ];

            $res = $this->field($field)
                ->where($pdata)
                ->whereOr($cdata)
                ->whereOr($rdata)
                ->order($order)
                ->limit($from,$offset)
                ->select();
        }
        //同城
        elseif($location_id > 10000 && $location_id < 99999 ){
            $cdata = [
                'location_id' => $location_id
            ];

            $ridmin = $location_id * 1000;
            $ridmax = ( $location_id + 1) * 1000;
            $rdata = [
                ['location_id','between',"$ridmin,$ridmax"]
            ];

            $res = $this->field($field)
                ->where($cdata)
                ->whereOr($rdata)
                ->order($order)
                ->limit($from,$offset)
                ->select();
        }
        //同区县
        elseif($location_id > 10000000 && $location_id < 99999999){
            $rdata = [
                'location_id' => $location_id
            ];

            $res = $this->field($field)
                ->where($rdata)
                ->order($order)
                ->limit($from,$offset)
                ->select();
        }

//        dump($this->getLastSql());
        return  $res;
    }

    /**
     * 获取某地区的动态数量
     * @param $location_id
     * @return float|string
     */
    public function getLocationUserDynamicCount($location_id)
    {
        $field = [
            'id',
        ];

        //同省
        if($location_id < 100 && $location_id > 0){
            $pdata = [
                'location_id' => $location_id
            ];

            $cidmin = $location_id * 1000;
            $cidmax = ( $location_id + 1 ) * 1000;
            $cdata = [
                ['location_id','BETWEEN',"$cidmin,$cidmax"],
            ];

            $ridmin = $cidmin * 1000;
            $ridmax = $cidmax * 1000;
            $rdata = [
                ['location_id','BETWEEN',"$ridmin,$ridmax"],
            ];

            $res = $this->field($field)
                ->where($pdata)
                ->whereOr($cdata)
                ->whereOr($rdata)
                ->count();
        }
        //同城
        elseif($location_id > 10000 && $location_id < 99999 ){
            $cdata = [
                'location_id' => $location_id
            ];

            $ridmin = $location_id * 1000;
            $ridmax = ( $location_id + 1) * 1000;
            $rdata = [
                ['location_id','between',"$ridmin,$ridmax"]
            ];

            $res = $this->field($field)
                ->where($cdata)
                ->whereOr($rdata)
                ->count();
        }
        //同区县
        elseif($location_id > 10000000 && $location_id < 99999999){
            $rdata = [
                'location_id' => $location_id
            ];

            $res = $this->field($field)
                ->where($rdata)
                ->count();
        }

//        dump($this->getLastSql());
        return  $res;
    }

    /**
     * 删除id相关条目
     * @param $user_dynamic
     * @return bool
     * @throws \Exception
     */
    public function remove($user_dynamic)
    {
        $data = [
            'id'            => $user_dynamic,
        ];
        return $this->where($data)
            ->delete();
    }

    /**
     * 关于递增操作的封装
     * @param array|string $id
     * @param int $name
     * @param int $offset
     * @return bool
     * @throws \think\Exception
     */
    public function userDynamicInc($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setInc($name,$offset);
    }

    /**
     * 关于递减操作的封装
     * @param array|string $id  id
     * @param int $name         字段名
     * @param int $offset       增加或者减少值
     * @return bool
     * @throws \think\Exception
     */
    public function userDynamicDec($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setDec($name,$offset);
    }


}