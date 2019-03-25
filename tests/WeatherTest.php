<?php

namespace Dotimes\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Dotimes\Weather\Weather;
use Dotimes\Weather\Exceptions\InvalidArgumentException;
use Dotimes\Weather\Exceptions\HttpException;

class WeatherTest extends TestCase
{

	// 检查 $type 参数
    public function testGetWeatherWithInvalidType()
    {
        $w = new Weather('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);

        // 断言异常消息为 'Invalid type value(base/all): foo'
        $this->expectExceptionMessage('Invalid type value(base/all): foo');

        $w->getWeather('上海', 'foo');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    // 检查 $format 参数
    public function testGetWeatherWithInvalidFormat()
    {
        $w = new Weather('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);  

        // 断言异常消息为 'Invalid response format: array'
        $this->expectExceptionMessage('Invalid response format: array'); 

        // 因为支持的格式为 xml/json，所以传入 array 会抛出异常
        $w->getWeather('上海', 'base', 'array'); 

        // 如果没有抛出异常，就会运行到这行，标记当前测试没成功
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');   
    }

	public function testGetWeather()
	{
		// json
		// 创建模拟接口响应值
		$response = new Response(200, [], '{"success" : true}');
		// 创建模拟 http client
		$client = \Mockery::mock(Client::class);

		// 指定将会产生的行为
		$client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
			'query' => [
				'key' => 'mock-key',
                'city' => '上海',
                'output' => 'json',
                'extensions' => 'base',
			]
		])->andReturn($response);

		// 将 getHttpClient 方法替换为上面创建的 http client 为返回值的模拟方法
		$w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
		$w->allows()->getHttpClient()->andReturn($client);

		// 然后调用 `getWeather` 方法，并断言返回值为模拟的返回值。
        $this->assertSame(['success' => true], $w->getWeather('上海'));

        // xml
        $response = new Response(200, [], '<hello>content</hello>');
        $client = \Mockery::mock(Client::class);
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key' => 'mock-key',
                'city' => '上海',
                'extensions' => 'all',
                'output' => 'xml',
            ],
        ])->andReturn($response);

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->assertSame('<hello>content</hello>', $w->getWeather('上海', 'all', 'xml'));
	}

	public function testGetLiveWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('上海', 'base', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getLiveWeather('上海'));
    }

    public function testGetForecastsWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('上海', 'all', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getForecastsWeather('上海'));
    }

	public function testGetWeatherWithGuzzleRuntimeException()
	{
		$client = \Mockery::mock(Client::class);
		$client->allows()
			->get(new AnyArgs())
			->andThrow(new \Exception('request timeout'));

		$w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
		$w->allows()->getHttpClient()->andReturn($client);

		$this->expectException(HttpException::class);
		$this->expectExceptionMessage('request timeout');

		$w->getWeather('上海');
	}

	public function testGetHttpClient()
	{
		$w = new Weather('mock-key');

		$this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
	}

	public function testSetGuzzleOptions()
	{
		$w = new Weather('mock-key');

		$this->assertNull($w->getHttpClient()->getConfig('timeout'));

		$w->setGuzzleOptions(['timeout' => 5000]);

		$this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
	}
}