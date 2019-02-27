<?php
/**
 * 用户登录的模型类
 * User: xjyplayer
 * Date: 2019/1/11
 * Time: 20:33
 */
namespace app\common\model;

class User extends Base
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
     * 检查openid是否存在，返回user_id
     * @param $openid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkOpenID($openid)
    {
        $data = [
            'openid' => $openid,
            'status' => 1,
        ];

        $field = [
            'id',
        ];

        return $this->field($field)
            ->where($data)
            ->find();
    }

    /**
     * 根据用户id获取正常用户信息
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo($user_id){
        $data = [
            'id'        => $user_id,
            'status'    => 1
        ];

        $field = [
            'id',
            'avatar',
            'nickname',
            'signature',
            'gender',
            'location_detail',
            'dynamic_count',
            'read_count',
            'favor_count',
            'concerned_count',
            'fans_count',
            'collection_count',
        ];

        return $this->field($field)
            ->where($data)
            ->find();
    }

    /**
     * 根据查询数据是否存在
     * @param $user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUserID($user_id)
    {
        $data = [
            'id'        => $user_id,
            'status'    => 1
        ];

        $field = [
            'id',
        ];

        return $this->field($field)
            ->where($data)
            ->find();
    }

    /**
     * 根据user_id获取username
     * @param $user_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUsername($user_id)
    {
        $data = [
            'id'        => $user_id,
            'status'    => 1
        ];

        $field = [
            'id',
            'avatar',
            'nickname',
        ];

        return $this->field($field)
            ->where($data)
            ->find();
    }

    /**
     * 根据省信息获取用户信息
     * @param $order
     * @param $location_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInArea($order,$location_id,$from ,$offset)
    {
        $field = [
            'id',
            'avatar',
            'nickname',
            'signature',
            'gender',
            'location_detail',
            'dynamic_count',
            'read_count',
            'favor_count',
            'concerned_count',
            'fans_count',
            'collection_count',
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
     * 获取总数量
     * @param $location_id
     * @return float|string
     */
    public function getUserInAreaCount($location_id)
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
     * 获取搜索结果
     * @param $order
     * @param $location_id
     * @param int $from
     * @param int $size
     * @param null $search_word
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUsersSearchList($order,$location_id = null,$from = 0,$size = 5,$search_word = null)
    {
        $data['status'] = config('code.com');

        if($search_word != null){
            $data[] = ['nickname','like','%'.$search_word.'%'];
        }

        //地区相关索引
        if($location_id != null){
            //同省
            if($location_id < 100 && $location_id > 0){
                $data[] = ['location_id', '=',$location_id];

                $cidmin = $location_id * 1000;
                $cidmax = ( $location_id + 1 ) * 1000;
                $data[] = ['location_id','BETWEEN',"$cidmin,$cidmax"];

                $ridmin = $cidmin * 1000;
                $ridmax = $cidmax * 1000;
                $data[] = ['location_id','BETWEEN',"$ridmin,$ridmax"];
            }
            //同城
            elseif($location_id > 10000 && $location_id < 99999 ){
                $data[] = ['location_id' ,'=',$location_id];

                $ridmin = $location_id * 1000;
                $ridmax = ( $location_id + 1) * 1000;
                $data[] = ['location_id','between',"$ridmin,$ridmax"];
            }
            //同区县
            elseif($location_id > 10000000 && $location_id < 99999999){
                $data = ['location_id','=',$location_id];
            }
        }

        $field = [
            'id',
            'avatar',
            'nickname',
            'signature',
            'gender',
            'location_detail',
            'dynamic_count',
            'read_count',
            'favor_count',
            'concerned_count',
            'fans_count',
            'collection_count',
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->limit($from,$size)
            ->order($order)
            ->select();
    }

    /**
     * 获取搜索数量
     * @param $location_id
     * @param null $search_word
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getUsersSearchCount($location_id= null,$search_word = null)
    {
        $data['status'] = config('code.com');

        if($search_word != null){
            $data[] = ['name','like','%'.$search_word.'%'];
            $data[] = ['description','%'.$search_word.'%'];
        }

        //地区相关索引
        if($location_id != null){
            //同省
            if($location_id < 100 && $location_id > 0){
                $data[] = ['location_id', '=',$location_id];

                $cidmin = $location_id * 1000;
                $cidmax = ( $location_id + 1 ) * 1000;
                $data[] = ['location_id','BETWEEN',"$cidmin,$cidmax"];

                $ridmin = $cidmin * 1000;
                $ridmax = $cidmax * 1000;
                $data[] = ['location_id','BETWEEN',"$ridmin,$ridmax"];
            }
            //同城
            elseif($location_id > 10000 && $location_id < 99999 ){
                $data[] = ['location_id' ,'=',$location_id];

                $ridmin = $location_id * 1000;
                $ridmax = ( $location_id + 1) * 1000;
                $data[] = ['location_id','between',"$ridmin,$ridmax"];
            }
            //同区县
            elseif($location_id > 10000000 && $location_id < 99999999){
                $data = ['location_id','=',$location_id];
            }
        }

        $field = [
            'id',
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();
    }

    /**关于递增操作的封装
     * @param array|string $id
     * @param int $name
     * @param int $offset
     * @return bool
     * @throws \think\Exception
     */
    public function userInc($id ,$name,$offset = 1)
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
    public function userDec($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setDec($name,$offset);
    }

}