<?php
namespace Amap;
/**
 * 高德地图web服务php-sdk,官方api
 * @author lvzhao1995<abc-1.2@qq.com>
 * @link https://github.com/lvzhao1995/AmapSdk
 * @version 0.1
 *  usage:
 *   $options = [
 *			'sign'=>false, //是否进行数字签名，可选
 *			'private_key'=>'', //数字签名私钥，sign=true时必填
 *			'key'=>'wxdk1234567890', //api调用key，必填
 *		];
 */
class amap
{

    const API_URL = 'http://restapi.amap.com/v3';

    const GEO_URL = '/geocode/geo?';

    const REGEO_URL = '/geocode/regeo?';

    const WALK_URL = '/direction/walking?';

    const BUS_URL = '/direction/transit/integrated?';

    const DRIVE_URL = '/direction/driving?';

    const DISTANCE_URL = '/distance?';

    private $sign = false;

    private $private_key;

    private $key;

    public $errCode;

    public $errMsg;

    public function __construct($options)
    {
        $this->sign = isset($options['sign']) ? $options['sign'] : false;
        $this->private_key = isset($options['private_key']) ? $options['private_key'] : '';
        $this->key = isset($options['key']) ? $options['key'] : '';
    }

    /**
     * 地理编码
     * @param string $address 结构化地址信息，规则： 省+市+区+街道+门牌号
     * @param string $city  查询城市，可选参数
     * @return boolean|array 成功返回结果
     * [
     *    'status'=>1,
     *    'count'=>1,//结果数
     *    'info'=>'ok',
     *    'geocodes'=>[
     *      'formatted_address'=>'省+市+区+街道+门牌号',
     *      'province'=>'省份',
     *      'city'=>'城市',
     *      'citycode'=>'城市编码',
     *      'district'=>'所在区',
     *      'township'=>'乡镇',
     *      'street'=>'街道',
     *      'number'=>'门牌',
     *      'adcode'=>'区域编码',
     *      'location'=>'经度,纬度'，
     *      'level'=>'匹配级别'
     *    ]
     * ]
     */
    public function geo($address, $city = '')
    {
        $data = [
            'key' => $this->key,
            'address' => $address
        ];
        if ($city != '')
            $data['city'] = $city;
        $paramStr = http_build_query($data);
        $url = self::API_URL . self::GEO_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 逆地理编码
     * @param string $location 经纬度坐标，多个坐标用'|'分隔，最多20个
     * @param array $ops 可选参数，仅传入非默认值参数
     * [
     *     'poitype'=>'1000',     //poi类型，多值用'|'分隔，extensions=all时生效，不支持batch=true
     *     'radius'=>'1000',      //搜索半径
     *     'extensions'=>'base',  //返回结果控制，'base'基本信息，'all'详细信息
     *     'batch'=>'false',      //批量查询控制，batch=true为批量查询。batch=false为单点查询，batch=false时即使传入多个点也只返回第一个点结果
     *     'roadlevel'=>'',       //道路等级，默认无值，可选值1，当为1时，仅输出主干道数据
     *     'homeorcorp'=>'0'      //poi返回顺序，0不优化，1居家相关优先，2公司相关优先
     * ]
     * @return boolean|array 成功返回结果
     * 内容参考http://lbs.amap.com/api/webservice/guide/api/georegeo/#regeo
     */
    public function regeo($location, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['location'] = $location;
        $ops['output'] = 'json';
        $paramStr = http_build_query($ops);
        $url = self::API_URL . self::REGEO_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 步行路径规划
     * @param string $origin       出发点，经度，纬度
     * @param string $destination  目的地，经度，纬度
     * @return boolean|array   成功返回数组，
     * 内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#walk
     */
    public function walking($origin, $destination)
    {
        $data = [
            'origin' => $origin,
            'destination' => $destination
        ];
        $paramStr = http_build_query($data);
        $url = self::API_URL . self::WALK_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 公交路径规划
     * @param string $origin        出发点         经度，纬度
     * @param string $destination   目的地        经度，纬度
     * @param string $city          城市|跨城时起点城市          可选值：城市名称|citycode
     * @param array $ops            其他可选参数
     * [
     *      'cityd'=>'',            //无默认，跨城规划时必填，终点城市         可选值:城市名称|citycode
     *      'extensions'=>'base',   //返回结果详略，base:返回基本信息；all：返回全部信息
     *      'strategy'=>0,          //公交换乘策略，可选值：0：最快捷模式,1：最经济模式,2：最少换乘模式,3：最少步行模式,5：不乘地铁模式
     *      'nightflag'=>0,         //是否计算夜班车，可选值：0：不计算夜班车,1：计算夜班车
     *      'date'=>'',             //出发日期，格式：date=2014-3-19
     *      'time'=>'',             //出发时间，格式：time=22:34
     * ]
     * @return boolean|array     成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#bus
     */
    public function bus($origin, $destination, $city, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['origin'] = $origin;
        $ops['destination'] = $destination;
        $ops['city'] = $city;
        $ops['output'] = 'json';
        $paramStr = http_build_query($ops);
        $url = self::API_URL . self::BUS_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 驾车路径规划
     * @param string $origin       出发点  经度，纬度
     * @param string $destination  目的地  经度，纬度
     * @param array $ops           其他可选参数
     * [
     *     'originid'=>'',          //无默认值，出发点poiid
     *     'destinationid'=>'',     //无默认值，目的地poiid
     *     'strategy'=>0,           //驾车选择策略。
     *                   0速度优先（时间），1费用优先（不走收费路段的最快道路），
     *                   2距离优先，3不走快速路，4躲避拥堵，
     *                   5多策略（同时使用速度优先、费用优先、距离优先三个策略计算路径）。其中必须说明，就算使用三个策略算路，会根据路况不固定的返回一~三条路径规划信息。
     *                   6不走高速，7不走高速且避免收费，8躲避收费和拥堵，9不走高速且躲避收费和拥堵
     *     'waypoints'=>'',         //途经点，坐标之间用';'分隔。最大16个坐标点。如果输入多个途径点，则按照用户输入的顺序进行路径规划 
     *     'avoidpolygons'=>'',     //避让区域，坐标之间用';'分隔，区域之间用'|'分隔。支持最多32个区域，每区域最多16个顶点
     *     'avoidroad'=>'',         //避让道路名，只支持一条道路。同时传入道路和区域只支持避让道路。
     *     'province'=>'',          //用汉字传入车牌省份判断限行，如'京'
     *     'number'=>'',            //除省份及标点之外车牌的字母和数字。判断限行相关
     * ]
     * @return boolean|array   成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#driving
     */
    public function drive($origin, $destination, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['origin'] = $origin;
        $ops['destination'] = $destination;
        $ops['output'] = 'json';
        if (isset($ops['number'])) {
            $ops['number'] = strtoupper($ops['number']);
        }
        $paramStr = http_build_query($ops);
        $url = self::API_URL . self::DRIVE_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }
    
    /**
     * 距离测量
     * @param string $origins          出发点，支持100个坐标对，坐标对之间用'|'分隔
     * @param string $destination     目的地，规则： lon,lat（经度,纬度）,支持一个
     * @param number $type            路径计算的方式和方法,0：直线距离,1：驾车导航距离（仅支持国内坐标）。其中必须特别指出，当为1 的时候会考虑路况因素，故返回结果可能存在不同
     *                                2：公交规划距离（仅支持同城坐标）,3：步行规划距离（仅支持5km之间的距离）
     * @return boolean|array          成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#distance
     */
    public function distance($origins,$destination,$type=1){
        $data=[
            'origins'=>$origins,
            'destination'=>$destination,
            'type'=>$type
        ];
        $paramStr = http_build_query($data);
        $url = self::API_URL . self::DISTANCE_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (! $json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 数字签名算法
     * @param array $data
     * @return string
     */
    private function signature($data = [])
    {
        ksort($data, SORT_STRING);
        $tmpStr = http_build_query($data);
        $tmpStr .= $this->private_key;
        $signStr = md5($tmpStr);
        return $signStr;
    }

    /**
     * GET 请求
     *
     * @param string $url            
     */
    private function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}