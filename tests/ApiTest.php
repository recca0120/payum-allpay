<?php

use Mockery as m;
use PayumTW\Allpay\Api;

class ApiTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_create_transaction()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $options = [
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => false,
        ];

        $params = [
            'ReturnURL' => 'http://www.allpay.com.tw/receive.php',
            'MerchantTradeNo' => 'Test'.time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'TotalAmount' => 2000,
            'TradeDesc' => 'good to drink',
            'ChoosePayment' => PaymentMethod::ALL,
            'Items' => [
                [
                    'Name' => '歐付寶黑芝麻豆漿',
                    'Price' => 2000,
                    'Currency' => '元',
                    'Quantity' => 1,
                    'URL' => 'dedwed',
                ],
            ],
        ];

        $httpClient = m::mock('Payum\Core\HttpClientInterface');
        $message = m::mock('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $api = new Api($options, $httpClient, $message);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $params = $api->createTransaction($params);
        $this->assertSame(CheckMacValue::generate($params, $options['HashKey'], $options['HashIV'], 0), $params['CheckMacValue']);
        $this->assertSame('https://payment.allpay.com.tw/Cashier/AioCheckOut/V2', $api->getApiEndpoint());
    }

    public function test_get_transaction_data()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $options = [
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => false,
        ];

        $params = [
            'response' => [
                'MerchantID' => '2000132',
                'MerchantTradeNo' => '57CBC66A39F82',
                'PayAmt' => '340',
                'PaymentDate' => '2016/09/04 15:03:08',
                'PaymentType' => 'Credit_CreditCard',
                'PaymentTypeChargeFee' => '3',
                'RedeemAmt' => '0',
                'RtnCode' => '1',
                'RtnMsg' => 'Succeeded',
                'SimulatePaid' => '0',
                'TradeAmt' => '340',
                'TradeDate' => '2016/09/04 14:59:13',
                'TradeNo' => '1609041459136128',
                'CheckMacValue' => '6812D213BF2C5B9377EBF101607BF2DF',
            ],
        ];

        $httpClient = m::mock('Payum\Core\HttpClientInterface');
        $message = m::mock('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $api = new Api($options, $httpClient, $message);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $params = $api->getTransactionData($params);

        $expected = [
            'response' => [
                'MerchantID' => '2000132',
                'MerchantTradeNo' => '57CBC66A39F82',
                'PayAmt' => '340',
                'PaymentDate' => '2016/09/04 15:03:08',
                'PaymentType' => 'Credit_CreditCard',
                'PaymentTypeChargeFee' => '3',
                'RedeemAmt' => '0',
                'RtnCode' => '1',
                'RtnMsg' => 'Succeeded',
                'SimulatePaid' => '0',
                'TradeAmt' => '340',
                'TradeDate' => '2016/09/04 14:59:13',
                'TradeNo' => '1609041459136128',
                'CheckMacValue' => '6812D213BF2C5B9377EBF101607BF2DF',
                'statusReason' => '成功',
            ],
         ];

        foreach ($expected['response'] as $key => $value) {
            $this->assertSame($value, $params[$key]);
        }

        $this->assertSame('https://payment.allpay.com.tw/Cashier/AioCheckOut/V2', $api->getApiEndpoint());
    }

    public function test_query_trade_info()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $options = [
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => false,
        ];

        $params = [
            'MerchantTradeNo' => '5832985816073',
            'response' => [],
        ];

        $httpClient = m::mock('Payum\Core\HttpClientInterface');
        $message = m::mock('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $api = new Api($options, $httpClient, $message);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $params = $api->getTransactionData($params);

        $this->assertSame('https://payment.allpay.com.tw/Cashier/AioCheckOut/V2', $api->getApiEndpoint());
    }

    public function test_parse_result_fail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $options = [
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => false,
        ];

        $params = [
            'response' => [
                'MerchantID' => '2000132',
                'MerchantTradeNo' => '57CBC66A39F82',
                'PayAmt' => '340',
                'PaymentDate' => '2016/09/04 15:03:08',
                'PaymentType' => 'Credit_CreditCard',
                'PaymentTypeChargeFee' => '3',
                'RedeemAmt' => '0',
                'RtnCode' => '1',
                'RtnMsg' => 'Succeeded',
                'SimulatePaid' => '0',
                'TradeAmt' => '340',
                'TradeDate' => '2016/09/04 14:59:13',
                'TradeNo' => '1609041459136128',
                'CheckMacValue' => '6812D213BF2C5B9377EBF101607BFEEE',
            ],
        ];

        $httpClient = m::mock('Payum\Core\HttpClientInterface');
        $message = m::mock('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $api = new Api($options, $httpClient, $message);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $result = $api->getTransactionData($params);
        $this->assertSame('10400002', $result['RtnCode']);
    }

    public function test_sandbox()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $options = [
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => true,
        ];

        $params = [

        ];

        $httpClient = m::mock('Payum\Core\HttpClientInterface');
        $message = m::mock('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $api = new Api($options, $httpClient, $message);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame('https://payment-stage.allpay.com.tw/Cashier/AioCheckOut/V2', $api->getApiEndpoint());
    }
}
