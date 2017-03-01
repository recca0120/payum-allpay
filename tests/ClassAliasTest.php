<?php

namespace PayumTW\Allpay\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class ClassAliasTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testActionTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ActionType'));
    }

    public function testCarruerTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\CarruerType'));
    }

    public function testClearanceMarkExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ClearanceMark'));
    }

    public function testDeviceTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\DeviceType'));
    }

    public function testDonationExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\Donation'));
    }

    public function testEncryptTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\EncryptType'));
    }

    public function testExtraPaymentInfoExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ExtraPaymentInfo'));
    }

    public function testInvoiceStateExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\InvoiceState'));
    }

    public function testInvTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\InvType'));
    }

    public function testPaymentMethodExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PaymentMethod'));
    }

    public function testPaymentMethodItemExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PaymentMethodItem'));
    }

    public function testPeriodTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PeriodType'));
    }

    public function testPrintMarkExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PrintMark'));
    }

    public function testTaxTypeExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\TaxType'));
    }

    public function testUseRedeemExists()
    {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\UseRedeem'));
    }
}
