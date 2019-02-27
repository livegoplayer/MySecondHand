<?php
/**
 * 用户动态回复相关
 * User: xjyplayer
 * Date: 2019/2/18
 * Time: 9:16
 */

namespace app\api\controller\v1;

use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

class UserDynamicReply extends Controller
{
    private $userDynamicModel ;
    private $userDynamicReplyModel;

    public function initialize()
    {
        $userDynamicModel       = model('common/UserDynamic');
        $userDynamicFavorModel  = model('common/UserDynamicReply');
    }

    /**
     * 添加用户回复
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function userDynamicReply(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/UserDynamicReply');
        if(!$validate->scene('user_dynamic_reply')->check($data)){
            throw new APIException($validate->getError());
        };
        $dynamic_id = $data['dynamic_id'];

        //更新回复数目，并且检查动态是否存在
        try{
            $res = $this->userDynamicModel->userDynamicInc($dynamic_id,'reply_count');
            if(!$res) {
                throw new APIException('增加点赞数目失败');
            }
        }catch(\Exception $e){
            throw new APIException('更新动态评论数目出错,请重新操作');
        }
        //插入数据
        $idata = [
            'user_id'       => $my_user_id,
            'dynamic_id'    => $dynamic_id,
            'reply_type'    => $data['reply_type'],
            'reply_id'      => $data['reply_id'],
            'content'       => $data['content'],
        ];

        try{
            $res = $this->userDynamicReplyModel->add($idata);
            if(!$res) {
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('插入用户评论数据出错');
        }

        return api_result();
    }

    /**
     * 删除评论
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function userDynamicReplyDelete(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/UserDynamicReply');
        if(!$validate->scene('user_dynamic_reply_delete')->check($data)){
            throw new APIException($validate->getError());
        };
        $user_id    = $data['user_id'];
        $reply_id   = $data['reply_id'];

        if($my_user_id != $user_id){
            throw new APIException('没有权限删除');
        }

        //删除数据
        try{
            $res = $this->userDynamicReplyModel->remove($reply_id);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('删除出错');
        }

        return api_result();
    }

}