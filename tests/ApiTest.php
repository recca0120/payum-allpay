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

    public function test_check_mac_value()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        // $merchantID = '2000132';
        // $hashKey = '5294y06JbISpM5x9';
        // $hashIV = 'v77hoKGq4kWxNNIS';
        // $config = [
        //     'MerchantID'        => $merchantID,
        //     'HashKey'           => $hashKey,
        //     'HashIV'            => $hashIV,
        //     'DeviceSource'      => DeviceType::PC,
        //     'sandbox'           => true,
        // ];
        //
        // $client = m::mock(HttpClientInterface::class);
        // $messageFactory = m::mock(MessageFactory::class);
        // $request = m::mock(stdClass::class);
        // $api = new Api($config, $client, $messageFactory);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        // $url = 'http://localhost:3000/kiki/payment/capture/zIpr-bFU1NjfgWQc_gyAliuIFjmQ47F22IAypk-QZCA';
        // $request->shouldReceive('getToken->getTargetUrl')->andReturn($url);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        // $httpPostRedirect = $api->request([
        //     'ReturnURL'         => 'http://localhost:3000/kiki/payment/capture/zIpr-bFU1NjfgWQc_gyAliuIFjmQ47F22IAypk-QZCA',
        //     'ClientBackURL'     => 'http://localhost:3000/kiki/payment/capture/zIpr-bFU1NjfgWQc_gyAliuIFjmQ47F22IAypk-QZCA',
        //     'OrderResultURL'    => 'http://localhost:3000/kiki/payment/capture/zIpr-bFU1NjfgWQc_gyAliuIFjmQ47F22IAypk-QZCA',
        //     'MerchantTradeNo'   => '57BC68DB12C49',
        //     'MerchantTradeDate' => '2016/08/23 23:16:43',
        //     'PaymentType'       => 'aio',
        //     'TotalAmount'       => '960',
        //     'TradeDesc'         => 'kiki食品雜貨',
        //     'ChoosePayment'     => 'Credit',
        //     'Remark'            => '',
        //     'ChooseSubPayment'  => '',
        //     'NeedExtraPaidInfo' => 'N',
        //     'IgnorePayment'     => '',
        //     'InvoiceMark'       => '',
        //     'EncryptType'       => 0,
        //     'UseRedeem'         => 'N',
        //     'ItemName'          => 'kiki 香茅粉 120 元 x 6#kiki 椒麻粉 120 元 x 1#運費 120 元 x 1',
        //     'ItemURL'           => 'dedwed',
        // ], $request);
        // $this->assertInstanceOf(HttpPostRedirect::class, $httpPostRedirect);
        // $params = $httpPostRedirect->getFields();
        // $this->assertSame('c6f87606cd33f32fbd750c6a317f7d03', $params['CheckMacValue']);
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
