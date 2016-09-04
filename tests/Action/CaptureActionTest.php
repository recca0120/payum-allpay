<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryInterface;
use PayumTW\Allpay\Action\CaptureAction;
use PayumTW\Allpay\Api;

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
        $model = new ArrayObject([]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway->shouldReceive('execute')->with(GetHttpRequest::class)->once();

        $request
            ->shouldReceive('getModel')->twice()->andReturn($model)
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

        $api
            ->shouldReceive('getApiEndpoint')->once()->andReturn('fooApiEndpoint')
            ->shouldReceive('preparePayment')->once()->andReturn($model->toUnsafeArray());

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setApi($api);
        $action->setGenericTokenFactory($tokenFactory);
        try {
            $action->execute($request);
        } catch (HttpResponse $response) {
            $this->assertSame('fooApiEndpoint', $response->getUrl());
            $this->assertSame('fooOrderResultURL', $model['OrderResultURL']);
            $this->assertSame('fooReturnURL', $model['ReturnURL']);
        }
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
        $model = new ArrayObject([]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $expected = [
            'RtnCode' => '1',
        ];

        $gateway->shouldReceive('execute')->with(GetHttpRequest::class)->once()->andReturnUsing(function ($httpRequest) use ($api, $expected) {
            $httpRequest->request = $expected;

            $api->shouldReceive('parseResult')->once()->andReturn($httpRequest->request);
        });

        $request
            ->shouldReceive('getModel')->twice()->andReturn($model);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setApi($api);
        $action->setGenericTokenFactory($tokenFactory);
        $action->execute($request);
        $this->assertSame($expected, $model->toUnsafeArray());
    }

    /**
     * @expectedException Payum\Core\Exception\UnsupportedApiException
     */
    public function test_api_fail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureAction();
        $api = m::mock(stdClass::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setApi($api);
    }
}
