<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Sync;
use Payum\Core\Security\GenericTokenFactoryInterface;
use PayumTW\Allpay\Action\CaptureAction;
use PayumTW\Allpay\Api;
use PayumTW\Allpay\Request\Api\CreateTransaction;
class CaptureActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_redirect_to_allpay()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Capture::class);
        $tokenFactory = m::mock(GenericTokenFactoryInterface::class);
        $token = m::mock(stdClass::class);
        $notifyToken = m::mock(stdClass::class);
        $api = m::mock(Api::class);
        $details = new ArrayObject([]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway
            ->shouldReceive('execute')->with(m::type(GetHttpRequest::class))->once()
            ->shouldReceive('execute')->with(m::type(CreateTransaction::class))->once();

        $request
            ->shouldReceive('getModel')->twice()->andReturn($details)
            ->shouldReceive('getToken')->once()->andReturn($token);

        $token
            ->shouldReceive('getTargetUrl')->andReturn('fooOrderResultURL')
            ->shouldReceive('getGatewayName')->andReturn('fooGatewayName')
            ->shouldReceive('getDetails')->andReturn([
                'foo' => 'bar',
            ]);

        $notifyToken->shouldReceive('getTargetUrl')->andReturn('fooReturnURL');

        $tokenFactory
            ->shouldReceive('createNotifyToken')->once()->andReturn($notifyToken);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setGenericTokenFactory($tokenFactory);
        $action->execute($request);
        $this->assertSame([
            'OrderResultURL' => 'fooOrderResultURL',
            'ReturnURL' => 'fooReturnURL',
        ], (array) $details);
    }

    public function test_allpay_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Capture::class);
        $tokenFactory = m::mock(GenericTokenFactoryInterface::class);
        $token = m::mock(stdClass::class);
        $notifyToken = m::mock(stdClass::class);
        $api = m::mock(Api::class);
        $details = new ArrayObject([]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $expected = [
            'RtnCode' => '1',
        ];

        $gateway
            ->shouldReceive('execute')->with(m::type(GetHttpRequest::class))->once()->andReturnUsing(function ($httpRequest) use ($api, $expected) {
                $httpRequest->request = $expected;
            })
            ->shouldReceive('execute')->with(m::type(Sync::class))->once();

        $request
            ->shouldReceive('getModel')->twice()->andReturn($details);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setGenericTokenFactory($tokenFactory);
        $action->execute($request);
        $this->assertSame($expected, (array) $details);
    }
}
