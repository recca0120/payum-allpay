<?php

namespace PayumTW\Allpay\Tests\Action;

use Mockery as m;
use Payum\Core\Request\Convert;
use PHPUnit\Framework\TestCase;
use PayumTW\Allpay\Action\ConvertPaymentAction;

class ConvertPaymentActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new ConvertPaymentAction();
        $request = new Convert(
            $payment = m::mock('Payum\Core\Model\PaymentInterface'),
            $to = 'array'
        );
        $payment->shouldReceive('getDetails')->once()->andReturn([]);
        $payment->shouldReceive('getNumber')->once()->andReturn($number = 'foo');
        $payment->shouldReceive('getTotalAmount')->once()->andReturn($totalAmount = 'foo');
        $payment->shouldReceive('getDescription')->once()->andReturn($description = 'foo');

        $action->execute($request);
        $this->assertSame([
            'MerchantTradeNo' => $number,
            'TotalAmount' => $totalAmount,
            'TradeDesc' => $description,
        ], $request->getResult());
    }
}
