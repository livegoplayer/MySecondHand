<?php
/**
 * 商品评论
 * User: xjyplayer
 * Date: 2019/2/20
 * Time: 13:59
 */
namespace app\api\controller\v1;

use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

class GoodsComments extends Controller
{
    private $goodsModel ;
    private $goodsCommentsModel;

    public function initialize()
    {
        $this->goodsModel           = model('common/Goods');
        $this->goodsCommentsModel   = model('common/GoodsComments');
    }

    /**
     * 添加用户评论
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function goodsComments(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/GoodsComments');
        if(!$validate->scene('goods_comments')->check($data)){
            throw new APIException($validate->getError());
        };
        $goods_id = $data['goods_id'];

        //更新回复数目，并且检查商品是否存在
        try{
            $res = $this->goodsModel->goodsInc($goods_id,'reply_count');
            if(!$res) {
                throw new APIException('增加点赞数目失败');
            }
        }catch(\Exception $e){
            throw new APIException('更新商品评论数目出错,请重新操作');
        }
        //插入数据
        $idata = [
            'user_id'       => $my_user_id,
            'goods_id'      => $goods_id,
            'reply_type'    => $data['reply_type'],
            'reply_id'      => $data['reply_id'],
            'content'       => $data['content'],
        ];

        try{
            $res = $this->goodsCommentsModel->add($idata);
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
    public function goodsCommentsDelete(Request $request)
    {
        $data           = $request->data;
        $my_user_id     = $request->user_id;
        //验证参数
        $validate = Validate('api/GoodsComments');
        if(!$validate->scene('goods_comments_delete')->check($data)){
            throw new APIException($validate->getError());
        };
        $user_id    = $data['user_id'];
        $comment_id   = $data['comment_id'];

        //为了以后扩展方方便
        if($my_user_id != $user_id){
            throw new APIException('没有权限删除');
        }

        //删除数据
        try{
            $res = $this->goodsCommentsModel->remove($comment_id);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('删除出错');
        }

        return api_result();
    }
}