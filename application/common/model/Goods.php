<?php
/**
 * 商品相关模型
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 21:10
 */

namespace app\common\model;

namespace app\common\model;

class Goods extends Base
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
     * 根据user_id 获取所有商品，时间倒序
     * @param $user_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserGoods($user_id,$from,$size){
        $data = [
            'user_id' => $user_id,
            'status'  => config('code.com'),
        ];

        $field = [
            'id',
            'user_id',
            'name',
            'price',
            'description',
            'location_detail',
            'comment_count',
            'image_count',
            'favor_count',
            'collection_count',
            'hot_degree',
            'create_time',
        ];

        $order = [
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
     * 获取某用户的商品条数
     * @param $user_id
     * @return float|string
     */
    public function getUserGoodsCount($user_id)
    {
        $data = [
            'user_id' => $user_id,
            'status'  => config('code.com'),
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
     * 获取所有商品，时间倒序
     * @param $order
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllGoods($order,$from,$size){
        $data = [
            'status'  => config('code.com'),
        ];

        $field = [
            'id',
            'user_id',
            'name',
            'price',
            'description',
            'location_detail',
            'comment_count',
            'image_count',
            'favor_count',
            'collection_count',
            'hot_degree',
            'create_time',
        ];

        return $this->allowField(true)
            ->field($field)
            ->order($order)
            ->where($data)
            ->limit($from,$size)
            ->select();
    }

    /**
     * 获取所有商品条数
     * @return float|string
     */
    public function getAllGoodsCount()
    {
        $data = [
            'status'  => config('code.com'),
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
     * 获取某地区的商品
     * @param $order
     * @param $location_id
     * @param $from
     * @param $offset
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLocationGoods($order,$location_id,$from,$offset)
    {
        $field = [
            'id',
            'user_id',
            'name',
            'price',
            'description',
            'location_detail',
            'comment_count',
            'image_count',
            'favor_count',
            'collection_count',
            'hot_degree',
            'create_time',
        ];

        //同省
        if($location_id < 100 && $location_id > 0){
            $pdata = [
                'location_id'   => $location_id,
                'status'        => config('code.com')
            ];

            $cidmin = $location_id * 1000;
            $cidmax = ( $location_id + 1 ) * 1000;
            $cdata = [
                ['location_id','BETWEEN',"$cidmin,$cidmax"],
                'status'        => config('code.com')
            ];

            $ridmin = $cidmin * 1000;
            $ridmax = $cidmax * 1000;
            $rdata = [
                ['location_id','BETWEEN',"$ridmin,$ridmax"],
                'status'        => config('code.com')
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
                'location_id' => $location_id,
                'status'        => config('code.com')
            ];

            $ridmin = $location_id * 1000;
            $ridmax = ( $location_id + 1) * 1000;
            $rdata = [
                ['location_id','between',"$ridmin,$ridmax"],
                'status'        => config('code.com')
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
                'location_id' => $location_id,
                'status'        => config('code.com')
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
     * 获取某地区的商品数量
     * @param $location_id
     * @return float|string
     */
    public function getLocationGoodsCount($location_id)
    {
        $field = [
            'id',
        ];

        //同省
        if($location_id < 100 && $location_id > 0){
            $pdata = [
                'location_id' => $location_id,
                'status'        => config('code.com')
            ];

            $cidmin = $location_id * 1000;
            $cidmax = ( $location_id + 1 ) * 1000;
            $cdata = [
                ['location_id','BETWEEN',"$cidmin,$cidmax"],
                'status'        => config('code.com')
            ];

            $ridmin = $cidmin * 1000;
            $ridmax = $cidmax * 1000;
            $rdata = [
                ['location_id','BETWEEN',"$ridmin,$ridmax"],
                'status'        => config('code.com')
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
                'location_id'   => $location_id,
                'status'        => config('code.com')
            ];

            $ridmin = $location_id * 1000;
            $ridmax = ( $location_id + 1) * 1000;
            $rdata = [
                ['location_id','between',"$ridmin,$ridmax"],
                'status'        => config('code.com')
            ];

            $res = $this->field($field)
                ->where($cdata)
                ->whereOr($rdata)
                ->count();
        }
        //同区县
        elseif($location_id > 10000000 && $location_id < 99999999){
            $rdata = [
                'location_id' => $location_id,
                'status'        => config('code.com')
            ];

            $res = $this->field($field)
                ->where($rdata)
                ->count();
        }

//        dump($this->getLastSql());
        return  $res;
    }

    /**
     * 获取某类别的所有商品信息
     * @param $order
     * @param $category_id
     * @param $from
     * @param $size
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryGoodsList($order,$category_id,$from,$size)
    {
        $data = [
            'category_id'   => $category_id,
            'status'        => config('code.com')
        ];

        $field = [
            'id',
            'user_id',
            'name',
            'price',
            'description',
            'location_detail',
            'comment_count',
            'image_count',
            'favor_count',
            'collection_count',
            'hot_degree',
            'create_time',
        ];

        return $this->allowField(true)
            ->field($field)
            ->order($order)
            ->where($data)
            ->limit($from,$size)
            ->select();

    }

    /**
     * 获取某类别的所有商品数量
     * @param $category_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getCategoryGoodsCount($category_id)
    {
        $data = [
            'category_id'   => $category_id,
            'status'        => config('code.com')
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
     * 删除id相关条目
     * @param $goods_id
     * @return bool
     * @throws \Exception
     */
    public function remove($goods_id)
    {
        $data = [
            'id'            => $goods_id,
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
    public function goodsInc($id ,$name,$offset = 1)
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
    public function goodsDec($id ,$name,$offset = 1)
    {
        return $this->where(["id" => $id])->setDec($name,$offset);
    }

    /**
     * 获取搜索结果
     * @param $order
     * @param $location_id
     * @param $category_id
     * @param int $from
     * @param int $size
     * @param null $search_word
     * @param null $price_max
     * @param null $price_min
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsSearchList($order,$location_id = null,$category_id = null,$from = 0,$size = 5,$search_word = null,$price_max = null,$price_min = null)
    {
        $data['status'] = config('code.com');

        if($search_word != null){
            $data[] = ['name','like','%'.$search_word.'%'];
            $data[] = ['description','%'.$search_word.'%'];
        }
        if($price_max != null && $price_min != null){
            $data[] = ['price','BETWEEN',"$price_min,$price_max"];
        }elseif($price_max != null){
            $data[] = ['price','<=',$price_max];
        }elseif($price_min != null){
            $data[] = ['price','>=',$price_min];
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

        //类别索引
        if ($category_id != null){
            $data['category_id'] = [$category_id];
        }

        $field = [
            'id',
            'user_id',
            'name',
            'price',
            'description',
            'location_detail',
            'comment_count',
            'image_count',
            'favor_count',
            'collection_count',
            'hot_degree',
            'create_time',
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
     * @param $category_id
     * @param null $search_word
     * @param null $price_max
     * @param null $price_min
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getGoodsSearchCount($location_id= null,$category_id =null,$search_word = null,$price_max = null,$price_min = null)
    {
        $data['status'] = config('code.com');

        if($search_word != null){
            $data[] = ['name','like','%'.$search_word.'%'];
            $data[] = ['description','%'.$search_word.'%'];
        }
        if($price_max != null && $price_min != null){
            $data[] = ['price','BETWEEN',"$price_min,$price_max"];
        }elseif($price_max != null){
            $data[] = ['price','<=',$price_max];
        }elseif($price_min != null){
            $data[] = ['price','>=',$price_min];
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

        //类别索引
        if ($category_id != null){
            $data['category_id'] = [$category_id];
        }

        $field = [
            'id',
        ];

        return $this->allowField(true)
            ->field($field)
            ->where($data)
            ->count();
    }
}