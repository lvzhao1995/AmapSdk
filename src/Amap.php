<?php

namespace Amap;

/**
 * 高德地图web服务php-sdk,官方api
 *
 * @author lvzhao1995<abc-1.2@qq.com>
 * @link https://github.com/lvzhao1995/AmapSdk
 * @version 0.1
 *          usage:
 *          $options = [
 *          'sign'=>false, //是否进行数字签名，可选
 *          'private_key'=>'', //数字签名私钥，sign=true时必填
 *          'key'=>'wxdk1234567890', //api调用key，必填
 *          ];
 */

// TODO 静态地图没有实现
class Amap
{

    const API_V3_URL = 'http://restapi.amap.com/v3';

    const API_V4_URL = 'http://restapi.amap.com/v4';

    const GEO_URL = '/geocode/geo?';

    const REGEO_URL = '/geocode/regeo?';

    const WALK_URL = '/direction/walking?';

    const BUS_URL = '/direction/transit/integrated?';

    const DRIVE_URL = '/direction/driving?';

    const BICYCLING_URL = '/direction/bicycling?';

    const DISTANCE_URL = '/distance?';

    const DISTRICT_URL = '/config/district?';

    const TEXT_SEARCH_URL = '/place/text?';

    const AROUND_SEARCH_URL = '/place/around?';

    const POLYGON_SEARCH_URL = '/place/polygon?';

    const ID_SRARCH_URL = '/place/detail?';

    const IP_URL = '/ip?';

    const GRASP_URL = '/autograsp?';

    const CONVERT_URL = '/assistant/coordinate/convert?';

    const WEATHER_URL = '/weather/weatherInfo?';

    const INPUT_TIPS_URL = '/assistant/inputtips?';

    const YUNTU_API_V3_URL = 'http://yuntuapi.amap.com';

    const YUNTU_CREATE_TABLE_URL = '/datamanage/table/create';

    const YUNTU_CREATE_DATA_URL = '/datamanage/data/create';

    const YUNTU_BATCH_CREATE_DATA_URL = '/datamanage/data/batchcreate';

    const YUNTU_UPDATE_DATA_URL = '/datamanage/data/update';

    const YUNTU_DELETE_DATA_URL = '/datamanage/data/delete';

    const YUNTU_IMPORT_STATUS_URL = '/datamanage/batch/importstatus?';

    private $sign = false;

    private $private_key;

    private $key;

    public $errCode=0;

    public $errMsg;

    public $tableid;

    public function __construct($options)
    {
        $this->sign = isset($options['sign']) ? $options['sign'] : false;
        $this->private_key = isset($options['private_key']) ? $options['private_key'] : '';
        $this->key = isset($options['key']) ? $options['key'] : '';
    }

    /**
     * 地理编码
     *
     * @param string $address
     *            结构化地址信息，规则： 省+市+区+街道+门牌号
     * @param string $city
     *            查询城市，可选参数
     * @return boolean|array 成功返回结果
     *         [
     *         'status'=>1,
     *         'count'=>1,//结果数
     *         'info'=>'ok',
     *         'geocodes'=>[
     *         'formatted_address'=>'省+市+区+街道+门牌号',
     *         'province'=>'省份',
     *         'city'=>'城市',
     *         'citycode'=>'城市编码',
     *         'district'=>'所在区',
     *         'township'=>'乡镇',
     *         'street'=>'街道',
     *         'number'=>'门牌',
     *         'adcode'=>'区域编码',
     *         'location'=>'经度,纬度'，
     *         'level'=>'匹配级别'
     *         ]
     *         ]
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
        $url = self::API_V3_URL . self::GEO_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 逆地理编码
     *
     * @param string $location
     *            经纬度坐标，多个坐标用'|'分隔，最多20个
     * @param array $ops
     *            可选参数，仅传入非默认值参数
     *            [
     *            'poitype'=>'1000', //poi类型，多值用'|'分隔，extensions=all时生效，不支持batch=true
     *            'radius'=>'1000', //搜索半径
     *            'extensions'=>'base', //返回结果控制，'base'基本信息，'all'详细信息
     *            'batch'=>'false', //批量查询控制，batch=true为批量查询。batch=false为单点查询，batch=false时即使传入多个点也只返回第一个点结果
     *            'roadlevel'=>'', //道路等级，默认无值，可选值1，当为1时，仅输出主干道数据
     *            'homeorcorp'=>'0' //poi返回顺序，0不优化，1居家相关优先，2公司相关优先
     *            ]
     * @return boolean|array 成功返回结果
     *         内容参考http://lbs.amap.com/api/webservice/guide/api/georegeo/#regeo
     */
    public function regeo($location, $ops = [])
    {
        $ops['key'] = $this->key;
        $this->dealOps($ops, 'batch');
        $ops['location'] = $location;
        $ops['output'] = 'json';
        unset($ops['callback']);
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::REGEO_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 步行路径规划
     *
     * @param string $origin
     *            出发点，经度，纬度
     * @param string $destination
     *            目的地，经度，纬度
     * @return boolean|array 成功返回数组，
     *         内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#walk
     */
    public function walking($origin, $destination)
    {
        $data = [
            'origin' => $origin,
            'destination' => $destination
        ];
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::WALK_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 公交路径规划
     *
     * @param string $origin
     *            出发点 经度，纬度
     * @param string $destination
     *            目的地 经度，纬度
     * @param string $city
     *            城市|跨城时起点城市 可选值：城市名称|citycode
     * @param array $ops
     *            其他可选参数
     *            [
     *            'cityd'=>'', //无默认，跨城规划时必填，终点城市 可选值:城市名称|citycode
     *            'extensions'=>'base', //返回结果详略，base:返回基本信息；all：返回全部信息
     *            'strategy'=>0, //公交换乘策略，可选值：0：最快捷模式,1：最经济模式,2：最少换乘模式,3：最少步行模式,5：不乘地铁模式
     *            'nightflag'=>0, //是否计算夜班车，可选值：0：不计算夜班车,1：计算夜班车
     *            'date'=>'', //出发日期，格式：date=2014-3-19
     *            'time'=>'', //出发时间，格式：time=22:34
     *            ]
     * @return boolean|array 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#bus
     */
    public function bus($origin, $destination, $city, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['origin'] = $origin;
        $ops['destination'] = $destination;
        $ops['city'] = $city;
        $ops['output'] = 'json';
        unset($ops['callback']);
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::BUS_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 驾车路径规划
     *
     * @param string $origin
     *            出发点 经度，纬度
     * @param string $destination
     *            目的地 经度，纬度
     * @param array $ops
     *            其他可选参数
     *            [
     *            'originid'=>'', //无默认值，出发点poiid
     *            'destinationid'=>'', //无默认值，目的地poiid
     *            'strategy'=>0, //驾车选择策略。
     *            0速度优先（时间），1费用优先（不走收费路段的最快道路），
     *            2距离优先，3不走快速路，4躲避拥堵，
     *            5多策略（同时使用速度优先、费用优先、距离优先三个策略计算路径）。其中必须说明，就算使用三个策略算路，会根据路况不固定的返回一~三条路径规划信息。
     *            6不走高速，7不走高速且避免收费，8躲避收费和拥堵，9不走高速且躲避收费和拥堵
     *            'waypoints'=>'', //途经点，坐标之间用';'分隔。最大16个坐标点。如果输入多个途径点，则按照用户输入的顺序进行路径规划
     *            'avoidpolygons'=>'', //避让区域，坐标之间用';'分隔，区域之间用'|'分隔。支持最多32个区域，每区域最多16个顶点
     *            'avoidroad'=>'', //避让道路名，只支持一条道路。同时传入道路和区域只支持避让道路。
     *            'province'=>'', //用汉字传入车牌省份判断限行，如'京'
     *            'number'=>'', //除省份及标点之外车牌的字母和数字。判断限行相关
     *            ]
     * @return boolean|array 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#driving
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
        unset($ops['callback']);
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::DRIVE_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 骑行路径规划
     *
     * @param string $origin
     *            出发点 经度，纬度
     * @param string $destination
     *            目的地 经度，纬度
     * @return boolean|array 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction#t8
     */
    public function bicycling($origin, $destination)
    {
        $ops=[
            'key'=>$this->key,
            'origin'=>$origin,
            'destination'=>$destination
        ];
        $paramStr = http_build_query($ops);
        $url = self::API_V4_URL . self::BICYCLING_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['errcode'] != 0) {
                $this->errMsg = $json['errdetail'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 距离测量
     *
     * @param string $origins
     *            出发点，支持100个坐标对，坐标对之间用'|'分隔
     * @param string $destination
     *            目的地，规则： lon,lat（经度,纬度）,支持一个
     * @param int $type
     *            路径计算的方式和方法,0：直线距离,1：驾车导航距离（仅支持国内坐标）。其中必须特别指出，当为1 的时候会考虑路况因素，故返回结果可能存在不同
     *            2：公交规划距离（仅支持同城坐标）,3：步行规划距离（仅支持5km之间的距离）
     * @return bool|mixed 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/direction/#distance
     */
    public function distance($origins, $destination, $type = 1)
    {
        $data = [
            'origins' => $origins,
            'destination' => $destination,
            'type' => $type
        ];
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::DISTANCE_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 行政区域查询
     *
     * @param array $ops
     *            参数数组，此接口所有参数均为可选
     *            [
     *            'keywords'=>'', //查询关键字,只支持单个关键词，可用政区名称、citycode、adcode
     *            'subdistrict'=>1, //子级行政区，0：不返回下级行政区；1：返回下一级行政区；并以此类推
     *            //行政区级别包括：国家、省/直辖市、市、区/县、商圈、街道多级数据，其中街道数据仅在keywords为区/县、商圈的时候显示
     *            'page'=>1, //需要第几页数据，最外层的districts最多会返回20个数据
     *            'offset'=>20,//最外层返回数据个数
     *            'extensions'=>'base', //此项控制行政区信息中返回行政区边界坐标点；
     *            //base:不返回行政区边界坐标点；all:只返回当前查询district的边界值，不返回子节点的边界值；
     *            'filter'=>'', //根据区划过滤，填入后只返回该省/直辖市信息，填入adcode
     *            ]
     * @return boolean|array 成功返回结果，内容参考http://lbs.amap.com/api/webservice/guide/api/district/#district
     */
    public function district($ops = [])
    {
        $ops['key'] = $this->key;
        $ops['output'] = 'json';
        unset($ops['callback']);
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::DISTRICT_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 关键字搜索
     *
     * @param string $keywords
     *            查询关键字，多个关键字用'|'分隔
     * @param string $types
     *            查询POI类型，多个类型用'|'分隔
     *            keywords和types两者至少必选其一
     *            POI分类编码表下载http://a.amap.com/lbs/static/zip/AMap_API_Table.zip
     * @param array $ops
     *            可选参数，列表如下
     *            [
     *            'city'=>'', //查询城市,可选值：城市中文、中文全拼、citycode、adcode
     *            'citylimit'=>'false' //仅返回指定城市数据，可选值：true/false
     *            'children'=>'' //是否按照层级展示子POI数据，可选值：1/''
     *            'offset'=>20, //每页记录数据，最大值25，超出最大值按最大值处理
     *            'page'=>1, //当前页数，最大翻页数100
     *            'building'=>'', //建筑物POI编号，传入后只在该建筑物之内进行搜索
     *            'floor'=>'', //传入楼层必须有建筑物id，否则报错。如果当前楼层有结果则返回，否则返回该建筑物搜索结果
     *            'extensions'=>'base' //返回结果控制，base返回基本地址信息，all返回地址信息、附近POI、道路及道路交叉口
     *            ]
     * @return boolean|array 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/search/#text
     */
    public function textSearch($keywords = '', $types = '', $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['output'] = 'json';
        unset($ops['callback']);
        $this->dealOps($ops, 'citylimit');
        if ($keywords != '') {
            $ops['keywords'] = $keywords;
        }
        if ($types != '') {
            $ops['types'] = $types;
        }
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::TEXT_SEARCH_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 周边搜索
     *
     * @param string $location
     *            中心点坐标，经度，纬度
     * @param array $ops
     *            可选参数
     *            [
     *            'keywords'=>'', //查询关键字，多个关键字用'|'分隔
     *            'types'=>'', //查询POI类型，多个类型用'|'分隔，POI分类编码表下载http://a.amap.com/lbs/static/zip/AMap_API_Table.zip
     *            'city'=>'', //查询城市，可选值：城市中文、中文全拼、citycode、adcode
     *            'radius'=>3000, //查询半径，取值范围0~50000，大于50000按默认值，单位米
     *            'sortrule'=>'distance', //按距离排序：distance；综合排序：weight
     *            'offset'=>20, //每页记录数，最大值25，超出范围按最大值处理
     *            'page'=>1, //当前页数，最大翻页数100
     *            'extensions'=>'base', //base返回基本地址信息；取值为all返回地址信息、附近POI、道路以及道路交叉口信息。
     *            ]
     * @return boolean|array 成功返回数组，内容参考http://lbs.amap.com/api/webservice/guide/api/search/#around
     */
    public function aroundSearch($location, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['location'] = $location;
        $ops['output'] = 'json';
        unset($ops['callback']);
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::AROUND_SEARCH_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 多边形搜索
     *
     * @param string $polygon
     *            经纬度坐标对（经度，纬度），坐标对用';'分隔。矩形可传入左上右下两坐标对，其他情况首位坐标对需相同
     * @param array $ops
     *            可选参数
     *            [
     *            'keywords'=>'', //查询关键字，多个关键字用'|'分隔
     *            'types'=>'', //查询POI类型，多个类型用'|'分隔，POI分类编码表下载http://a.amap.com/lbs/static/zip/AMap_API_Table.zip
     *            'offset'=>20, //每页记录数，最大值25，超出范围按最大值处理
     *            'page'=>1, //当前页数，最大翻页数100
     *            'extensions'=>'base', //base返回基本地址信息；取值为all返回地址信息、附近POI、道路以及道路交叉口信息。
     *            ]
     * @return boolean|array 成功返回结果数组，内容参考http://lbs.amap.com/api/webservice/guide/api/search/#polygon
     */
    public function polygon($polygon, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['polygon'] = $polygon;
        $ops['output'] = 'json';
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::POLYGON_SEARCH_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * id搜索
     *
     * @param string|integer $id
     *            兴趣点id
     * @return boolean|array 成功返回数组，内容与关键字搜索类似，参考http://lbs.amap.com/api/webservice/guide/api/search/#text
     */
    public function idSearch($id)
    {
        $data['key'] = $this->key;
        $data['id'] = $id;
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::ID_SRARCH_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * IP定位
     *
     * @param string $ip
     *            需要搜索的ip，仅支持国内。不传参数取接口请求ip
     * @param boolean $all
     *            结果详略，true返回数组，false返回省市拼接字符串
     * @return boolean|array|string 成功返回结果。国外ip或非法ip返回空。数组内容参考http://lbs.amap.com/api/webservice/guide/api/ipconfig/#ip
     */
    public function ip($ip = '', $all = false)
    {
        $data['key'] = $this->key;
        if ($ip != '') {
            $data['ip'] = $ip;
        }
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::IP_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errCode = $json['infocode'];
                $this->errMsg = $json['info'];
                return false;
            }
            if ($all) {
                return $json;
            } else {
                return ($json['province'] == [] ? '' : $json['province']) . ($json['city'] == [] ? '' : $json['city']);
            }
        }
        return false;
    }

    /**
     * 抓路服务
     *
     * @param string $carid
     *            车辆唯一标识。无需填写key后四位
     * @param string $locations
     *            用于抓路的经纬度坐标，要求每次传入按utc时间排列的三个或三个以上经纬度坐标，
     *            且相邻经纬度gps时间间隔小于300s，距离小于1km。
     *            格式为：X1,Y1;X2,Y2……，最多支持20个经纬度
     * @param string $time
     *            utc时间，与坐标点一一对应，时间个数与坐标点个数一致，用逗号分隔
     * @param string $direction
     *            行驶方向，正北为0度，与坐标点个数一致并一一对应。用逗号分隔。
     * @param string $speed
     *            行驶速度，单位km/h。与坐标点个数一致并一一对应。用逗号分隔
     * @return boolean|array 成功返回结果，内容参考 http://lbs.amap.com/api/webservice/guide/api/autograsp/#autograsp
     */
    public function autograsp($carid, $locations, $time, $direction, $speed)
    {
        $data = array(
            'key' => $this->key,
            'carid' => substr($this->key, -4) . $carid,
            'locations' => $locations,
            'time' => $time,
            'direction' => $direction,
            'speed' => $speed
        );
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::GRASP_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 坐标转换。其他坐标系转为高德坐标
     *
     * @param string $locations
     *            坐标点，经度在前。多对坐标用分号分隔，最多支持40对坐标。
     * @param string $coordsys
     *            原坐标系。可选值：gps;mapbar;baidu;autonavi(不进行转换)
     * @return string|boolean 转换后的坐标，如果有多对坐标，则用分号分隔
     */
    public function convert($locations, $coordsys = 'autonavi')
    {
        if ($coordsys == 'autonavi') {
            return $locations;
        }
        $data = array(
            'key' => $this->key,
            'locations' => $locations,
            'coordsys' => $coordsys
        );
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::CONVERT_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json['locations'];
        }
        return false;
    }

    /**
     * 天气查询
     *
     * @param string $city
     *            城市名称，填写adcode
     * @param string $extensions
     *            气象类型，base:返回实况天气，all:返回预报天气
     * @return boolean|array 成功返回数组，内容参考 http://lbs.amap.com/api/webservice/guide/api/weatherinfo/#weatherinfo
     */
    public function weather($city, $extensions = 'base')
    {
        $data = array(
            'key' => $this->key,
            'city' => $city,
            'extensions' => $extensions,
            'output' => 'json'
        );
        $paramStr = http_build_query($data);
        $url = self::API_V3_URL . self::WEATHER_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($data);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 输入提示
     *
     * @param string $keywords
     *            查询关键词
     * @param array $ops
     *            可选参数
     *            [
     *            'type'=>'', //POI分类，可选分类名称或分类代码，建议使用分类代码
     *            'location'=>'', //坐标，格式：“X,Y”（经度,纬度），不可以包含空格
     *            'city'=>'', //搜索城市，可选值：城市中文、中文全拼、citycode、adcode。不填则在全国范围内搜索。
     *            'citylimit'=>false, //仅返回指定城市数据，可选值true/false
     *            'datatype'=>'all', //可选值：all-返回所有数据类型、poi-返回POI数据类型、bus-返回公交站点数据类型、busline-返回公交线路数据类型
     *            ]
     * @return boolean|array 成功返回结果数组，内容参考http://lbs.amap.com/api/webservice/guide/api/inputtips#inputtips
     */
    public function inputtips($keywords, $ops = [])
    {
        $ops['key'] = $this->key;
        $ops['keywords'] = $keywords;
        $ops['output'] = 'json';
        $this->dealOps($ops, 'citylimit');
        $paramStr = http_build_query($ops);
        $url = self::API_V3_URL . self::INPUT_TIPS_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 调用云图接口创建表
     *
     * @param string $name
     *            表名,最长50字符
     * @return boolean|string 成功返回tableid,失败返回false
     */
    public function tableCreate($name)
    {
        $ops['key'] = $this->key;
        if (mb_strlen($name) > 50) {
            $this->errMsg = '表名不能超过50字符';
            return false;
        }
        $ops['name'] = $name;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_V3_URL . self::YUNTU_CREATE_TABLE_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] !== 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json['tableid'];
        }
        return false;
    }

    /**
     * 设置tableid，供接下来调用云图api使用
     *
     * @param $tableid
     * @return $this
     */
    public function setTableid($tableid)
    {
        $this->tableid = $tableid;
        return $this;
    }

    /**
     * 向指定tableid的数据表中插入一条新数据。
     *
     * @param array $data 新增的数据
     *                      [
     *                          '_name'=>'',//数据名称，必填
     *                          '_location'=>'',//坐标，经度在前，纬度在后，当loctype=1时必填
     *                          'coordtype'=>'',//坐标类型，可选值1gps，2autonavi，3baidu，此参数可选，默认autonavi
     *                          '_address'=>'',//地址，当loctype=2时必填
     *                      ]
     * @param int $loctype 定位方式，1经纬度，2地址
     * @return bool|string 成功返回创建的数据id，失败返回false
     */
    public function dataCreate($data, $loctype = 1)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['loctype'] = $loctype;
        if ($loctype == 1) {
            if (empty($data['_location'])) {
                $this->errMsg = '当loctype=1时，_location必填';
                return false;
            }
        } else {
            if (empty($data['_address'])) {
                $this->errMsg = '当loctype=2时，_address必填';
                return false;
            }
        }
        $ops['data'] = json_encode($data);
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_V3_URL . self::YUNTU_CREATE_DATA_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] !== 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json['_id'];
        }
        return false;
    }

    /**
     * 向指定tableid的数据表中通过上传文件的方式创建多条数据。
     *
     * @param string $file 新增的数据文件名，csv文件
     * @param string $_name 文件中代表”名称”的字段
     * @param array $ops 可选参数
     * [
     *     'loctype'=>'',//定位方式，1经纬度，2地址，默认为1
     *     '_address'=>'',//文件中代表”地址”的字段，当loctype=2时，必填
     *     'longitude'=>'',//文件中代表”经度”的字段，当loctype=1时，必填
     *     'latitude'=>'',//文件中代表”纬度”的字段，当loctype=1时，必填
     *     'coordtype'=>''//坐标类型，
     * ]
     * @return bool|string 成功返回任务id，失败返回false
     */
    public function dataBatchCreate($file, $_name, $ops = [])
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (!empty($ops['loctype']) && $ops['loctype'] == 2) {
            if (empty($ops['_address'])) {
                $this->errMsg = '当loctype=2时，_address必填';
                return false;
            }
        } else {
            if (empty($ops['longitude'])) {
                $this->errMsg = '当loctype=1时，longitude必填';
                return false;
            }
            if (empty($ops['latitude'])) {
                $this->errMsg = '当loctype=1时，latitude必填';
                return false;
            }
        }
        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['file'] = new \CURLFile(realpath($file));
        $ops['_name'] = $_name;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_V3_URL . self::YUNTU_BATCH_CREATE_DATA_URL, $ops, true);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] !== 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json['batchid'];
        }
        return false;
    }

    /**
     * 更新指定tableid，指定一条数据序列号_id的数据信息。
     *
     * @param array $data 新增的数据
     * @param int $loctype 定位方式，1经纬度，2地址，默认为1
     * @return bool 操作成功或失败
     */
    public function dataUpdate($data, $loctype = 1)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['loctype'] = $loctype;
        if (empty($data['_id'])) {
            $this->errMsg = '数据id不能为空';
            return false;
        }
        $ops['data'] = json_encode($data);
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_V3_URL . self::YUNTU_UPDATE_DATA_URL, $ops, true);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] !== 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除指定tableid的数据表中的数据，一次请求限制删除1-50条数据。
     *
     * @param string|array $ids 删除的数据_id，多个id用逗号分割或传入数组，单次限制50条以内
     * @return bool|array 成功返回数组
     */
    public function dataDelete($ids)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        if (count($ids) > 50) {
            $this->errMsg = '不能超过50个id';
            return false;
        }
        $ops['ids'] = implode(',', $ids);
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_V3_URL . self::YUNTU_DELETE_DATA_URL, $ops, true);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] !== 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 批量处理管理接口为用户提供查看批处理的进度和结果信息。
     * @param string $batchid 批量处理任务唯一标识
     * @return bool|array 成功返回数组
     */
    public function importStatus($batchid)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        $ops['key'] = $this->key;
        $ops['batchid'] = $batchid;
        $paramStr = http_build_query($ops);
        $url = self::YUNTU_API_V3_URL . self::YUNTU_IMPORT_STATUS_URL . $paramStr;
        if ($this->sign) {
            $sign = $this->signature($ops);
            $url .= '&sig=' . $sign;
        }
        $result = $this->http_get($url);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 数字签名算法
     *
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
     * @param $url
     * @return bool|mixed
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

    /**
     * POST 请求
     *
     * @param string $url
     * @param array|string $param
     * @param boolean $post_file
     *            是否文件上传
     * @return string content
     */
    private function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        if (!(is_string($param) || $post_file)) {
            $strPOST = http_build_query($param);
        } else {
            $strPOST = $param;
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 将数组中boolean类型值转为string
     *
     * @param array $array
     * @param string $key
     */
    private function dealOps(&$array, $key)
    {
        if (isset($array[$key])) {
            if ($array[$key] === true || $array[$key] == 'true') {
                $array[$key] = 'true';
            } elseif ($array[$key] === false || $array[$key] == 'false') {
                $array[$key] = 'false';
            } else {
                unset($array[$key]);
            }
        }
    }
}