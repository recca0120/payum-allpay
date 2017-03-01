<?php

namespace PayumTW\Allpay\Tests\Action\Api;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Allpay\Request\Api\GetTransactionData;
use PayumTW\Allpay\Action\Api\GetTransactionDataAction;

class GetTransactionDataActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new GetTransactionDataAction();
        $request = new GetTransactionData(new ArrayObject([]));

        $action->setApi(
            $api = m::mock('PayumTW\Allpay\Api')
        );

        $api->shouldReceive('getTransactionData')->once()->with((array) $request->getModel())->andReturn($params = ['RepCode' => '1']);

        $action->execute($request);

        $this->assertSame($params, (array) $request->getModel());
    }
}
