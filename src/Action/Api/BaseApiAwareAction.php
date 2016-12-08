<?php

namespace PayumTW\Allpay\Action\Api;

use PayumTW\Allpay\Api;
use PayumTW\Allpay\LogisticsApi;

use Payum\Core\ApiAwareInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\UnsupportedApiException;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var \Payum\Allpay\Api
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (true == $api instanceof Api || true == $api instanceof LogisticsApi) {
            $this->api = $api;
        } else {
            throw new UnsupportedApiException('Not supported.');
        }
    }
}
