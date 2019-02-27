<?php
/**
 * 分页相关基础控制器
 * User: xjyplayer
 * Date: 2019/1/26
 * Time: 14:28
 */
namespace app\api\controller;

use think\Controller;

class PagerBase extends Controller
{
    protected $page;
    protected $size;
    protected $from;
    protected $totalCount;
    protected $totalPages;
    /**初始化分页变量,如果没有传入，则默认值为一页显示所有
     * @param $totalCount
     */
    protected function initPaginateParams($totalCount,$data)
    {
        //这个方法可以得到get内容
        $this->page = !empty($data["page"]) ? $data["page"] : 1;
        $this->size = !empty($data["size"]) ? $data["size"] : $totalCount;
        $this->from = ($this->page - 1) * $this->size;
        $this->totalCount = $totalCount;
        $this->totalPages = ceil($totalCount/$this->size);
    }
}