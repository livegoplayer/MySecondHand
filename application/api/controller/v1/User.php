<?php
/**
 * 用户界面模块
 * User: xjyplayer
 * Date: 2019/1/21
 * Time: 12:21
 */

namespace app\api\controller\v1;


use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Exception;
use think\Request;

class User extends PagerBase
{
    private $userModel;
    private $userFavorModel;
    private $userConcernedModel;

    public function initialize()
    {
        $this->userModel            = model('common/User');
        $this->userFavorModel       = model('common/UserFavor');
        $this->userConcernedModel   = model('common/UserConcerned');
    }

    /**
     * 获取用户的信息(自己的和别人的)
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     * @throws Exception
     */
    public function userInfo(Request $request)
    {
        $data = $request->data;
        $my_user_id = $request->user_id;
        //验证参数
        $validate = Validate('api/User');
        if(!$validate->scene('we_chat_user_info')->check($data)){
            throw new APIException($validate -> getError(),403);
        };

        $user_id = $data['user_id'];

        //增加用户的被阅读数
        try{
            $res = $this->userModel->userInc($user_id,'read_count');
            if(empty($res)){
                throw new APIException('用户不存在');
            }
        }catch(\Exception $e){
            throw new APIException($e->getMessage());
        }
//        dump($user_id);
        //查询用户是否存在
        try{
            $res = $this->userModel->getUserInfo($user_id);
//            if(empty($res)){
//                throw new APIException('用户不存在');
//            }
        }catch(\Exception $e){
            throw new Exception($e->getMessage());
        }

        //完善用户信息
        $res = $this->getOtherUserInformation($res,$my_user_id);

        //返回用户信息
        $sdate = [
            'user_info'     => $res,
        ];
        return api_result($sdate,'用户信息');
    }

    /**
     * 修改自己信息的请求
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function updateMyInfo(Request $request)
    {
        $data = $request->data;
        $data['user_id'] = $request->user_id;
        //验证参数
        $validate = Validate('api/User');
        if(!$validate->scene('we_chat_user_update')->check($data)){
            throw new APIException($validate -> getError(),403);
        };
        $user_id = $data['user_id'];
        //查询用户是否存在
        try{
            $res = $this->userModel->checkUserID($user_id);
        }catch(\Exception $e){
            throw new APIException($e->getMessage());
        }
//        dump($res);
        if(empty($res)){
            throw new APIException('用户不存在');
        }

        //组装数据
        if(isset($data['avatar'])){
            $udata['avatar'] = $data['avatar'];
        }
        if(isset($data['nickname'])){
            $udata['nickname'] = $data['nickname'];
        }
        if(isset($data['signature'])){
            $udata['signature'] = $data['signature'];
        }
        if(isset($data['signature'])){
            $udata['signature'] = $data['signature'];
        }
        if(isset($data['location'])){
            $udata['location'] = $data['location'];
        }
        if(isset($data['gender'])){
            $udata['gender'] = $data['gender'];
        }

        if(!isset($udata)){
            throw new APIException('未传入任何数据');
        }
        $udata['id'] = $user_id;
        //更新数据
        try{
            $res = $this->userModel->upgrade($udata);
        }catch(\Exception $e){
            throw new APIException('数据更新失败');
        }

        if(!$res){
            throw new APIException('数据更新失败');
        }

        //返回数据
        return api_result();
    }

    /**
     *
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getUserInArea(Request $request)
    {
        $data = $request->data;
        $my_user_id = $request->user_id;
        $validate = Validate('Location');
        if(!$validate->scene('get_location_user')->check($data)){
            throw new APIException($validate->getError());
        };

        $location_id = $data['location_id'];
        //获取总条数
        try{
            $total_count = $this->userModel->getUserInAreaCount(intval($location_id));
        }catch(\Exception $e){
            throw new APIException('获取附近用户出错');
        }
        //初始化分页变量
        $this->initPaginateParams($total_count,$data);
//        dump($location_id);
        //获取不同条件下的用户信息
        //如果选定省，则获取该省下的所有用户,如果选定市则，返回所有该市下的用户，同理区县最简单
        $order = [
            'fans_count' => 'desc'
        ];
        try{
            $userInfo = $this->userModel->getUserInArea($order,intval($location_id),$this->from,$this->size);
            if(empty($userInfo[0])){
                throw new APIException('没有用户信息');
            }
        }catch(\Exception $e){
            throw new APIException('获取用户信息出错');
        }

        //返回数据
        $userInfo = $this->getOtherUserInformation($userInfo,$my_user_id);

        $pagenate = [
            'from'      => $this->from,
            'size'      => $this->size,
            'page'      => $this->page,
            'totalCount'     => $this->totalCount,
            'totalPages'    => $this->totalPages,
        ];

        $rdata = [
            'userInfo' => $userInfo,
            'pagenate' => $pagenate
        ];

        return api_result($rdata);
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