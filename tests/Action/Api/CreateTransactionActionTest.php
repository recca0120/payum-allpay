<?php

use Mockery as m;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Allpay\Action\Api\CreateTransactionAction;

class CreateTransactionActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_api_http_post_redirect()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Allpay\Api');
        $request = m::spy('PayumTW\Allpay\Request\Api\CreateTransaction');
        $details = new ArrayObject([
            'foo' => 'bar',
        ]);
        $endpoint = 'foo.endpoint';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $api
            ->shouldReceive('createTransaction')->andReturn((array) $details)
            ->shouldReceive('getApiEndpoint')->andReturn($endpoint);

        $action = new CreateTransactionAction();
        $action->setApi($api);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        try {
            $action->execute($request);
        } catch (HttpResponse $response) {
            $this->assertSame((array) $details, $response->getFields());
        }

        $request->shouldHaveReceived('getModel')->twice();
        $api->shouldHaveReceived('createTransaction')->with((array) $details)->once();
        $api->shouldHaveReceived('getApiEndpoint')->once();
    }
}
