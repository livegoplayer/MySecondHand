<?php
/**
 * 用户动态相关
 * User: xjyplayer
 * Date: 2019/1/25
 * Time: 13:14
 */
namespace app\api\controller\v1;

use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Request;

class UserDynamic extends PagerBase
{
    //需要的模型
    private $userDynamicModel ;
    private $userDynamicImagesModel;
    private $userDynamicFavorModel;
    private $userDynamicReplyModel;
    private $userModel;
    private $userDynamicReplyFavorModel;

    protected function initialize()
    {
        $this->userDynamicModel             = model('common/UserDynamic');
        $this->userDynamicImagesModel       = model('common/UserDynamicImages');
        $this->userDynamicFavorModel        = model('common/UserDynamicFavor');
        $this->userDynamicReplyModel        = model('common/UserDynamicReply');
        $this->userModel                    = model('common/User');
        $this->userDynamicReplyFavorModel   = model('common/UserDynamicReplyFavor');
    }

    /**
     * 用户发布动态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function dynamicPost(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        $validate = Validate('UserDynamic');
        if(!$validate->scene('user_dynamic_add')->check($data)){
            throw new APIException($validate->getError());
        };

        //数据组装dynamic
        $sdata = [
            'user_id'       => $my_user_id,
            'image_count'   => $data['image_count'],
            'content'       => $data['content'],
            'location_id'   => $data['location_id'],
            'location_detail'   => $data['location_detail'],
            'on_top'        => isset($data['on_top']) ? 1 : 0
        ];

        //数据入库
        try{
            $dynamic_id = $this->userDynamicModel->add($sdata);
            if(empty($dynamic_id)){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('数据插入失败');
        }

        //数据组装dynamic_images
        if($data['image_count'] > 0 && !empty($dynamic_id)){
            foreach ($data['image_urls'] as $image_order => $image_url){
                $idata = [
                    'dynamic_id'    => $dynamic_id,
                    'url'           => $image_url,
                    'image_order'   => $image_order
                ];

                //数据入库
                try{
                    $image_id = $this->userDynamicImagesModel -> add($idata);
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
     * 修改用户动态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function  dynamicEdit(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        $validate = Validate('UserDynamic');
        if(!$validate->scene('user_dynamic_edit')->check($data)){
            throw new APIException($validate->getError());
        };

        $dynamic_id = $data['dynamic_id'];

        //数据组装dynamic
        if (isset($data['image_count'])){
            $sdata['image_count'] = $data['image_count'];
        }
        //如果修改图片，有三种修改方式
        if(isset($data['image_urls'])){
            if(isset($data['image_urls']['delete'])) {
                foreach ($data['image_urls']['delete'] as $image_id) {
                    try {
                        $res = $this->userDynamicImagesModel->statusChange($image_id, -1);
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
                        'url' => $image_info['image_url'],
                    ];
                    try {
                        $res = $this->userDynamicImagesModel->upgrade($sdata);
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
                        'dynamic_id'    => $dynamic_id,
                        'url'           => $image_info['url'],
                        'order'         => $image_info['image_order'],
                    ];
                    try {
                        $res = $this->userDynamicImagesModel->add($sdata);
                        if (!$res) {
                            throw new APIException();
                        }
                    } catch (\Exception $e) {
                        throw new APIException('修改图片出错');
                    }
                }
            }
        }
        if(isset($data['content'])){
            $sdata['content'] = $data['content'];
        }
        if(isset($data['location_id'])){
            $sdata['location_id'] = $data['location_id'];
        }
        if(isset($data['location_detail'])){
            $sdata['location_detail'] = $data['location_detail'];
        }
        if(isset($data['on_top'])){
            $sdata['on_top'] = $data['on_top'];
        }
        $sdata['id'] = $dynamic_id;

        //数据修改
        try{
            $res = $this->userDynamicModel->upgrade($sdata);
            if(empty($res)){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('数据修改失败');
        }

        return api_result();
    }

    /**
     * 获取某用户所有的动态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function userDynamicInfo(Request $request)
    {
        $data           = $request->data;
        $validate       = Validate('api/UserDynamic');
        if(!$validate->scene('user_dynamic_get')->check($data)){
            throw new APIException($validate->getError(),403);
        };
        $user_id    = $data['user_id'];
        $my_user_id = $request->user_id;

        //读取用户动态条数
        try{
            $count = $this->userDynamicModel->getUserDynamicCount($user_id);
        }catch(\Exception $e){
            throw new APIException('获取用户动态总条数出错');
        }
        if($count == 0){
            throw new APIException('该用户没有发布任何动态');
        }

        //初始化分页变量
        $this->initPaginateParams($count,$data);

        //获取所有相关用户动态
        try{
            $res = $this->userDynamicModel->getUserDynamic($user_id,$this->from,$this->size);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取用户动态出错');
        }

        //获取其他信息
        $res = $this->getOtherInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'dynamic_List'      => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取特定条数的动态信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function dynamicListInfo(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;

        //默认返回50条动态
        $limit_count    = !empty($data['limit_count']) ?  $data['limit_count'] : 50;
        //获取总动态数目
        try{
            $count = $this->userDynamicModel->getDynamicListCount($limit_count);
        }catch(\Exception $e){
            throw new APIException('获取动态总数出错');
        }

        if($count == 0){
            throw new APIException('暂无动态');
        }
        //初始化分页变量
        $this->initPaginateParams($count,$data);
        //获取动态
        //点赞数倒序，时间辅助
        $order = [
            'on_top'        =>  'desc',
            'favor_count'   =>  'desc',
            'create_time'   =>  'desc'
        ];
        try{
            $res = $this->userDynamicModel->getDynamicList($order,$this->from,$this->size,$limit_count);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取动态出错');
        }

        //完善信息
        $res = $this->getOtherInformation($res,$my_user_id);

        //返回数据
        if($this->from + $this->size > $this->totalCount)
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'user_dynamic_Info' => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取特定地区内的所用用户的动态信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getUserDynamicInArea(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('Location');
        if(!$validate->scene('get_location_user_dynamic')->check($data)){
            throw new APIException($validate->getError());
        };

        $location_id = $data['location_id'];
        //获取总动态数目
        try{
            $count = $this->userDynamicModel->getLocationUserDynamicCount($location_id);
        }catch(\Exception $e){
            throw new APIException('获取动态总数出错');
        }

        if($count == 0){
            throw new APIException('暂无动态');
        }
        //初始化分页变量
        $this->initPaginateParams($count,$data);
        //获取动态
        //点赞数倒序，时间辅助
        $order = [
            'favor_count'   =>  'desc',
            'create_time'   =>  'desc'
        ];
        try{
            $res = $this->userDynamicModel->getLocationUserDynamic($order,$location_id,$this->from,$this->size);
            if(empty($res[0])){
                throw  new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取动态出错');
        }

        //完善信息
        $res = $this->getOtherInformation($res,$my_user_id);

        //返回数据
        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'user_dynamic_Info' => $res,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 用户删除动态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function dynamicDelete(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        $validate = Validate('api/UserDynamic');
        if(!$validate->scene('user_dynamic_delete')->check($data)){
            throw new APIException($validate->getError());
        };

        $user_id        = $data['user_id'];
        $dynamic_id     = $data['dynamic_id'];

        //为了以后扩展方方便,验证身份
        if($my_user_id != $user_id){
            throw new APIException('没有权限删除');
        }

        //删除数据
        try{
            $res = $this->userDynamicModel->remove($dynamic_id);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('删除出错');
        }

        return api_result();
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
        foreach ($res as $dynamic_info){
            $dynamic_id         = $dynamic_info['id'];
            $dynamic_user_id    = $dynamic_info['user_id'];
            $image_count        = $dynamic_info['image_count'];
            $favor_count        = $dynamic_info['favor_count'];
            $reply_count        = $dynamic_info['reply_count'];
            //维护一个$user_info数组
            $user_info         = [];

            //读取用户头像
            try{
                $user_res = $this->userModel->getUsername($dynamic_user_id);
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
            $dynamic_info['avatar']     =
                $user_info[$dynamic_user_id]['avatar'];
            $dynamic_info['nickname']   =
                $user_info[$dynamic_user_id]['nickname'];

            //读取图片
            if($image_count > 0){
                try{
                    $image_res = $this->userDynamicImagesModel->getDynamicImages($dynamic_id,$image_count);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态图片出错');
                }
//                dump($image_res);
                $dynamic_info['images'] = $image_res;
            }
            //读取点赞状态
            if($favor_count > 0){
                try{
                    $favor_res = $this->userDynamicFavorModel->checkFavor($dynamic_id,$my_user_id);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态图片出错');
                }
                if($favor_res){
                    $dynamic_info['is_favored']   = true;
                }else{
                    $dynamic_info['is_favored']   = false;
                }
            }

            //读取回复
            if($reply_count > 0){
                try{
                    $reply_res = $this->userDynamicReplyModel->getUserDynamicReply($dynamic_id,$reply_count);
                }catch(\Exception $e){
                    throw new APIException('读取用户动态评论出错');
                }

                foreach($reply_res as $reply){
                    if(!empty($reply)){
                        //读取回复者头像和名字
                        try{
                            $user_res = $this->userModel->getUsername($reply['user_id']);
                            if(!$user_res){
                                throw new APIException();
                            }
                            $user_info[$reply['user_id']] = [
                                'avatar'    =>  $user_res['avatar'],
                                'nickname'  =>  $user_res['nickname']
                            ];
                        }catch(\Exception $e){
                            throw new APIException('读取用户信息出错');
                        }
//            dump($user_info);
                        $reply['avatar']     =
                            $user_info[$reply['user_id']]['avatar'];
                        $reply['nickname']   =
                            $user_info[$reply['user_id']]['nickname'];

                        //读取其他回复该回复的信息
                        try{
                            $reply_id = $reply['id'];
                            $reply_reply_res = $this->userDynamicReplyModel->getUserDynamicReplyReply($dynamic_id,$reply_id);
                        }catch(\Exception $e){
                            throw new APIException('读取用户动态评论出错');
                        }

                        //读取是否被当前用户点赞
                        try{
                            $reply_reply_favor_res = $this->userDynamicReplyFavorModel->getUserFavorStatus($my_user_id,$reply['id']);
//                                    dump($reply_reply_favor_res);
                            if(!$reply_reply_favor_res){
                                $reply['is_favored'] = false;
                            }else{
                                $reply['is_favored'] = true;
                            }
                        }catch(\Exception $e){
                            throw new APIException('读取评论回复用户点赞状态失败');
                        }

                        foreach($reply_reply_res as $reply_re){
                            if(!empty($reply_re)) {
                                //读取回复者头像和名称
                                try {
                                    $user_res = $this->userModel->getUsername($reply_re['user_id']);
                                    if (!$user_res) {
                                        throw new APIException();
                                    }
                                    $user_info[$reply_re['user_id']] = [
                                        'avatar'    =>  $user_res['avatar'],
                                        'nickname'  =>  $user_res['nickname']
                                    ];
                                }catch(\Exception $e){
                                    throw new APIException('读取用户信息出错');
                                }
//            dump($user_info);
                                $reply_re['avatar']     =
                                    $user_info[$reply_re['user_id']]['avatar'];
                                $reply_re['nickname']   =
                                    $user_info[$reply_re['user_id']]['nickname'];

                            }
                        }

                        //再循环一遍获得被回复者的头像，保持良好的兼容性
                        foreach($reply_reply_res as $reply_re){
                            try {
                                $replay_to_user_info_res = $this->userDynamicReplyModel->getUserID($reply_re['id']);
                                if (!$replay_to_user_info_res) {
                                    throw new APIException();
                                }
                                $to_user_id = $replay_to_user_info_res['user_id'];
                                $reply_re['to_avatar'] =
                                    $user_info[$to_user_id]['avatar'];
                                $reply_re['to_nickname'] =
                                    $user_info[$to_user_id]['nickname'];
                                $reply_re['to_user_id'] = $to_user_id;
                            }catch(\Exception $e){
                                throw new APIException('读取被回复者头像出错');
                            }
                        }
                        $reply['reply'] = $reply_reply_res;
                    }
                }
//                dump($reply_res);
                $dynamic_info['reply']  = $reply_res;
            }
        }
        return $res;
    }

}