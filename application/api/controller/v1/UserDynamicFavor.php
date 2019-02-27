<?php
/**
 * 用户动态点在相关功能
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 8:43
 */

namespace app\api\controller\v1;

use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

class UserDynamicFavor extends Controller
{
    private $userDynamicModel ;
    private $userDynamicFavorModel;

    public function _initialize()
    {
        $userDynamicModel       = model('common/UserDynamic');
        $userDynamicFavorModel  = model('common/UserDynamicFavor');
    }

    /**
     * 点赞或者取消点赞
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function favor(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/UserDynamicFavor');
        if(!$validate->scene('favor')->check($data)){
            throw new APIException($validate->getError());
        };
        $dynamic_id = $data['dynamic_id'];

        //判断该点赞关系是否存在
        $is_favored = $this->isFavored($my_user_id,$dynamic_id);

        if($is_favored){
            //更新用户点赞数目，并且检查动态是否存在
            try{
                $res = $this->userDynamicModel->userDynamicDec($dynamic_id,'favor_count');
                if(!$res) {
                    throw new APIException('增加点赞数目失败');
                }
            }catch(\Exception $e){
                throw new APIException('更新动态点赞数目出错,请重新操作');
            }

            //取消关注联系
            try{
                $res = $this->userDynamicFavorModel->remove($my_user_id,$dynamic_id);
                if(!$res){
                    throw new APIException();
                }
            }catch(\Exception $e){
                throw new APIException('取消点赞失败');
            }
        }else{
            //创建点赞联系
            $udata = [
                'user_id'       => $my_user_id,
                'dynamic_id'    => $dynamic_id
            ];

            //更新用户关注数目，并且检查用户是否存在
            try{
                $res = $this->userDynamicModel->userDynamicInc($dynamic_id,'favor_count');
                //检查
                if(!$res){
                    throw new APIException();
                }
            }catch(\Exception $e){
                throw new APIException('更新动态点赞数目出错,或者没有该动态');
            }

            //插入数据
            try{
                $res = $this->userDynamicFavorModel->add($udata);
                //检查
                if(!$res){
                    throw new APIException();
                }
            }catch(\Exception $e){
                throw new APIException('点赞失败');
            }
        }
        $rdata = [
            'is_favored' => !$is_favored,
        ];
        return api_result($rdata);
    }

    /**
     * 查看点赞关系是否存在
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function readFavor(Request $request){
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/UserDynamicFavor');
        if(!$validate->scene('favor')->check($data)){
            throw new APIException($validate->getError());
        };
        $dynamic_id = $data['dynamic_id'];

        //判断该点赞关系是否存在
        $is_favored = $this->isFavored($my_user_id,$dynamic_id);

        //返回数据
        $data = [
            'is_favored' => $is_favored,
        ];

        return api_result($data);
    }

    /**
     * 查看点赞状态
     * @param $user_id
     * @param $dynamic_id
     * @return bool
     */
    private function isFavored($user_id,$dynamic_id){
        //查看该关注关系是否存在
        try{
            $res = $this->userDynamicFavorModel->checkFavor($user_id,$dynamic_id);
            if(!$res){
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
        return true;
    }
}