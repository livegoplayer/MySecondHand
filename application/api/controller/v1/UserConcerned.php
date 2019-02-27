<?php
/**
 * 用户关注用户
 * User: xjyplayer
 * Date: 2019/1/22
 * Time: 12:32
 */

namespace app\api\controller\v1;

use app\api\controller\PagerBase;
use app\api\lib\exception\APIException;
use think\Request;

class UserConcerned extends PagerBase
{
    private $userConcernedModel;
    private $userModel;

    protected function initialize()
    {
        $this->userConcernedModel   = model('common/UserConcerned');
        $this->userModel            = model('common/User');
    }

    /**
     * 读取对特定用户的关注状态
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function readConcern(Request $request)
    {
        $data           = $request->data;
        $from_user_id   = $request->user_id;
        //验证参数
//        dump($data);
        $validate = Validate('api/UserConcerned');
        if(!$validate->scene('read_concern')->check($data)){
            throw new APIException($validate->getError());
        };

        //判断该关注关系是否存在
        $is_concerned = $this->isConcerned($from_user_id,$data['to_user_id']);

        //返回数据
        $data = [
            'is_concerned' => $is_concerned,
        ];

        return api_result($data);
    }

    /**
     * 关注或者取消关注
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function concern(Request $request)
    {
        $data           = $request->data;
        $from_user_id   = $request->user_id;
        //验证参数
        $validate = Validate('api/UserConcerned');
        if(!$validate->scene('concern')->check($data)){
            throw new APIException($validate->getError());
        };
        $to_user_id = $data['to_user_id'];

        //查询关注关系是否存在
        $is_concerned = $this->isConcerned($from_user_id,$data['to_user_id']);

        if($is_concerned){
            //更新用户关注数目，并且检查用户是否存在
            try{
                $res = $this->userModel->userDec($from_user_id,'concerned_count');
                if(!$res) {
                    throw new APIException('增加关注数目失败');
                }
                $res = $this->userModel->userDec($to_user_id, 'fans_count');
                if(!$res){
                    throw new APIException('增加粉丝数目失败');
                }
            }catch(\Exception $e){
                throw new APIException('更新用户关注数目出错,请重新操作');
            }

            //取消关注联系
            try{
                $res = $this->userConcernedModel->remove($from_user_id,$to_user_id);
                if(!$res){
                    throw new APIException('取消关注失败');
                }
            }catch(\Exception $e){
                throw new APIException('取消关注失败');
            }
        }else{
            //创建关注联系
            $udata = [
                'from_user_id'  => $from_user_id,
                'to_user_id'    => $to_user_id
            ];

            //更新用户关注数目，并且检查用户是否存在
            try{
                $res = $this->userModel->userInc($from_user_id,'concerned_count');
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
                $res = $this->userModel->userInc($to_user_id,'fans_count');
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
            }catch(\Exception $e){
                throw new APIException('更新用户关注数目出错,或者没有该用户');
            }

            //插入数据
            try{
                $res = $this->userConcernedModel->add($udata);
                //检查
                if(!$res){
                    throw new APIException('关注失败');
                }
            }catch(\Exception $e){
                throw new APIException('关注失败');
            }
        }

        $rdata = [
            'is_concerned' => !$is_concerned,
        ];
        return api_result($rdata);
    }

    /**
     * 获取某用户所关注的用户信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getConcernedUser(Request $request)
    {
        $data           = $request->data;
        //验证参数
        $validate = Validate('api/UserConcerned');
        if(!$validate->scene('get_concerned')->check($data)){
            throw new APIException($validate->getError());
        };

        $from_user_id   = $data['from_user_id'];

        //获取总数目
        try{
            $total_count = $this->userConcernedModel->getConcernedUserCount($from_user_id);
        }catch(\Exception $e){
            throw new APIException('读取用户关注数目出错');
        }


        if($total_count == 0){
            throw new APIException('该用户暂时没有关注其他用户');
        }

        //初始化分页信息
        $this->initPaginateParams($total_count,$data);

        //获取用户关注的用户信息
        try{
            $res = $this->userConcernedModel->getConcernedUser($from_user_id,$this->from,$this->size);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('读取用户关注信息出错');
        }
        //完善用户信息
        //维护一个数组
        $user_info = [];
        foreach ($res as $user){
            if(isset($user['to_user_id'])){
                $user_id = $user['to_user_id'];
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
            'user_concerned'    => $user_info,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 获取关注某用户的用户信息
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getConcernUser(Request $request)
    {
        $data           = $request->data;
        //验证参数
        $validate = Validate('api/UserConcerned');
        if(!$validate->scene('get_concern')->check($data)){
            throw new APIException($validate->getError());
        };

        $to_user_id     = $data['to_user_id'];
        //获取总数目
        try{
            $total_count = $this->userConcernedModel->getConcernUserCount($to_user_id);
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
            $res = $this->userConcernedModel->getConcernUser($to_user_id,$this->from,$this->size);
            if(empty($res[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('读取关注用户信息出错');
        }
        //完善用户信息
        //维护一个数组
        $user_info = [];
        foreach ($res as $user){
            if(isset($user['from_user_id'])){
                $user_id = $user['from_user_id'];
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
            'user_concern'    => $user_info,
            'pagenate'          => $pagenate
        ];

        return api_result($rdata);
    }

    /**
     * 判断特定的关注关系是否存在
     * @param $from_user_id
     * @param $to_user_id
     * @return bool
     */
    private function isConcerned($from_user_id,$to_user_id){
        //查看该关注关系是否存在
        try{
            $res = $this->userConcernedModel->checkConcern($from_user_id,$to_user_id);
            if(!$res){
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
        return true;
    }

}