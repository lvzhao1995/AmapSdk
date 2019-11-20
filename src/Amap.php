<?php

namespace Amap;

/**
 * 高德地图web服务php-sdk,官方api
 *
 * @author lvzhao1995<abc-1.2@qq.com>
 * @link https://github.com/lvzhao1995/AmapSdk
 * @version 0.1.2
 *          usage:
 *          $options = [
 *          'sign'=>false, //是否进行数字签名，可选
 *          'private_key'=>'', //数字签名私钥，sign=true时必填
 *          'key'=>'wxdk1234567890', //api调用key，必填
 *          ];
 */

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

    const STATIC_MAP_URL = '/staticmap?';

    const GRASP_URL = '/autograsp?';

    const CONVERT_URL = '/assistant/coordinate/convert?';

    const WEATHER_URL = '/weather/weatherInfo?';

    const INPUT_TIPS_URL = '/assistant/inputtips?';

    const RECTANGLE_TRAFFIC_URL = '/traffic/status/rectangle?';

    const CIRCLE_TRAFFIC_URL = '/traffic/status/circle?';

    const ROAD_TRAFFIC_URL = '/traffic/status/road?';

    const YUNTU_API_URL = 'http://yuntuapi.amap.com';

    const YUNTU_CREATE_TABLE_URL = '/datamanage/table/create';

    const YUNTU_CREATE_DATA_URL = '/datamanage/data/create';

    const YUNTU_BATCH_CREATE_DATA_URL = '/datamanage/data/batchcreate';

    const YUNTU_UPDATE_DATA_URL = '/datamanage/data/update';

    const YUNTU_DELETE_DATA_URL = '/datamanage/data/delete';

    const YUNTU_IMPORT_STATUS_URL = '/datamanage/batch/importstatus?';

    const YUNTU_SEARCH_LOCAL_URL = '/datasearch/local?';

    const YUNTU_SEARCH_AROUND_URL = '/datasearch/around?';

    const YUNTU_SEARCH_POLYGON_URL = '/datasearch/polygon?';

    const YUNTU_SEARCH_ID_URL = '/datasearch/id?';

    const YUNTU_DATA_LIST_URL = '/datamanage/data/list?';

    const YUNTU_STATISTICS_CITY_URL = '/datasearch/statistics/province?';

    const YUNTU_STATISTICS_PROVINCE_URL = '/datasearch/statistics/city?';

    const YUNTU_STATISTICS_DISTRICT_URL = '/datasearch/statistics/district?';

    private $sign = false;

    private $private_key;

    private $key;

    public $errCode = 0;

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
        if (!empty($city)) {
            $data['city'] = $city;
        }
        $url = self::API_V3_URL . self::GEO_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
        $default = [
            'poitype' => '',
            'radius' => '',
            'extensions' => '',
            'batch' => '',
            'roadlevel' => '',
            'homeorcorp' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $this->dealOps($ops, 'batch');
        $ops['location'] = $location;
        $url = self::API_V3_URL . self::REGEO_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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

        $url = self::API_V3_URL . self::WALK_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
        $default = [
            'cityd' => '',
            'extensions' => '',
            'strategy' => '',
            'nightflag' => '',
            'date' => '',
            'time' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['origin'] = $origin;
        $ops['destination'] = $destination;
        $ops['city'] = $city;

        $url = self::API_V3_URL . self::BUS_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
     *            'origintype'=>'',//无默认值，起点poi类别
     *            'destinationtype'=>'',//无默认值，终点poi类别
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
        $default = [
            'originid' => '',
            'destinationid' => '',
            'origintype' => '',
            'destinationtype' => '',
            'strategy' => '',
            'waypoints' => '',
            'avoidpolygons' => '',
            'avoidroad' => '',
            'province' => '',
            'number' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['origin'] = $origin;
        $ops['destination'] = $destination;
        if (isset($ops['number'])) {
            $ops['number'] = strtoupper($ops['number']);
        }

        $url = self::API_V3_URL . self::DRIVE_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $ops = [
            'key' => $this->key,
            'origin' => $origin,
            'destination' => $destination
        ];

        $url = self::API_V4_URL . self::BICYCLING_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
            'type' => $type,
            'key' => $this->key
        ];
        $url = self::API_V3_URL . self::DISTANCE_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
        $default = [
            'keywords' => '',
            'subdistrict' => '',
            'page' => '',
            'offset' => '',
            'extensions' => '',
            'filter' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;

        $url = self::API_V3_URL . self::DISTRICT_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $default = [
            'city' => '',
            'citylimit' => '',
            'children' => '',
            'offset' => '',
            'page' => '',
            'building' => '',
            'floor' => '',
            'extensions' => ''
        ];
        $ops = array_intersect_key($ops, $default);
        if (empty($keywords) && empty($types)) {
            $this->errMsg = 'keywords和types两者至少必选其一';
            return false;
        }

        $ops['key'] = $this->key;
        $this->dealOps($ops, 'citylimit');
        if (!empty($keywords)) {
            $ops['keywords'] = $keywords;
        }
        if (!empty($types)) {
            $ops['types'] = $types;
        }

        $url = self::API_V3_URL . self::TEXT_SEARCH_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $default = [
            'keywords' => '',
            'types' => '',
            'city' => '',
            'radius' => '',
            'sortrule' => '',
            'offset' => '',
            'page' => '',
            'extensions' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['location'] = $location;

        $url = self::API_V3_URL . self::AROUND_SEARCH_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $default = [
            'keywords' => '',
            'types' => '',
            'offset' => '',
            'page' => '',
            'extensions' => 'base'
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['polygon'] = $polygon;
        $url = self::API_V3_URL . self::POLYGON_SEARCH_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $data = [
            'key' => $this->key,
            'id' => $id
        ];

        $url = self::API_V3_URL . self::ID_SRARCH_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
        if (!empty($ip)) {
            $data['ip'] = $ip;
        }
        $url = self::API_V3_URL . self::IP_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] == 0) {
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
        $data = [
            'key' => $this->key,
            'carid' => substr($this->key, -4) . $carid,
            'locations' => $locations,
            'time' => $time,
            'direction' => $direction,
            'speed' => $speed
        ];
        $url = self::API_V3_URL . self::GRASP_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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

    private function dealMarker($markers)
    {
        $array = [];
        foreach ($markers as $v) {
            $style = [
                'size' => $v['style']['size'] ?: '',
                'color' => $v['style']['color'] ?: '',
                'label' => $v['style']['label'] ?: ''
            ];
            $style = implode(',', $style);
            $tmp = $v;
            unset($tmp['style']);
            $array[] = $style . ':' . implode(';', $tmp);
        }
        return implode('|', $array);
    }

    private function dealLabels($labels)
    {
        $array = [];
        foreach ($labels as $v) {
            $style = [
                'content' => $v['style']['content'] ?: '',
                'font' => $v['style']['font'] ?: '',
                'bold' => $v['style']['bold'] ?: '',
                'fontSize' => $v['style']['fontSize'] ?: '',
                'fontColor' => $v['style']['fontColor'] ?: '',
                'background' => $v['style']['background'] ?: ''
            ];
            $style = implode(',', $style);
            $tmp = $v;
            unset($tmp['style']);
            $array[] = $style . ':' . implode(';', $tmp);
        }
        return implode('|', $array);
    }

    private function dealPaths($paths)
    {
        $array = [];
        foreach ($paths as $v) {
            $style = [
                'weight' => $v['style']['weight'] ?: '',
                'color' => $v['style']['color'] ?: '',
                'transparency' => $v['style']['transparency'] ?: '',
                'fillcolor' => $v['style']['fillcolor'] ?: '',
                'fillTransparency' => $v['style']['fillTransparency'] ?: ''
            ];
            $style = implode(',', $style);
            $tmp = $v;
            unset($tmp['style']);
            $array[] = $style . ':' . implode(';', $tmp);
        }
        return implode('|', $array);
    }

    /**
     * @param array $ops 参数
     *  [
     * 'location' => '',//地图中心点
     * 'zoom' => '',//地图级别[1,17]
     * 'size' => '',//地图大小,最大1024*1024，默认400*400
     * 'scale' => '',//1普通，2高清，默认1
     * 'markers' => [//标注
     *      [
     *          'style'=>[
     *              'size'=>'',//标注点大小，可选值small，mid，large，默认small，如果用自定义图片填写-1
     *              'color'=>'',//标注点颜色，16进制颜色代码，例如0x000000,如果用自定义图片填写图片url
     *              'label'=>''[0-9]、[A-Z]、[单个中文字] 当size为small时，图片不展现标注名。如果用自定义图片填写0
     *          ],
     *          '',//标注点坐标1
     *          '',//标注点坐标2
     *          ...//当前样式下更多标注
     *      ]//更多标注样式继续添加数组
     * ],
     * 'labels' => [//标签
     *      [
     *          'style'=>[
     *              'content'=>'',//标签内容，最大15字符
     *              'font'=>'',//字体，0微软雅黑，1宋体，2Times New Roman，3Helvetica，默认0
     *              'bold'=>'',//0非粗体，1粗体，默认0
     *              'fontSize'=>'',//字体大小，可选值[1,72]，默认10
     *              'fontColor'=>'',//字体颜色，默认0xffffff
     *              'background'=>''//背景色，默认0x5288d8
     *          ],
     *          '',//标签坐标1
     *          '',//标签坐标2
     *          ...//当前样式下更多标签
     *      ]//更多标签样式继续添加数组
     * ],
     * 'paths' => [//线条
     *      [
     *          'style'=>[
     *              'weight'=>'',//线条粗细，可选值[2,15]，默认5
     *              'color'=>'',//线条颜色，默认值0x0000ff
     *              'transparency'=>'',//透明度，可选值[0,1],小数点后最多2位，默认为1
     *              'fillcolor'=>'',//多边形填充颜色，不为空时折线封闭为多边形
     *              'fillTransparency'=>''//填充面透明度，默认0.5
     *          ],
     *          '',//折线点坐标
     *          '',//折线点坐标
     *          ...//继续添加，所有坐标连成折线或多边形
     *      ]//更多折线继续添加数组
     * ],
     * 'traffic' => ''//交通路况标识，0不展现，1展现，默认为0
     * ]
     * @param bool $url 是否返回url，true返回构造好的url，false返回图片二进制内容
     * @return bool|mixed|string
     */
    public function staticmap($ops = [], $url = false)
    {
        $default = [
            'location' => '',
            'zoom' => '',
            'size' => '',
            'scale' => '',
            'markers' => '',
            'labels' => '',
            'paths' => '',
            'traffic' => ''
        ];
        $ops = array_intersect_key($ops, $default);
        empty($ops['markers']) and $ops['markers'] = $this->dealMarker($ops['markers']);
        empty($ops['labels']) and $ops['labels'] = $this->dealLabels($ops['labels']);
        empty($ops['paths']) and $ops['paths'] = $this->dealPaths($ops['paths']);
        if (empty($ops['markers']) && empty($ops['labels']) && empty($ops['paths']) && (empty($ops['location']) || empty($ops['zoom']))) {
            $this->errMsg = '没有标注/标签/折线等覆盖物时，中心点（location）和地图级别（zoom）必填';
            return false;
        }

        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        if ($url) {
            $ops = array_filter($ops);
            return $url . http_build_query($ops);
        }
        $url = self::API_V3_URL . self::STATIC_MAP_URL;
        $result = $this->http_get($url, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!is_array($json)) {
                return $result;
            } elseif ($json['status'] == 0) {
                $this->errMsg = $json['info'];
                return false;
            }
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
        $data = [
            'key' => $this->key,
            'locations' => $locations,
            'coordsys' => $coordsys
        ];
        $url = self::API_V3_URL . self::CONVERT_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
        $data = [
            'key' => $this->key,
            'city' => $city,
            'extensions' => $extensions,
            'output' => 'json'
        ];
        $url = self::API_V3_URL . self::WEATHER_URL;
        if ($this->sign) {
            $data['sig'] = $this->signature($data);
        }
        $result = $this->http_get($url, $data);
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
     * @return boolean|array 成功返回结果数组，内容参考 http://lbs.amap.com/api/webservice/guide/api/inputtips#inputtips
     */
    public function inputtips($keywords, $ops = [])
    {
        $default = [
            'type' => '',
            'location' => '',
            'city' => '',
            'citylimit' => '',
            'datatype' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['keywords'] = $keywords;
        $this->dealOps($ops, 'citylimit');
        $url = self::API_V3_URL . self::INPUT_TIPS_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
     * 矩形区域交通态势
     *
     * @param string|array $rectangle 代表此为矩形区域查询,分号分割的坐标对或坐标数组
     * @param array $ops 参数数组
     * [
     *      'level'=>'',//道路等级，1高速，2城市快速路、国道，3高速辅路，4主要道路，5一般道路，6无名道路，默认5
     *      'extensions'=>''//返回结果控制，可选值base,all，默认base
     * ]
     * @return bool|array 成功返回结果数组，内容参考 http://lbs.amap.com/api/webservice/guide/api/trafficstatus#rectangle
     */
    public function rectangleTraffic($rectangle, $ops = [])
    {
        $default = [
            'level' => '',
            'extensions' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (!is_array($rectangle)) {
            $rectangle = explode(';', $rectangle);
        }
        if (count($rectangle) != 2) {
            $this->errMsg = '只能有两个坐标对';
            return false;
        }
        $rectangle = implode(';', $rectangle);

        $ops['key'] = $this->key;
        $ops['rectangle'] = $rectangle;

        $url = self::API_V3_URL . self::RECTANGLE_TRAFFIC_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
     * 圆形区域交通态势
     * @param string $location 中心点坐标
     * @param array $ops 参数数组
     * [
     *      'level'=>'',//道路等级，1高速，2城市快速路、国道，3高速辅路，4主要道路，5一般道路，6无名道路，默认5
     *      'extensions'=>'',//返回结果控制，可选值base,all，默认base
     *      'radius'=>''//半径，单位米，最大5000米，默认1000米
     * ]
     * @return bool|array 成功返回结果数组，内容参考 http://lbs.amap.com/api/webservice/guide/api/trafficstatus#circle
     */
    public function circleTraffic($location, $ops = [])
    {
        $default = [
            'level' => '',
            'extensions' => '',
            'radius' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        $ops['key'] = $this->key;
        $ops['location'] = $location;

        $url = self::API_V3_URL . self::CIRCLE_TRAFFIC_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
     * 指定线路交通态势
     * @param string $name 道路名
     * @param string $adcode 城市编码，推荐使用此字段，不使用city，city和adcode必填一个
     * @param string $city city和adcode必填一个
     * @param array $ops 参数数组
     * [
     *      'level'=>'',//道路等级，1高速，2城市快速路、国道，3高速辅路，4主要道路，5一般道路，6无名道路，默认5
     *      'extensions'=>'',//返回结果控制，可选值base,all，默认base
     * ]
     * @return bool|array 成功返回结果数组，内容参考 http://lbs.amap.com/api/webservice/guide/api/trafficstatus#road
     */
    public function roadTraffic($name, $adcode = '', $city = '', $ops = [])
    {
        $default = [
            'level' => '',
            'extensions' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($adcode) && empty($city)) {
            $this->errMsg = 'city和adcode必填一个';
            return false;
        }

        $ops['key'] = $this->key;
        $ops['name'] = $name;
        $ops['adcode'] = $adcode;
        $ops['city'] = $city;

        $url = self::API_V3_URL . self::CIRCLE_TRAFFIC_URL;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_get($url, $ops);
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
        $result = $this->http_post(self::YUNTU_API_URL . self::YUNTU_CREATE_TABLE_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] != 1) {
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
        if (empty($data['_name'])) {
            $this->errMsg = '请填写数据名称';
            return false;
        }
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

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'loctype' => $loctype,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_URL . self::YUNTU_CREATE_DATA_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] != 1) {
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
        $default = [
            'loctype' => '',
            '_address' => '',
            'longitude' => '',
            'latitude' => '',
            'coordtype' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($_name)) {
            $this->errMsg = '_name必填';
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
        $path = realpath($file);
        if (false === $path) {
            $this->errMsg = '文件不存在或权限不足';
            return false;
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'file' => new \CURLFile(realpath($file)),
            '_name' => $_name
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_URL . self::YUNTU_BATCH_CREATE_DATA_URL, $ops, true);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] != 1) {
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
        if (empty($data['_id'])) {
            $this->errMsg = '数据id不能为空';
            return false;
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'loctype' => $loctype,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_URL . self::YUNTU_UPDATE_DATA_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] != 1) {
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
        if (empty($ids)) {
            $this->errMsg = 'ids不能为空';
            return false;
        }
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if (count($ids) > 50) {
            $this->errMsg = '不能超过50个id';
            return false;
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'ids' => implode(',', $ids)
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $result = $this->http_post(self::YUNTU_API_URL . self::YUNTU_DELETE_DATA_URL, $ops);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || $json['status'] != 1) {
                $this->errMsg = $json['info'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 批量处理管理接口为用户提供查看批处理的进度和结果信息。
     *
     * @param string $batchid 批量处理任务唯一标识
     * @return bool|array 成功返回数组
     */
    public function importStatus($batchid)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($batchid)) {
            $this->errMsg = 'batchid不能为空';
            return false;
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'batchid' => $batchid
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_IMPORT_STATUS_URL;
        $result = $this->http_get($url, $ops);
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
     * 云图本地检索
     *
     * @param string $keywords 搜索关键词，可以为空。详细说明请查看高德云图文档。
     * @param string $city 检索的城市，设定值非法或不正确时按照全国返回
     * @param array $ops 可选参数，
     *                  [
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                      'sortrule'=>'',//排序规则，详情查看高的云图文档
     *                      'limit'=>'',//每页的条数，最大为100，默认20
     *                      'page'=>''//当前页数，默认为1
     *                  ]
     * @return bool|mixed
     */
    public function yuntuLocalSearch($keywords, $city, $ops = [])
    {
        $default = [
            'filter' => '',
            'sortrule' => '',
            'limit' => '',
            'page' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($keywords)) {
            $keywords = ' ';
        }
        if (empty($city)) {
            $city = '全国';
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['keywords'] = $keywords;
        $ops['city'] = $city;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_SEARCH_LOCAL_URL;
        $result = $this->http_get($url, $ops);
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
     * 云图周边检索
     *
     * @param string $center 中心点坐标
     * @param array $ops 可选参数，
     *                  [
     *                      'keywords'=>'',//搜索关键词，详情查看高德云图文档
     *                      'radius'=>'',//查询半径，单位米，默认3000，取值范围[0,50000]，超出取值范围按默认值
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                      'sortrule'=>'',//排序规则，详情查看高的云图文档
     *                      'limit'=>'',//每页的条数，最大为100，默认20
     *                      'page'=>''//当前页数，默认为1
     *                  ]
     * @return bool|mixed
     */
    public function yuntuAroundSearch($center, $ops = [])
    {
        $default = [
            'keywords' => '',
            'radius' => '',
            'filter' => '',
            'sortrule' => '',
            'limit' => '',
            'page' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($center)) {
            $this->errMsg = '请填写中心点坐标';
            return false;
        }
        if (isset($ops['keywords']) && empty($ops['keywords'])) {
            $ops['keywords'] = ' ';
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['center'] = $center;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_SEARCH_AROUND_URL;
        $result = $this->http_get($url, $ops);
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
     * 云图多边形检索
     *
     * @param string $polygon 做标集合,矩形只需传对角线坐标，多边形首尾坐标需相同，坐标之间用分号分割
     * @param array $ops 可选参数，
     *                  [
     *                      'keywords'=>'',//搜索关键词，详情查看高德云图文档
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                      'sortrule'=>'',//排序规则，详情查看高的云图文档
     *                      'limit'=>'',//每页的条数，最大为100，默认20
     *                      'page'=>''//当前页数，默认为1
     *                  ]
     * @return bool|mixed
     */
    public function yuntuPolygonSearch($polygon, $ops = [])
    {
        $default = [
            'keywords' => '',
            'filter' => '',
            'sortrule' => '',
            'limit' => '',
            'page' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($polygon)) {
            $this->errMsg = '请填写多边形坐标集合';
            return false;
        }
        if (isset($ops['keywords']) && empty($ops['keywords'])) {
            $ops['keywords'] = ' ';
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['polygon'] = $polygon;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_SEARCH_POLYGON_URL;
        $result = $this->http_get($url, $ops);
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
     * 云图id检索
     *
     * @param string $_id 数据id
     * @return bool|mixed
     */
    public function yuntuIdSearch($_id)
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($_id)) {
            $this->errMsg = '数据id不能为空';
            return false;
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            '_id' => $_id
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_SEARCH_ID_URL;
        $result = $this->http_get($url, $ops);
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
     * 按条件检索数据
     *
     * @param array $ops 可选参数
     *                  [
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                      'sortrule'=>'',//排序规则，详情查看高的云图文档
     *                      'limit'=>'',//每页的条数，最大为100，默认20
     *                      'page'=>''//当前页数，默认为1
     *                  ]
     * @return bool|mixed
     */
    public function yuntuListData($ops = [])
    {
        $default = [
            'filter' => '',
            'sortrule' => '',
            'limit' => '',
            'page' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_DATA_LIST_URL;
        $result = $this->http_get($url, $ops);
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
     * 省数据分布检索请求
     *
     * @param string $keywords 搜索关键词
     * @param array $ops 可选参数
     *                  [
     *                      'country'=>'',//指定所需查询的国家名，目前仅支持中国，默认值中国
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                  ]
     * @return bool|mixed
     */
    public function provinceStatistics($keywords = ' ', $ops = [])
    {
        $default = [
            'country' => '',
            'filter' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($keywords)) {
            $keywords = ' ';
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['keywords'] = $keywords;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_STATISTICS_PROVINCE_URL;
        $result = $this->http_get($url, $ops);
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
     * 省数据分布检索请求
     *
     * @param string $keywords 搜索关键词
     * @param array $ops 可选参数
     *                  [
     *                      'province'=>'',//指定所需查询的省，返回含有数据的所有市名称以及数据量，根据数据量排序，默认值全国
     *                      'filter'=>'',//过滤条件，详情查看高德云图文档
     *                  ]
     * @return bool|mixed
     */
    public function cityStatistics($keywords = ' ', $ops = [])
    {
        $default = [
            'province' => '',
            'filter' => ''
        ];
        $ops = array_intersect_key($ops, $default);

        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($keywords)) {
            $keywords = ' ';
        }

        $ops['key'] = $this->key;
        $ops['tableid'] = $this->tableid;
        $ops['keywords'] = $keywords;
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_STATISTICS_CITY_URL;
        $result = $this->http_get($url, $ops);
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
     * 区县数据分布检索
     *
     * @param string $province 指定所需查询的省
     * @param string $city 指定所需查询的市
     * @param string $keywords 搜索关键词
     * @param string $filter 过滤条件
     * @return bool|mixed
     */
    public function districtStatistics($province, $city, $keywords = ' ', $filter = '')
    {
        if (empty($this->tableid)) {
            $this->errMsg = '请先设置tableid';
            return false;
        }
        if (empty($keywords)) {
            $keywords = ' ';
        }

        $ops = [
            'key' => $this->key,
            'tableid' => $this->tableid,
            'keywords' => $keywords,
            'province' => $province,
            'city' => $city,
            'filter' => $filter
        ];
        if ($this->sign) {
            $ops['sig'] = $this->signature($ops);
        }
        $url = self::YUNTU_API_URL . self::YUNTU_STATISTICS_DISTRICT_URL;
        $result = $this->http_get($url, $ops);
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
        $tmpStr = '';
        foreach ($data as $key => $value) {
            if (strlen($tmpStr) == 0) {
                $tmpStr .= $key . "=" . $value;
            } else {
                $tmpStr .= "&" . $key . "=" . $value;
            }
        }
        $tmpStr .= $this->private_key;
        $signStr = md5($tmpStr);
        return $signStr;
    }

    /**
     * @param $url
     * @param array $params 参数数组，自动剔除空值参数，数组为空时不处理
     * @return bool|mixed
     */
    private function http_get($url, $params = [])
    {
        if (!empty($params)) {
            $url .= http_build_query($params);
        }
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
