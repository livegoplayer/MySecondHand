<?php
/**
 * 搜索相关
 * User: xjyplayer
 * Date: 2019/2/21
 * Time: 18:21
 */

namespace app\api\controller\v1;

use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Request;

class Search extends PagerBase
{
    private $goodsModel;
    private $goodsImagesModel;
    private $userModel;
    private $goodsFavorModel;
    private $goodsCommentsModel;
    private $goodsCommentsFavorModel;
    private $goodsCollectionModel;

    protected function initialize ()
    {
        $this->goodsModel               = model('common/Goods');
        $this->goodsImagesModel         = model('common/GoodsImages');
        $this->userModel                = model('common/User');
        $this->goodsFavorModel          = model('common/GoodsFavor');
        $this->goodsCommentsModel       = model('common/GoodsComments');
        $this->goodsCommentsFavorModel  = model('common/GoodsCommentsFavor');
        $this->goodsCollectionModel     = model('common/GoodsCollection');
    }

    /**
     * 搜索特定的商品
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function searchGoods(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        //获取搜索条件
        $search_word    = isset($data['search_word']) ? null:$data['search_word'];
        $price_max      = isset($data['price_max']) ? null:$data['price_max'];
        $price_min      = isset($data['price_min']) ? null:$data['price_min'];
        $location_id    = isset($data['location_id']) ? null:$data['location_id'];
        $category_id    = isset($data['category_id']) ? null:$data['category_id'];

        //获取搜索总数
        try{
            $count = $this->goodsModel->getGoodsSearchCount($location_id,$category_id,$search_word,$price_max,$price_min);
        }catch(\Exception $e){
            throw new APIException('获取数量出错');
        }

        if($count == 0){
            throw new APIException('暂无数据');
        }

        //初始化分页变量
        $this->initPaginateParams($count,$data);

        //获取数据
        $order = [
            'create_time',
        ];
        try{
            $res = $this->goodsModel->getGoodsSearchList($order,$location_id,$category_id,$this->from,$this->size,$search_word,$price_max,$price_min);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取数量出错');
        }

        //完善信息
        $res = $this->getOtherSimpleGoodsInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'goods' => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 搜索用户
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function searchUser(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        //获取搜索条件
        $location_id    = isset($data['location_id']) ? null:$data['location_id'];
        $search_word    = empty($data['search_word']) ? null:$data['search_word'];
        $price_max      = empty($data['price_max']) ? null:$data['price_max'];
        $price_min      = empty($data['price_min']) ? null:$data['price_min'];

        //获取搜索总数
        try{
            $count = $this->userModel->getUsersSearchCount($location_id,$search_word);
        }catch(\Exception $e){
            throw new APIException('获取数量出错');
        }

        if($count == 0){
            throw new APIException('暂无数据');
        }

        //初始化分页变量
        $this->initPaginateParams($count,$data);

        //获取数据
        $order = [
            'create_time',
        ];
        try{
            $res = $this->userModel->getUsersSearchList($order,$location_id,$this->from,$this->size,$search_word,$price_max,$price_min);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取数量出错');
        }

        //完善信息
        $res = $this->getOtherUserInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'users'             => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取简单的商品信息
     * @param $res
     * @param $my_user_id
     * @return mixed
     * @throws APIException
     */
    private function getOtherSimpleGoodsInformation($res,$my_user_id){
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

            //读取图片
            if($image_count > 0){
                try{
                    $image_res = $this->goodsImagesModel->getGoodsImages($goods_id,1);
                }catch(\Exception $e){
                    throw new APIException('读取用户商品图片出错');
                }
//                dump($image_res);
                $goods_info['images'] = $image_res;
            }
            //读取点赞状态
            if($favor_count > 0){
                try{
                    $favor_res = $this->goodsFavorModel->checkFavor($goods_id,$my_user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户商品图片出错');
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
                    throw new APIException('读取用户商品图片出错');
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

    /**
     * 完善用户信息
     * @param $res
     * @param $my_user_id
     * @return mixed
     * @throws APIException
     */
    private function getOtherUserInformation(&$res,$my_user_id){
        //循环获得相关数据
        foreach ($res as $user_info){
            $user_id                = $user_info['id'];
            $favor_count            = $user_info['favor_count'];
            $fans_count             = $user_info['fans_count'];

            //读取点赞状态
            if($favor_count > 0){
                try{
                    $favor_res = $this->userFavorModel->checkFavor($my_user_id,$user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户商品图片出错');
                }
                if($favor_res){
                    $user_info['is_favored']   = true;
                }else{
                    $user_info['is_favored']   = false;
                }
            }
            //读取收藏状态
            if($fans_count > 0){
                try{
                    $fans_res = $this->userConcernedModel->checkConcern($my_user_id,$user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户商品图片出错');
                }
                if($fans_res){
                    $user_info['is_collected']   = true;
                }else{
                    $user_info['is_collected']   = false;
                }
            }

        }
        return $res;
    }
}