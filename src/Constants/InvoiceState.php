<?php

namespace PayumTW\Allpay\Constants;

/**
 * 電子發票開立註記。
 */
abstract class InvoiceState
{
    /**
     * 需要開立電子發票。
     */
    const Yes = 'Y';

    /**
     * 不需要開立電子發票。
     */
    const No = '';
}
