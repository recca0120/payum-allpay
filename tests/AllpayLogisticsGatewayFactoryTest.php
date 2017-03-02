<?php

namespace PayumTW\Allpay\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Allpay\AllpayLogisticsGatewayFactory;

class AllpayLogisticsGatewayFactoryTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testCreateConfig()
    {
        $gateway = new AllPayLogisticsGatewayFactory();
        $config = $gateway->createConfig([
            'payum.api' => false,
            'payum.required_options' => [],
            'payum.http_client' => $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            'httplug.message_factory' => $messageFactory = m::mock('Http\Message\MessageFactory'),
            'MerchantID' => '2000132',
            'HashKey' => '5294y06JbISpM5x9',
            'HashIV' => 'v77hoKGq4kWxNNIS',
            'sandbox' => true,
        ]);

        $this->assertInstanceOf(
            'PayumTW\Ecpay\EcpayLogisticsApi',
            $config['payum.api'](ArrayObject::ensureArrayObject($config))
        );

        $this->assertSame('allpay_logistics', $config['payum.factory_name']);
        $this->assertSame('Allpay Logistics', $config['payum.factory_title']);
    }
}
