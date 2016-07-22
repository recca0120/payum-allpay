<?php

namespace PayumTW\Allpay\Constants;

/**
 * 定期定額的週期種類。
 */
abstract class PeriodType
{
    /**
     * 無
     */
    const None = '';

    /**
     * 年.
     */
    const Year = 'Y';

    /**
     * 月.
     */
    const Month = 'M';

    /**
     * 日.
     */
    const Day = 'D';
}
