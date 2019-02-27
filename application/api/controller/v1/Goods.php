<?php
/**
 * 商品相关
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 20:53
 */

namespace app\api\controller\v1;

use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Request;

class Goods extends PagerBase {
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
     * 用户发布商品
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsPost(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        $validate = Validate('api/Goods');
        if(!$validate->scene('goods_add')->check($data)){
            throw new APIException($validate->getError());
        };

        //数据组装
        $sdata = [
            'user_id'           => $my_user_id,
            'name'              => $data['name'],
            'description'       => $data['description'],
            'location_id'       => $data['location_id'],
            'location_detail'   => $data['location_detail'],
            'image_count'       => $data['image_count'],
        ];

        if(isset($data['pre_url'])){
            $sdata['pre_url'] = $data['pre_url'];
        }

        //数据入库
        try{
            $goods_id = $this->goodsModel->add($sdata);
            if(empty($goods_id)){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('数据插入失败');
        }

        //图片入库
        if($data['image_count'] > 0 && !empty($goods_id)){
            foreach ($data['image_urls'] as $image_order => $image_url){
                $idata = [
                    'goods_id'      => $goods_id,
                    'url'           => $image_url,
                    'image_order'   => $image_order
                ];

                //数据入库
                try{
                    $image_id = $this->goodsImagesModel -> add($idata);
                    if(empty($image_id)){
                        throw new APIException();
                    }
                }catch(\Exception $e){
                    throw new APIException('插入图片出错');
                }
            }
        }

        return api_result();
    }

    /**
     * 用户修改商品信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsEdit(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        $validate = Validate('api/Goods');
        if(!$validate->scene('goods_edit')->check($data)){
            throw new APIException($validate->getError());
        };

        $goods_id = $data['goods_id'];

        //数据组装
        if (isset($data['image_count'])){
            $sdata['image_count'] = $data['image_count'];
        }
        //如果修改图片，有三种修改方式
        if(isset($data['image_urls'])){
            if(isset($data['image_urls']['delete'])) {
                foreach ($data['image_urls']['delete'] as $image_id) {
                    try {
                        $res = $this->goodsImagesModel->statusChange($image_id, -1);
                        if (empty($res)) {
                            throw new APIException();
                        }
                    } catch (\Exception $e) {
                        throw new APIException('删除图片出错');
                    }
                }
            }
            if(isset($data['image_urls']['change'])){
                foreach ($data['image_urls']['change'] as $image_info) {
                    $sdata = [
                        'id'        => $image_info['image_id'],
                        'url'       => $image_info['image_url'],
                    ];
                    try {
                        $res = $this->goodsImagesModel->upgrade($sdata);
                        if (empty($res)) {
                            throw new APIException();
                        }
                    } catch (\Exception $e) {
                        throw new APIException('修改图片出错');
                    }
                }
            }
            if(isset($data['image_urls']['add'])){
                foreach ($data['image_urls']['add'] as $image_info) {
                    $sdata = [
                        'goods_id'      => $goods_id,
                        'url'           => $image_info['url'],
                        'order'         => $image_info['image_order'],
                    ];
                    try {
                        $res = $this->goodsImagesModel->add($sdata);
                        if (!$res) {
                            throw new APIException();
                        }
                    } catch (\Exception $e) {
                        throw new APIException('修改图片出错');
                    }
                }
            }
        }
        if(isset($data['description'])){
            $sdata['description'] = $data['description'];
        }
        if(isset($data['name'])){
            $sdata['name'] = $data['name'];
        }
        if(isset($data['location_id'])){
            $sdata['location_id'] = $data['location_id'];
        }
        if(isset($data['location_detail'])){
            $sdata['location_detail'] = $data['location_detail'];
        }
        if(isset($data['pre_url'])){
            $sdata['pre_url'] = $data['pre_url'];
        }
        if(isset($data['price'])){
            $sdata['price'] = $data['price'];
        }
        $sdata['id'] = $goods_id;

        //数据修改
        try{
            $res = $this->goodsModel->upgrade($sdata);
            if(empty($res)){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('数据修改失败');
        }

        return api_result();
    }

    /**
     * 获取某用户发布的所有商品简略信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsInfo(Request $request)
    {
        $data           = $request->data;
        //验证
        $validate = Validate('api/Goods');
        if(!$validate->scene('goods_get')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id    = $data['user_id'];
        $my_user_id = $request->user_id;

        //读取用户发布的商品条数
        try{
            $count = $this->goodsModel->getUserGoodsCount($user_id);
        }catch(\Exception $e){
            throw new APIException('获取用户商品总条数出错');
        }
        if($count == 0){
            throw new APIException('该用户没有发布任何商品');
        }

        //初始化分页变量
        $this->initPaginateParams($count,$data);

        //获取所有相关用户商品
        try{
            $res = $this->goodsModel->getUserGoods($user_id,$this->from,$this->size);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取用户商品出错');
        }

        //获取其他信息
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'goods_info'      => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取一个商品的详细信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getOneGoods(Request $request)
    {
        $data           = $request->data;
        //验证
        $validate = Validate('api/Goods');
        if(!$validate->scene('one_goods_get')->check($data)){
            throw new APIException($validate->getError());
        };
        $goods_id   = $data['goods_id'];
        $my_user_id = $request->user_id;

        //直接读取该条商品详细信息
        try{
            $res = $this->goodsModel->get($goods_id);
        }catch(\Exception $e){
            throw new APIException($e->getFile().$e->getLine().$e->getMessage());
        }

        //补全其他信息
        $res = $this->getOtherInformation($res,$my_user_id);

        //返回数据
        $rdata = [
            'goods_info'      => $res,
        ];

        return api_result($rdata);

    }

    /**
     * 获取所有的商品信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getAllGoodsInfo(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        //获取总商品数目
        try{
            $count = $this->goodsModel->getAllGoodsCount();
        }catch(\Exception $e){
            throw new APIException('获取商品总数出错');
        }

        if($count == 0){
            throw new APIException('暂无商品');
        }
        //初始化分页变量
        $this->initPaginateParams($count,$data);
        //获取商品
        //点赞数倒序，时间辅助
        $order = [
            'on_top'        =>  'desc',
            'favor_count'   =>  'desc',
            'create_time'   =>  'desc'
        ];
        try{
            $res = $this->goodsModel->getAllGoods($order,$this->from,$this->size);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取商品出错');
        }

        //完善信息
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

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
     * 获取特定地区内的所有商品信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getGoodsInArea(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('Location');
        if(!$validate->scene('get_location_goods')->check($data)){
            throw new APIException($validate->getError());
        };

        $location_id = $data['location_id'];
        //获取总商品数目
        try{
            $count = $this->goodsModel->getLocationGoodsCount($location_id);
        }catch(\Exception $e){
            throw new APIException('获商品总数出错');
        }

        if($count == 0){
            throw new APIException('暂无商品');
        }
        //初始化分页变量
        $this->initPaginateParams($count,$data);
        //获取商品
        //点赞数倒序，时间辅助
        $order = [
            'favor_count'   =>  'desc',
            'create_time'   =>  'desc'
        ];
        try{
            $res = $this->goodsModel->getLocationGoods($order,$location_id,$this->from,$this->size);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取商品出错');
        }

        //完善信息
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

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
     * 用户删除商品
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsDelete(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('api/Goods');
        if(!$validate->scene('goods_delete')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id        = $data['user_id'];
        $goods_id       = $data['goods_id'];

        //为了以后扩展方方便,验证身份
        if($my_user_id != $user_id){
            throw new APIException('没有权限删除');
        }

        //删除数据
        try{
            $res = $this->goodsModel->remove($goods_id);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('删除出错');
        }

        return api_result();
    }

    /**
     * 用户操作商品
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsOperating(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('api/Goods');
        if(!$validate->scene('goods_operating')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id        = $data['user_id'];
        $goods_id       = $data['goods_id'];
        $status         = $data['status'];

        //为了以后扩展方方便,验证身份
        if($my_user_id != $user_id){
            throw new APIException('没有权限操作');
        }

        //下架数据
        try{
            $res = $this->goodsModel->statusChange($goods_id,$status);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('操作出错');
        }

        return api_result();
    }

    /**
     * 获取特定状态的商品
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getStatusGoods(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('api/Goods');
        if(!$validate->scene('get_goods_by_status')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id        = $data['user_id'];
        $status         = $data['status'];

        //为了以后扩展方方便,验证身份
        if($my_user_id != $user_id){
            throw new APIException('没有权限获取');
        }
        
        //获取总数目
        try{
            $count = $this->goodsModel->getGoodsCountByStatus($status);
        }catch(\Exception $e){
            throw new APIException('获取总数目出错');
        }
        
        if($count == 0){
            throw new APIException('暂无商品');
        }
        
        //初始化分页数据
        $this->initPaginateParams($count,$data);
        
        //获取对应数据
        $order = [
            'create_time'   => 'desc'  
        ];
        try{
            $res = $this->goodsModel->getGoodsListByStatus($order,$status,$this->from,$this->size);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取出错');
        }

        //完善信息
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'          => $this->from,
            'size'          => $this->size,
            'page'          => $this->page,
            'totalCount'    => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'goods'             => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);

    }

    /**
     * 获取某类别下的所有商品信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getCategoryGoods(Request $request)
    {
        $data = $request->data;
        $my_user_id = $request->user_id;
        $validate = Validate('api/Category');
        if(!$validate->scene('get_category_goods')->check($data)){
            throw new APIException($validate->getError());
        };

        $category_id    = $data['$category_id'];

        //获取数量
        try{
            $count = $this->goodsModel->getCategoryGoodsCount($category_id);
        }catch(\Exception $e){
            throw new APIException('获取商品数量出错');
        }

        if($count == 0){
            throw new APIException('该类别暂时没有商品');
        }

        //初始化分页变量
        $this->initPaginateParams($count,$data);
        //获取商品
        $order = [
            'create_time'   =>  'desc'      //按照create_time排序
        ];
        try{
            $res = $this->goodsModel->getCategoryGoodsList($order,$category_id,$this->from,$this->size);
            if(empty($res[0])){
               throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取商品列表出错');
        }

        //完善基本信息
        $res = $this->getOtherSimpleInformation($res,$my_user_id);

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
     * 获取相关信息
     * @param $res
     * @param $my_user_id
     * @return mixed
     * @throws APIException
     */
    private function getOtherInformation($res,$my_user_id)
    {
        //循环获得相关数据
        $goods_info = &$res;

        $goods_id               = $goods_info['id'];
        $goods_user_id          = $goods_info['user_id'];
        $image_count            = $goods_info['image_count'];
        $favor_count            = $goods_info['favor_count'];
        $comment_count          = $goods_info['comment_count'];
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
                $image_res = $this->goodsImagesModel->getGoodsImages($goods_id,$image_count);
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

        //读取评论
        if($comment_count > 0){
            try{
                $comment_res = $this->goodsCommentsModel->getGoodsComments($goods_id,$comment_count);
            }catch(\Exception $e){
                throw new APIException('读取用户商品评论出错');
            }

            foreach($comment_res as $comment){
                if(!empty($comment)){
                    //读取回复者头像和名字
                    try{
                        $user_res = $this->userModel->getUsername($comment['user_id']);
                        if(!$user_res){
                            throw new APIException();
                        }
                        $user_info[$comment['user_id']] = [
                            'avatar'    =>  $user_res['avatar'],
                            'nickname'  =>  $user_res['nickname']
                        ];
                    }catch(\Exception $e){
                        throw new APIException('读取用户信息出错');
                    }
//            dump($user_info);
                    $comment['avatar']     =
                        $user_info[$comment['user_id']]['avatar'];
                    $comment['nickname']   =
                        $user_info[$comment['user_id']]['nickname'];

                    //读取其他回复该回复的信息
                    try{
                        $comment_id         = $comment['id'];
                        $comment_reply_res  = $this->goodsCommentsModel->getGoodsCommentsReply($goods_id,$comment_id);
                    }catch(\Exception $e){
                        throw new APIException('读取用户商品评论出错');
                    }

                    //读取是否被当前用户点赞
                    try{
                        $comment_reply_favor_res = $this->goodsCommentsFavorModel->checkFavor($my_user_id,$comment['id']);
//                                    dump($comment_reply_favor_res);
                        if(!$comment_reply_favor_res){
                            $comment['is_favored'] = false;
                        }else{
                            $comment['is_favored'] = true;
                        }
                    }catch(\Exception $e){
                        throw new APIException('读取评论回复用户点赞状态失败');
                    }

                    foreach($comment_reply_res as $comment_re){
                        if(!empty($comment_re)) {
                            //读取回复者头像和名称
                            try {
                                $user_res = $this->userModel->getUsername($comment_re['user_id']);
                                if (!$user_res) {
                                    throw new APIException();
                                }
                                $user_info[$comment_re['user_id']] = [
                                    'avatar'    =>  $user_res['avatar'],
                                    'nickname'  =>  $user_res['nickname']
                                ];
                            }catch(\Exception $e){
                                throw new APIException('读取用户信息出错');
                            }
//            dump($user_info);
                            $comment_re['avatar']     =
                                $user_info[$comment_re['user_id']]['avatar'];
                            $comment_re['nickname']   =
                                $user_info[$comment_re['user_id']]['nickname'];

                        }
                    }

                    //再循环一遍获得被回复者的头像，保持良好的兼容性
                    foreach($comment_reply_res as $comment_re){
                        try {
                            $reply_to_user_info_res = $this->goodsCommentsModel->getUserID($comment_re['id']);
                            if (!$reply_to_user_info_res) {
                                throw new APIException();
                            }
                            $to_user_id = $reply_to_user_info_res['user_id'];
                            $comment_re['to_avatar'] =
                                $user_info[$to_user_id]['avatar'];
                            $comment_re['to_nickname'] =
                                $user_info[$to_user_id]['nickname'];
                            $comment_re['to_user_id'] = $to_user_id;
                        }catch(\Exception $e){
                            throw new APIException('读取被回复者头像出错');
                        }
                    }
                    $comment['reply'] = $comment_reply_res;
                }
            }
//                dump($reply_res);
            $goods_info['reply']  = $comment_res;
        }

        return $res;
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

}