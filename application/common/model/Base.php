<?php
/**
 * 模型类的基础功能
 * User: xjyplayer
 * Date: 2019/1/11
 * Time: 20:34
 */
namespace app\common\model;

use think\Model;

class Base extends Model
{

    /**
     * 添加一条数据的功能
     * @param $data
     * @return mixed
     */
    public function add($data){
        if(!isset($data['status'])) {
            $data['status'] = config("code.rev");               //待审核状态
        }
        $this->allowField(true)->save($data);
        return $this->id;
        //返回id
    }

    /**
     * 利用带id的数据进行更新
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function upgrade($data)
    {
        if(!isset($data['id'])){
            Exception("没有传入id,无法更新");
        }
        $this->allowField(true)->save($data,['id' => intval($data['id'])]);
        return $this->id;
    }

    /**
     * 专门修改状态的方法
     * @param $id
     * @param $status
     * @return bool
     */
    public function statusChange($id,$status){
        $data = [
            'id'    => $id,
            'status'=> $status,
        ];

        $this->allowField(true)->save($data,['id' => intval($data['id'])]);
        return $this->id;
    }



}