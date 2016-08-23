<?php

use Http\Message\MessageFactory;
use Mockery as m;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use PayumTW\Allpay\Api;
use PayumTW\Allpay\Constants\DeviceType;

class ApiTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_request()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $hashKey = '5294y06JbISpM5x9';
        $hashIV = 'v77hoKGq4kWxNNIS';
        $config = [
            'MerchantID'   => '2000132',
            'HashKey'      => $hashKey,
            'HashIV'       => $hashIV,
            'DeviceSource' => DeviceType::PC,
            'sandbox'      => true,
        ];

        $client = m::mock(HttpClientInterface::class);
        $messageFactory = m::mock(MessageFactory::class);
        $request = m::mock(stdClass::class);
        $api = new Api($config, $client, $messageFactory);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $url = 'http://localhost/';
        $request->shouldReceive('getToken->getTargetUrl')->andReturn($url);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $httpPostRedirect = $api->request([], $request);
        $this->assertInstanceOf(HttpPostRedirect::class, $httpPostRedirect);
        $params = $httpPostRedirect->getFields();
        $this->assertSame(check_mac_value($hashKey, $hashIV, $params), $params['CheckMacValue']);
    }
}

function check_mac_value($hashKey, $hashIV, $params)
{
    if (isset($params['CheckMacValue']) === true) {
        unset($params['CheckMacValue']);
    }
    ksort($params, SORT_NATURAL | SORT_FLAG_CASE);
    $checkMacValue = 'HashKey='.$hashKey;
    foreach ($params as $key => $value) {
        $checkMacValue .= '&'.$key.'='.$value;
    }
    $checkMacValue .= '&HashIV='.$hashIV;
    $checkMacValue = strtolower(urlencode($checkMacValue));
    $checkMacValue = str_replace('%2d', '-', $checkMacValue);
    $checkMacValue = str_replace('%5f', '_', $checkMacValue);
    $checkMacValue = str_replace('%2e', '.', $checkMacValue);
    $checkMacValue = str_replace('%21', '!', $checkMacValue);
    $checkMacValue = str_replace('%2a', '*', $checkMacValue);
    $checkMacValue = str_replace('%28', '(', $checkMacValue);
    $checkMacValue = str_replace('%29', ')', $checkMacValue);

    return md5($checkMacValue);
}
