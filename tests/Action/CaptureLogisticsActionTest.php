<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryInterface;
use PayumTW\Allpay\Action\CaptureLogisticsAction;
use PayumTW\Allpay\LogisticsApi;

class CaptureLogisticsActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_redirect_csv_map()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureLogisticsAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Capture::class);
        $tokenFactory = m::mock(GenericTokenFactoryInterface::class);
        $token = m::mock(stdClass::class);
        $notifyToken = m::mock(stdClass::class);
        $api = m::mock(LogisticsApi::class);
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
            ->shouldReceive('getTargetUrl')->andReturn('fooTragetURL')
            ->shouldReceive('getGatewayName')->andReturn('fooGatewayName')
            ->shouldReceive('getDetails')->andReturn([
                'foo' => 'bar',
            ]);

        $api->shouldReceive('prepareMap')->once()->andReturn([
            'apiEndpoint' => 'fooApiEndpoint',
            'params' => [
                'foo' => 'bar',
            ],
        ]);

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
        }
    }

    public function test_receive_csv_map()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureLogisticsAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Capture::class);
        $tokenFactory = m::mock(GenericTokenFactoryInterface::class);
        $token = m::mock(stdClass::class);
        $notifyToken = m::mock(stdClass::class);
        $api = m::mock(LogisticsApi::class);
        $model = new ArrayObject([]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway->shouldReceive('execute')->with(GetHttpRequest::class)->once()->andReturnUsing(function ($request) {
            $request->request = [
                'CVSStoreID' => 'fooCVSStoreID',
            ];

            return $request;
        });

        $request->shouldReceive('getModel')->twice()->andReturn($model);

        $api->shouldReceive('parseResult')->once()->andReturn($model);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setApi($api);
        $action->setGenericTokenFactory($tokenFactory);
        $action->execute($request);
    }

    public function test_request_logistics()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $action = new CaptureLogisticsAction();
        $gateway = m::mock(GatewayInterface::class);
        $request = m::mock(Capture::class);
        $tokenFactory = m::mock(GenericTokenFactoryInterface::class);
        $token = m::mock(stdClass::class);
        $notifyToken = m::mock(stdClass::class);
        $api = m::mock(LogisticsApi::class);
        $model = new ArrayObject([
            'SenderName' => 'fooSenderName',
        ]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $gateway->shouldReceive('execute')->with(GetHttpRequest::class)->once()->andReturnUsing(function ($request) {
            $request->request = [];

            return $request;
        });

        $request
            ->shouldReceive('getModel')->twice()->andReturn($model)
            ->shouldReceive('getToken')->once()->andReturn($token);

        $token
            ->shouldReceive('getTargetUrl')->once()->andReturn('fooTargetUrl')
            ->shouldReceive('getGatewayName')->once()->andReturn('foogGatewayName')
            ->shouldReceive('getDetails')->once()->andReturn([]);

        $tokenFactory->shouldReceive('createNotifyToken')->once()->andReturn($notifyToken);

        $notifyToken->shouldReceive('getTargetUrl')->once()->andReturn('fooNotifyTargetUrl');

        $api
            ->shouldReceive('preparePayment')->once()->andReturn($model)
            ->shouldReceive('parseResult')->once()->andReturn($model);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $action->setGateway($gateway);
        $action->setApi($api);
        $action->setGenericTokenFactory($tokenFactory);
        $action->execute($request);
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

        $action = new CaptureLogisticsAction();
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
