<?php

namespace PayumTW\Allpay;

use Exception;
use DeviceType;
use InvoiceState;
use Detection\MobileDetect;
use Http\Message\MessageFactory;
use PayumTW\Allpay\Sdk\AllInOne;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * $client.
     *
     * @var \Payum\Core\HttpClientInterface
     */
    protected $client;

    /**
     * MessageFactory.
     *
     * @var \Http\Message\MessageFactory
     */
    protected $messageFactory;

    /**
     * $options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * $sdk.
     *
     * @var \PayumTW\Allpay\Sdk\AllInOne
     */
    protected $sdk;

    /**
     * @param array $options
     * @param \Payum\Core\HttpClientInterface $client
     * @param \Http\Message\MessageFactory $messageFactory
     * @param \PayumTW\Allpay\Sdk\AllInOne $sdk
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory, AllInOne $sdk = null)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
        $this->sdk = $sdk ?: new AllInOne();
        $this->sdk->HashKey = $this->options['HashKey'];
        $this->sdk->HashIV = $this->options['HashIV'];
        $this->sdk->MerchantID = $this->options['MerchantID'];
    }

    /**
     * getApiEndpoint.
     *
     * @return string
     */
    public function getApiEndpoint($name = 'AioCheckOut')
    {
        $map = [
            'AioCheckOut' => 'https://payment.allpay.com.tw/Cashier/AioCheckOut/V2',
            'QueryTradeInfo' => 'https://payment.allpay.com.tw/Cashier/QueryTradeInfo/V2',
            'QueryPeriodCreditCardTradeInfo' => 'https://payment.allpay.com.tw/Cashier/QueryCreditCardPeriodInfo',
            'DoAction' => 'https://payment.allpay.com.tw/CreditDetail/DoAction',
            'AioChargeback' => 'https://payment.allpay.com.tw/Cashier/AioChargeback',
        ];

        if ($this->options['sandbox'] === true) {
            $map = [
                'AioCheckOut' => 'https://payment-stage.allpay.com.tw/Cashier/AioCheckOut/V2',
                'QueryTradeInfo' => 'https://payment-stage.allpay.com.tw/Cashier/QueryTradeInfo/V2',
                'QueryPeriodCreditCardTradeInfo' => 'https://payment-stage.allpay.com.tw/Cashier/QueryCreditCardPeriodInfo',
                'DoAction' => null,
                'AioChargeback' => 'https://payment-stage.allpay.com.tw/Cashier/AioChargeback',
            ];
        }

        return $map[$name];
    }

    /**
     * createTransaction.
     *
     * @param array $params
     * @return array
     */
    public function createTransaction(array $params)
    {
        $this->sdk->ServiceURL = $this->getApiEndpoint('AioCheckOut');
        $this->sdk->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
        $this->sdk->Send['DeviceSource'] = $this->isMobile() ? DeviceType::Mobile : DeviceType::PC;
        $this->sdk->Send = array_replace(
            $this->sdk->Send,
            array_intersect_key($params, $this->sdk->Send)
        );

        /*
         * 電子發票參數
         * $this->sdk->Send['InvoiceMark'] = InvoiceState::Yes;
         * $this->sdk->SendExtend['RelateNumber'] = $MerchantTradeNo;
         * $this->sdk->SendExtend['CustomerEmail'] = 'test@allpay.com.tw';
         * $this->sdk->SendExtend['CustomerPhone'] = '0911222333';
         * $this->sdk->SendExtend['TaxType'] = TaxType::Dutiable;
         * $this->sdk->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
         * $this->sdk->SendExtend['InvoiceItems'] = array();
         *  將商品加入電子發票商品列表陣列
         * foreach ($this->sdk->Send['Items'] as $info) {
         *      array_push($this->sdk->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
         *          $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => TaxType::Dutiable));
         * }
         * $this->sdk->SendExtend['InvoiceRemark'] = '測試發票備註';
         * $this->sdk->SendExtend['DelayDay'] = '0';
         * $this->sdk->SendExtend['InvType'] = InvType::General;
         */

        return $this->sdk->formToArray(
            $this->sdk->CheckOutString()
        );
    }

    /**
     * cancelTransaction.
     *
     * @param array $params
     * @return array
     */
    public function cancelTransaction($params)
    {
        $this->sdk->ServiceURL = $this->getApiEndpoint('DoAction');
        $this->sdk->Action = array_replace(
            $this->sdk->Action,
            array_intersect_key($params, $this->sdk->Action)
        );

        return $this->sdk->DoAction();
    }

    /**
     * refundTransaction.
     *
     * @param array $params
     * @return array
     */
    public function refundTransaction($params)
    {
        $this->sdk->ServiceURL = $this->getApiEndpoint('AioChargeback');
        $this->sdk->ChargeBack = array_replace(
            $this->sdk->ChargeBack,
            array_intersect_key($params, $this->sdk->ChargeBack)
        );

        return $this->sdk->AioChargeback();
    }

    /**
     * getTransactionData.
     *
     * @param mixed $params
     * @return array
     */
    public function getTransactionData($params)
    {
        $this->sdk->ServiceURL = $this->getApiEndpoint('QueryTradeInfo');
        $this->sdk->Query['MerchantTradeNo'] = $params['MerchantTradeNo'];
        $details = $this->sdk->QueryTradeInfo();
        $details['RtnCode'] = $details['TradeStatus'] === '1' ? '1' : '2';

        return $details;
    }

    /**
     * Verify if the hash of the given parameter is correct.
     *
     * @param array $params
     * @return bool
     */
    public function verifyHash(array $params)
    {
        $result = false;
        try {
            $this->sdk->CheckOutFeedback($params);
            $result = true;
        } catch (Exception $e) {
        }

        return $result;
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
