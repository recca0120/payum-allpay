<?php

use Mockery as m;

class AllpayLogisticsGatewayFactoryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_create_factory()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $httpClient = m::spy('Payum\Core\HttpClientInterface');
        $message = m::spy('Http\Message\MessageFactory');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $gateway = new AllpayLogisticsGatewayFactory();
        $config = $gateway->createConfig();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('allpay_logistics', $config['payum.factory_name']);
        $this->assertSame('Allpay Logistics', $config['payum.factory_title']);
    }
}
