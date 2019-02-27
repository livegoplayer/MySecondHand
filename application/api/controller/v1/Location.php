<?php
/**
 * 获取地理位置相关
 * User: xjyplayer
 * Date: 2019/1/30
 * Time: 14:24
 */
namespace app\api\controller\v1;

use app\api\lib\exception\APIException;
use think\Controller;
use think\Request;

class Location extends Controller
{
    private $provinceModel;
    private $cityModel;
    private $regionModel;

    protected function initialize ()
    {
        $this->provinceModel    = model('common/Province');
        $this->cityModel        = model('common/City');
        $this->regionModel      = model('common/Region');
    }

    /**
     * 获取省列表
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getProvince(Request $request)
    {
        //获取省信息
        try{
            $province = $this->provinceModel->getProvince();
            if(!isset($province[0])){
                throw new APIException();
            }
        }catch(\Exception $e){
            throw new APIException('获取省信息出错');
        }

        //返回信息
        $rdata = [
            'provinces' => $province
        ];

        return api_result($rdata);
    }

    /**
     * 获取某省城市列表
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getCity(Request $request)
    {
        $data = $request->data;
        if(isset($data['pid'])) {
            $pid = $data['pid'];
            //获取city
            try{
                $city = $this->cityModel->getCity($pid);
            }catch(\Exception $e){
                throw new APIException('获取城市信息出错');
            }
            if(!isset($city[0])){
                throw new APIException('没有该省城市信息');
            }
        }elseif(isset($data['pname'])){
            $pname = $data['pname'];
            //获取city
            try{
                $city = $this->cityModel->getCityByName($pname);
                if(!isset($city[0])){
                    throw new APIException();
                }
            }catch(\Exception $e){
                throw new APIException('获取城市信息出错');
            }
        }

        //返回信息
        $rdata = [
            'city' => $city
        ];

        return api_result($rdata);
    }

    /**
     * 获取某城市区县列表
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getRegion(Request $request)
    {
        $data = $request -> data;
        if(isset($data['cid'])) {
            $cid = $data['cid'];
            //获取city
            try{
                $region = $this->regionModel->getRegion($cid);

            }catch(\Exception $e){
                throw new APIException('获取区县信息出错');
            }
            if(!isset($region[0])){
                throw new APIException('没有该区县信息');
            }
        }elseif(isset($data['cname'])){
            $cname = $data['cname'];
            //获取city
            try{
                $region = $this->regionModel->getRegionByName($cname);
                if(!isset($region[0])){
                    throw new APIException();
                }
            }catch(\Exception $e){
                throw new APIException('获取城市信息出错');
            }
        }

        //返回信息
        $rdata = [
            'region' => $region
        ];

        return api_result($rdata);
    }

    /**
     * 解析location_id
     * @param Request $request
     * @return \think\response\Json
     * @throws APIException
     */
    public function getLocationInfo(Request $request)
    {
        $data = $request->data;
        $validate = Validate('Location');
        if(!$validate->scene('parse_location')->check($data)){
            throw new APIException($validate->getError());
        };

        $location_id = $data['location_id'];
        //如果是区县id
        if($location_id > 10000000 && $location_id < 99999999){
            try{
                $regionInfo = $this->regionModel->getRegionInfo($location_id);
            }catch(\Exception $e){
                throw new APIException('获取区县信息出错');
            }
            if(empty($regionInfo))
            {
                throw new APIException('不存在该地理位置信息');
            }

            //获取省信息
            try{
                $cityInfo = $this->cityModel->getCityInfo($regionInfo['cid']);
            }catch(\Exception $e){
                throw new APIException('获取区县信息的省信息出错');
            }

            if(empty($cityInfo))
            {
                throw new APIException('不存在该地理位置信息');
            }

            $locationInfo = [
                'province'  => [
                    'pid'   => $cityInfo['pid'],
                    'pname' => $cityInfo['pname']
                ],

                'city'      => [
                    'cid'   => $regionInfo['cid'],
                    'cname' => $regionInfo['cname']
                ],

                'region'    => [
                    'rid'   => $regionInfo['rid'],
                    'rname' => $regionInfo['rname']
                ]
            ];
        }elseif($location_id > 10000 && $location_id < 99999){
            try{
                $cityInfo = $this->cityModel->getCityInfo($location_id);
            }catch(\Exception $e){
                throw new APIException('获取城市信息失败');
            }
            if(empty($cityInfo))
            {
                throw new APIException('不存在该地理位置信息');
            }

            $locationInfo = [
                'province'  => [
                    'pid'   => $cityInfo['pid'],
                    'pname' => $cityInfo['pname']
                ],

                'city'      => [
                    'cid'   => $cityInfo['cid'],
                    'cname' => $cityInfo['cname']
                ],
            ];
        }elseif($location_id < 100 && $location_id > 0){
            try{
                $provinceInfo = $this->provinceModel->getProvinceInfo($location_id);
            }catch(\Exception $e){
                throw new APIException('获取省信息失败');
            }
            if(empty($provinceInfo))
            {
                throw new APIException('不存在该地理位置信息');
            }

            $locationInfo = [
                'province'  => [
                    'pid'   => $provinceInfo['pid'],
                    'pname' => $provinceInfo['pname']
                ],
            ];

        } else{
            throw new APIException('提交的location_id不符合要求');
        }

        //返回信息
        $rdata = [
            'locationInfo'  =>  $locationInfo
        ];

        return api_result($rdata);
    }
}