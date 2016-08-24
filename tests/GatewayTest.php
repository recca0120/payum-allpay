<?php

use Mockery as m;
use Payum\Core\PayumBuilder;
use PayumTW\Allpay\AllpayGatewayFactory;

class GatewayTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_gateway()
    {
        $defaultConfig = [];
        $payum = (new PayumBuilder())
            ->addGatewayFactory('allpay', new AllpayGatewayFactory($defaultConfig))
            ->addGateway('allpay', [
                'factory' => 'allpay',
                'sandbox' => true,
            ])
            ->getPayum();
    }
}
