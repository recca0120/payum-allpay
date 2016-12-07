<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Allpay\Action\Api\CancelTransactionAction;

class CancelTransactionActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_execute()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('PayumTW\Allpay\Request\Api\CancelTransaction, ArrayAccess');
        $api = m::spy('PayumTW\Allpay\Api');
        $input = [];
        $details = new ArrayObject($input);

        $endpoint = 'foo.endpoint';
        $data = ['foo.data'];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $api
            ->shouldReceive('cancelTransaction')->andReturn($details);

        $action = new CancelTransactionAction();
        $action->setApi($api);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $action->execute($request);
        $request->shouldHaveReceived('getModel')->twice();
        $api->shouldHaveReceived('cancelTransaction')->with($input)->once();
    }
}
