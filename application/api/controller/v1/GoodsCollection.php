<?php
/**
 * 用户收藏商品相关
 * User: xjyplayer
 * Date: 2019/1/22
 * Time: 12:32
 */

namespace app\api\controller\v1;

use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Hook;
use think\Request;

class GoodsCollection extends PagerBase
{
    private $goodsCollectionModel;
    private $goodsModel;
    private $userModel;

    protected function initialize()
    {
        $this->goodsCollectionModel     = model('common/GoodsCollection');
        $this->goodsModel               = model('common/Goods');
        $this->userModel                = model('common/User');
    }

    /**
     * 读取对特定商品的收藏状态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function readCollection(Request $request)
    {
        $data           = $request->data;
        $user_id        = $request->user_id;
        //验证参数
//        dump($data);
        $validate = Validate('api/GoodsCollection');
        if(!$validate->scene('read_collection')->check($data)){
            throw new APIException($validate->getError());
        };
        $goods_id = $data['goods_id'];

        //判断该关注关系是否存在
        $is_collected = $this->isCollected($user_id,$goods_id);

        //返回数据
        $data = [
            'is_collected' => $is_collected,
        ];

        return api_result($data);
    }

    /**
     * 收藏或者取消收藏
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function collect(Request $request)
    {
        $data           = $request->data;
        $user_id        = $request->user_id;
        //验证参数
        $validate = Validate('api/GoodsCollection');
        if(!$validate->scene('collection')->check($data)){
            throw new APIException($validate->getError());
        };
        $goods_id = $data['goods_id'];

        //查询关注关系是否存在
        $is_collected = $this->isCollected($user_id,$goods_id);

        if($is_collected){
            //更新用户收藏数目，并且检查用户是否存在
            try{
                $res = $this->userModel->userDec($user_id,'collection_count');
                if(!$res) {
                    throw new APIException('增加收藏数目失败');
                }
                $res = $this->goodsModel->goodsDec($goods_id, 'collection_count');
                if(!$res){
                    throw new APIException('增加被收藏数目失败');
                }
            }catch(\Exception $e){
                throw new APIException('更新收藏数目出错,请重新操作');
            }

            //取消关注联系
            try{
                $res = $this->goodsCollectionModel->remove($user_id,$goods_id);
                if(!$res){
                    throw new APIException('取消收藏失败');
                }
            }catch(\Exception $e){
                throw new APIException('取消收藏失败');
            }
        }else{
            //创建关注联系
            $udata = [
                'user_id'       => $user_id,
                'goods_id'      => $goods_id
            ];

            //更新用户关注数目，并且检查用户是否存在
            try{
                $res = $this->userModel->userInc($user_id,'collection_count');
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
                $res = $this->goodsModel->goodsInc($goods_id,'collection_count');
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
            }catch(\Exception $e){
                throw new APIException('更新用户关注数目出错,或者没有该用户');
            }

            //插入数据
            try{
                $res = $this->goodsCollectionModel->add($udata);
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
            }catch(\Exception $e){
                throw new APIException('关注失败');
            }
        }

        $rdata = [
            'is_collected' => !$is_collected,
        ];
        return api_result($rdata);
    }

    /**
     * 获取某用户所收藏的商品信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getCollectedGoods(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/GoodsCollection');
        if(!$validate->scene('get_collected')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id   = $data['user_id'];

        //获取总数目
        try{
            $total_count = $this->goodsCollectionModel->getCollectedGoodsCount($user_id);
        }catch(\Exception $e){
            throw new APIException('读取用户收藏数目出错');
        }

        if($total_count == 0){
            throw new APIException('该用户暂时没有收藏商品');
        }

        //初始化分页信息
        $this->initPaginateParams($total_count,$data);

        //获取用户收藏的商品信息
        try{
            $res = $this->goodsCollectionModel->getCollectedGoods($user_id,$this->from,$this->size);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('读取用户关注信息出错');
        }
        //完善商品信息
        foreach ($res as $goods){
            if(isset($goods['goods_id'])){
                try{
                    $goods_info     = $this->goodsModel->get($goods['goods_id']);
                    if(!$goods_info){
                        throw new APIException();
                    }
                }catch(\Exception $e){
                    throw new APIException('获取商品信息出错');
                }
            }
            $goods = $goods_info;
        }
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

        //返回信息
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'goods_collected'       => $res,
            'pagenate'              => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取收藏某商品的用户信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getCollectionUser(Request $request)
    {
        $data           = $request->data;
        //验证参数
        $validate = Validate('api/GoodsCollection');
        if(!$validate->scene('get_collection')->check($data)){
            throw new APIException($validate->getError());
        };

        $goods_id     = $data['goods_id'];
        //获取总数目
        try{
            $total_count = $this->goodsCollectionModel->getGoodsCollectionCount($goods_id);
        }catch(\Exception $e){
            throw new APIException('读取关注用户数目出错');
        }

        if($total_count == 0){
            throw new APIException('该用户暂时没有关注其他用户');
        }

        //初始化分页信息
        $this->initPaginateParams($total_count,$data);

        //获取用户关注的用户信息
        try{
            $res = $this->goodsCollectionModel->getGoodsCollection($goods_id,$this->from,$this->size);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('读取收藏用户信息出错');
        }
        //完善用户信息
        //维护一个数组
        $user_info = [];
        foreach ($res as $user){
            if(isset($user['user_id'])){
                $user_id = $user['user_id'];
                try{
                    $user_res = $this->userModel->getUsername($user_id);
                    if(!$user_res){
                        throw new APIException();
                    }
                    $user_info[$user_res['id']] = [
                        'avatar'    =>  $user_res['avatar'],
                        'nickname'  =>  $user_res['nickname']
                    ];
                }catch(\Exception $e){
                    throw new APIException('读取用户信息出错');
                }
            }
        }

        //返回信息
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'user_collected'      => $user_info,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 判断特定的收藏关系是否存在
     * @param $user_id
     * @param $goods_id
     * @return bool
     */
    private function isCollected($user_id,$goods_id){
        //查看该关注关系是否存在
        try{
            $res = $this->goodsCollectionModel->checkCollection($user_id,$goods_id);
            if(!$res){
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * 获取简单的信息
     * @param $res
     * @param $my_user_id
     * @return mixed
     * @throws APIException
     */
    private function getOtherSimpleInformation($res,$my_user_id){
        //循环获得相关数据
        foreach ($res as $goods_info){
            $goods_id               = $goods_info['id'];
            $goods_user_id          = $goods_info['user_id'];
            $image_count            = $goods_info['image_count'];
            $favor_count            = $goods_info['favor_count'];
            $collection_count       = $goods_info['collection_count'];
            //维护一个$user_info数组
            $user_info         = [];

            //读取用户头像
            try{
                $user_res = $this->userModel->getUsername($goods_user_id);
                if(!$user_res){
                    throw new APIException();
                }
                $user_info[$user_res['id']] = [
                    'avatar'    =>  $user_res['avatar'],
                    'nickname'  =>  $user_res['nickname']
                ];
            }catch(\Exception $e){
                throw new APIException('读取用户信息出错');
            }
//            dump($user_info);
            $goods_info['avatar']     =
                $user_info[$goods_user_id]['avatar'];
            $goods_info['nickname']   =
                $user_info[$goods_user_id]['nickname'];

            //读取图片_
            if($image_count > 0){
                try{
                    $image_res = $this->goodsImagesModel->getGoodsImages($goods_id,1);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态图片出错');
                }
//                dump($image_res);
                $goods_info['images'] = $image_res;
            }
            //读取点赞状态
            if($favor_count > 0){
                try{
                    $favor_res = $this->goodsFavorModel->checkFavor($goods_id,$my_user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态图片出错');
                }
                if($favor_res){
                    $goods_info['is_favored']   = true;
                }else{
                    $goods_info['is_favored']   = false;
                }
            }
            //读取收藏状态
            if($collection_count > 0){
                try{
                    $collection_res = $this->goodsCollectionModel->checkColection($goods_id,$my_user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态图片出错');
                }
                if($collection_res){
                    $goods_info['is_collected']   = true;
                }else{
                    $goods_info['is_collected']   = false;
                }
            }

        }
        return $res;
    }

}