<?php
/**
 * 类别相关
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 14:24
 */
namespace app\api\controller\v1;

use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

class Category extends Controller
{

    private $categoryModel;
    protected function initialize ()
    {
        $this->categoryModel = model('common/Category');
    }

    /**
     * 获取第一级分类列表
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getFirstCategory(Request $request)
    {
        //获取省信息
        try{
            $category = $this->categoryModel->getFirstCategory();
            if(!isset($category[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取省信息出错');
        }

        //返回信息
        $rdata = [
            'first_category' => $category
        ];

        return api_result($rdata);
    }

    /**
     * 获取某分类的下级列表
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getChild(Request $request)
    {
        $data = $request->data;
        $validate = Validate('Category');
        if(!$validate->scene('get_child')->check($data)){
            throw new APIException($validate->getError());
        };
        $parent_id  = $data['parent_id'];

        //获取子栏目信息
        try{
            $category = $this->categoryModel->getCategory($parent_id);
        }catch(\Exception $e){
            throw new APIException('获取子栏目出错');
        }

        if(!isset($category[0])){
            throw new APIException('暂无子栏目信息');
        }
        
        //返回信息
        $rdata = [
            'child_category' => $category
        ];

        return api_result($rdata);
    }

    /**
     * 解析category_id
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getCategoryInfo(Request $request)
    {
        $data = $request->data;
        $validate = Validate('api/Category');
        if(!$validate->scene('get_category_detail')->check($data)){
            throw new APIException($validate->getError());
        };

        $category_id = $data['category_id'];
        $category = [];
        do{
            try{
                $res = $this->categoryModel->field(['id','name','parent_id'])->get($category_id);
            }catch(\Exception $e){
                throw new APIException('获取类别出错');
            }
            if(isset($res['parent_id'])){
                $category_id = $res['parent_id'];
            }else{
                throw new APIException('获取类别出错');
            }
            $category[] = $res;
        } while($category_id != 0);


        //返回信息
        $rdata = [
            'category'  =>  $category
        ];

        return api_result($rdata);
    }

    /**
     * 新增类别
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function addCategory(Request $request)
    {
        $data = $request->data;
        $validate = Validate('api/Category');
        if(!$validate->scene('add_category')->check($data)){
            throw new APIException($validate->getError());
        };

        $sdata = [
            'parent_id' => $data['parent_id'],
            'name'      => $data['name']
        ];

        try{
            $res = $this->categoryModel->add($sdata);
            if(!$res){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('添加类别出错');
        }

        return api_result();
    }

}