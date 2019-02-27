<?php
namespace app\api\controller\v1;
use app\common\lib\auth\IAuth;
use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

/**
 * 登录接口逻辑
 * User: xjyplayer
 * Date: 2019/1/11
 * Time: 20:02
 */

class Login extends Controller
{
    private $user_model;

    protected function initialize()
    {
        $this->user_model = model('User');
    }

    /**
     * 登录
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function login(Request $request)
    {
        $data = $request->data;
//        halt($data);
        //验证传递的字段
        $validate = Validate('api/Login');
        if(!$validate->scene('we_chat_login')->check($data)){
            throw new APIException($validate->getError(),403);
        };
        $openid = $data['openid'];
        //搜索用户账号
        try{
            $res = $this->user_model->checkOpenID($openid);
        }catch(\Exception $e){
            throw new APIException($e->getFile().$e->getLine().$e->getMessage());
        }
        //如果没有注册
        if(!$res){
                //验证传递的字段
                $validate = Validate('api/Login');
                if(!$validate->scene('we_chat_register')->check($data)){
                    throw new APIException($validate->getError(),403);
                };
                //数据重新组装入库
                $udata = [
                    'openid'    => $data['openid'],
                    'avatar'    => $data['avatar'],
                    'nickname'  => $data['nickname'],
                    'gender'    => $data['gender'],
                ];
            try{
                $res = $this->user_model->add($udata);
            }catch(\Exception $e){
                throw new APIException($e->getFile().$e->getLine().$e->getMessage());
            }
            //如果注册失败
            if(!$res){
                throw new APIException('用户不存在,且注册失败,请重新请求');
            }
        }
        //如果已经注册或者注册成功
        $user_id = $res;

        //创建JWTToken
        try{
            $token_data = [
                'user_id' => $user_id,
            ];
            $access_token = IAuth::createJWTToken($token_data);
        }catch(\Exception $e){
            throw new APIException($e->getFile().$e->getLine().$e->getMessage());
        }
        //返回
        $return_data = [
            'access_token' => $access_token,
        ];
        return api_result($return_data,'成功获取token');
    }


    /**
     * 登出
     * @param Request $request
     * @return \think\response\Json
     *
     */
    public function logout(Request $request){
        return api_result();
    }
}
