<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Sync;
use PayumTW\Allpay\Action\NotifyAction;
use PayumTW\Allpay\Api;

class NotifyActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_notify_success()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new NotifyAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Notify::class);
        $details = new ArrayObject([
            'MerchantTradeNo' => 'fooMerchantTradeNo',
        ]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway
            ->shouldReceive('execute')->with(m::type(GetHttpRequest::class))->once()
            ->shouldReceive('execute')->with(m::type(Sync::class));

        $request->shouldReceive('getModel')->twice()->andReturn($details);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        try {
            $action->execute($request);
        } catch (HttpResponse $response) {
            $this->assertSame('1|OK', $response->getContent());
            $this->assertSame(200, $response->getStatusCode());
        }
    }

    public function test_notify_vaild_fail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new NotifyAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Notify::class);
        $details = new ArrayObject([
            'RtnCode' => '-1',
        ]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway
            ->shouldReceive('execute')->with(GetHttpRequest::class)->once()
            ->shouldReceive('execute')->with(m::type(Sync::class));;

        $request->shouldReceive('getModel')->twice()->andReturn($details);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        try {
            $action->execute($request);
        } catch (HttpResponse $response) {
            $this->assertSame('0|CheckMacValue verify fail.', $response->getContent());
            $this->assertSame(400, $response->getStatusCode());
        }
    }
}
