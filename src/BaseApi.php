<?php

namespace PayumTW\Allpay;

use Exception;
use Detection\MobileDetect;

abstract class BaseApi
{
    /**
     * Verify if the hash of the given parameter is correct.
     *
     * @param array $params
     *
     * @return bool
     */
    public function verifyHash(array $params)
    {
        $result = false;
        try {
            $this->api->CheckOutFeedback($params);
            $result = true;
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * getStatusReason.
     *
     * @param string $code
     *
     * @return string
     */
    protected function getStatusReason($code)
    {
        $statusReason = '拒絕交易';
        if (isset($this->code[$code]) === true) {
            $statusReason = $this->code[$code];
        }

        return preg_replace('/(\.|。)$/', '', $statusReason);
    }

    /**
     * isMobile.
     *
     * @return bool
     */
    protected function isMobile()
    {
        $detect = new MobileDetect();

        return ($detect->isMobile() === false && $detect->isTablet() === false) ? false : true;
    }
}
