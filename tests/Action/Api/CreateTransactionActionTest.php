<?php

namespace PayumTW\Allpay\Tests\Action\Api;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Allpay\Request\Api\CreateTransaction;
use PayumTW\Allpay\Action\Api\CreateTransactionAction;

class CreateTransactionActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new CreateTransactionAction();
        $request = new CreateTransaction(new ArrayObject([
            'GoodsAmount' => 100,
        ]));

        $action->setApi(
            $api = m::mock('PayumTW\Allpay\Api')
        );

        $api->shouldReceive('createTransaction')->once()->with((array) $request->getModel())->andReturn($result = ['foo' => 'bar']);
        $api->shouldReceive('getApiEndpoint')->once()->andReturn($apiEndpoint = 'foo');

        try {
            $action->execute($request);
        } catch (HttpResponse $e) {
            $this->assertSame($apiEndpoint, $e->getUrl());
            $this->assertSame($result, $e->getFields());
        }
    }
}
