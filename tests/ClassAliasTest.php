<?php

use Mockery as m;

class ClassAliasTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_ActionType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ActionType'));
    }
    public function test_CarruerType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\CarruerType'));
    }
    public function test_ClearanceMark_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ClearanceMark'));
    }
    public function test_DeviceType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\DeviceType'));
    }
    public function test_Donation_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\Donation'));
    }
    public function test_EncryptType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\EncryptType'));
    }
    public function test_ExtraPaymentInfo_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\ExtraPaymentInfo'));
    }
    public function test_InvoiceState_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\InvoiceState'));
    }
    public function test_InvType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\InvType'));
    }
    public function test_PaymentMethod_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PaymentMethod'));
    }
    public function test_PaymentMethodItem_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PaymentMethodItem'));
    }
    public function test_PeriodType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PeriodType'));
    }
    public function test_PrintMark_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\PrintMark'));
    }
    public function test_TaxType_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\TaxType'));
    }
    public function test_UseRedeem_exists() {
        $this->assertTrue(class_exists('PayumTW\Allpay\Bridge\Allpay\UseRedeem'));
    }
}
