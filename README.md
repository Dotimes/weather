---

# Weather

基于  [高德开放平台](https://lbs.amap.com/dev/id/newuser) 的 PHP 天气信息组件。
[![Build Status](https://travis-ci.org/dotimes/weather.svg?branch=master)](https://travis-ci.org/dotimes/weather) ![StyleCI build status](https://github.styleci.io/repos/177609837/shield) 

## 安装

```sh
$ composer require dotimes/weather -vvv
```

## 配置

在使用本扩展之前，你需要去 [高德开放平台](https://lbs.amap.com/dev/id/newuser) 注册账号，然后创建应用，获取应用的 API Key。

## 使用

```php
use Dotimes\Weather\Weather;

$key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

$weather = new Weather($key);
```

###  获取实时天气

```php
$response = $weather->getLiveWeather('上海');
```
示例：

```
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "lives": [
        {
            "province": "上海",
            "city": "上海市",
            "adcode": "310000",
            "weather": "晴",
            "temperature": "10",
            "winddirection": "北",
            "windpower": "≤3",
            "humidity": "91",
            "reporttime": "2019-03-26 00:18:46"
        }
    ]
}
```

### 获取近期天气预报

```
$response = $weather->getForecastsWeather('上海');
```
示例：

```json
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "forecasts": [
        {
            "city": "上海市",
            "adcode": "310000",
            "province": "上海",
            "reporttime": "2019-03-26 00:18:46",
            "casts": [
                {
                    "date": "2019-03-25",
                    "week": "1",
                    "dayweather": "阴",
                    "nightweather": "晴",
                    "daytemp": "14",
                    "nighttemp": "10",
                    "daywind": "西南",
                    "nightwind": "西南",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                },
                {
                    "date": "2019-03-26",
                    "week": "2",
                    "dayweather": "多云",
                    "nightweather": "多云",
                    "daytemp": "22",
                    "nighttemp": "13",
                    "daywind": "南",
                    "nightwind": "南",
                    "daypower": "4",
                    "nightpower": "4"
                },
                {
                    "date": "2019-03-27",
                    "week": "3",
                    "dayweather": "小雨",
                    "nightweather": "小雨",
                    "daytemp": "17",
                    "nighttemp": "13",
                    "daywind": "东北",
                    "nightwind": "东北",
                    "daypower": "4",
                    "nightpower": "4"
                },
                {
                    "date": "2019-03-28",
                    "week": "4",
                    "dayweather": "中雨",
                    "nightweather": "小雨",
                    "daytemp": "17",
                    "nighttemp": "10",
                    "daywind": "北",
                    "nightwind": "北",
                    "daypower": "4",
                    "nightpower": "4"
                }
            ]
        }
    ]
}
```

### 获取 XML 格式返回值

第三个参数为返回值类型，可选 `json` 与 `xml`，默认 `json`：

```php
$response = $weather->getLiveWeather('上海', 'xml');
```

示例：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
	<status>1</status>
	<count>1</count>
	<info>OK</info>
	<infocode>10000</infocode>
	<lives type="list"><live>
		<province>上海</province>
		<city>上海市</city>
		<adcode>310000</adcode>
		<weather>晴</weather>
		<temperature>10</temperature>
		<winddirection>北</winddirection>
		<windpower>≤3</windpower>
		<humidity>91</humidity>
		<reporttime>2019-03-26 00:18:46</reporttime>
	</live>
</lives>
</response>
```

### 参数说明

```
array | string   getLiveWeather(string $city, string $format = 'json')
array | string   getForecastsWeather(string $city, string $format = 'json')
```

> - `$city` - 城市名，比如：“上海”；
> - `$format`  - 输出的数据格式，默认为 json 格式，当 output 设置为 “`xml`” 时，输出的为 XML 格式的数据。

### 在 Laravel 中使用

在 Laravel 中使用也是同样的安装方式，配置写在 `config/services.php` 中：

```php
	.
	.
	.
	 'weather' => [
		'key' => env('WEATHER_API_KEY'),
    ],
```

然后在 `.env` 中配置 `WEATHER_API_KEY` ：

```env
WEATHER_API_KEY=xxxxxxxxxxxxxxxxxxxxx
```

可以用两种方式来获取 `Dotimes\Weather\Weather` 实例：

#### 方法参数注入

```php
	.
	.
	.
	public function edit(Weather $weather) 
	{
		$response = $weather->getLiveWeather('上海');
		$response = $weather->getForecastsWeather('上海');
	}
	.
	.
	.
```

#### 服务名访问

```php
	.
	.
	.
	public function edit() 
	{
		$response = app('weather')->getLiveWeather('上海');
		$response = app('weather')->getForecastsWeather('上海');
	}
	.
	.
	.

```

## 参考

- [高德开放平台天气接口](https://lbs.amap.com/api/webservice/guide/api/weatherinfo/)

## License

MIT