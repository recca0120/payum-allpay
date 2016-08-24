<?php

namespace PayumTW\Allpay;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use PayumTW\Allpay\Action\CaptureAction;
use PayumTW\Allpay\Action\ConvertPaymentAction;
use PayumTW\Allpay\Action\NotifyAction;
use PayumTW\Allpay\Action\StatusAction;

class AllpayGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name'           => 'allpay',
            'payum.factory_title'          => 'Allpay',
            'payum.action.capture'         => new CaptureAction(),
            'payum.action.status'          => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.notify'          => new NotifyAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'MerchantID'   => '2000132',
                'HashKey'      => '5294y06JbISpM5x9',
                'HashIV'       => 'v77hoKGq4kWxNNIS',
                'sandbox'      => true,
            ];

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['MerchantID', 'HashKey', 'HashIV'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
