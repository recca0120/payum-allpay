<?php

namespace PayumTW\Allpay\Tests\Action;

use Mockery as m;
use Payum\Core\Request\Sync;
use PHPUnit\Framework\TestCase;
use PayumTW\Allpay\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;

class SyncActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new SyncAction();
        $request = new Sync(new ArrayObject([]));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $gateway->shouldReceive('execute')->once()->with(m::type('PayumTW\Allpay\Request\Api\GetTransactionData'));

        $action->execute($request);

        $this->assertSame([], (array) $request->getModel());
    }
}
