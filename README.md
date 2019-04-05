> 0.2版本开发中，整体重构

## 安装

```shell
composer require lvzhao1995/amap-sdk
```

## 使用

```php
<?php
use \Amap\Amap;

$options = [
    'sign'=>false, //是否进行数字签名，默认不签名
    'private_key'=>'', //数字签名私钥，sign=true时必填
    'key'=>''//api调用key，必填
];

$map = new Amap($options);
```

> 详细使用文档制作中，目前请查看源码注释

## 相关文档

* 高德web服务API: http://lbs.amap.com/api/webservice/summary/
* 高德云图服务API: http://lbs.amap.com/api/yuntu/summary

> 建议先查看高德相关文档

## License

MIT

