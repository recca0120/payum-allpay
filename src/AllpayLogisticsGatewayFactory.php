<?php

namespace PayumTW\Allpay;

use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ecpay\EcpayLogisticsGatewayFactory;

class AllpayLogisticsGatewayFactory extends EcpayLogisticsGatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        parent::populateConfig($config);
        $config->replace([
            'payum.factory_name' => 'allpay_logistics',
            'payum.factory_title' => 'Allpay Logistics',
        ]);
    }
}
