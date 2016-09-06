<?php

namespace PayumTW\Allpay;

use Device;
use Exception;
use Http\Message\MessageFactory;
use IsCollection;
use LogisticsSubType;
use LogisticsType;
use Payum\Core\HttpClientInterface;
use PayumTW\Allpay\Bridge\Allpay\AllpayLogistics;

class LogisticsApi extends BaseApi
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $code = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * getApi.
     *
     * @method getApi
     *
     * @return \PayumTW\Allpay\Bridge\Allpay\AllpayLogistics
     */
    protected function getApi()
    {
        $api = new AllpayLogistics();
        $api->HashKey = $this->options['HashKey'];
        $api->HashIV = $this->options['HashIV'];
        $api->Send['MerchantID'] = $this->options['MerchantID'];

        return $api;
    }

    /**
     * prepareMap.
     *
     * @method prepareMap
     *
     * @param array $params
     *
     * @return array
     */
    public function prepareMap(array $params)
    {
        $api = $this->getApi();
        $api->Send = array_merge($api->Send, [
            'ServerReplyURL' => '',
            'MerchantTradeNo' => '',
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsSubType' => LogisticsSubType::UNIMART,
            'IsCollection' => IsCollection::NO,
            'Device' => $this->isMobile() ? Device::MOBILE : Device::PC,
        ]);

        $api->Send = array_replace(
            $api->Send,
            array_intersect_key($params, $api->Send)
        );

        $params = $api->CvsMap();

        return [
            'apiEndpoint' => $api->ServiceURL,
            'params' => $params,
        ];
    }

    /**
     * payment.
     *
     * @param array $params
     * @param mixed $request
     *
     * @return array
     */
    public function preparePayment(array $params)
    {
        $api = $this->getApi();
        $api->Send = array_merge($api->Send, [
            'MerchantTradeNo' => '',
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => '',
            'LogisticsSubType' => LogisticsSubType::UNIMART,
            'GoodsAmount' => 0,
            'CollectionAmount' => 0,
            'IsCollection' => IsCollection::NO,
            'GoodsName' => '',
            'SenderName' => '',
            'SenderPhone' => '',
            'SenderCellPhone' => '',
            'ReceiverName' => '',
            'ReceiverPhone' => '',
            'ReceiverCellPhone' => '',
            'ReceiverEmail' => '',
            'TradeDesc' => '',
            'ServerReplyURL' => '',
            'LogisticsC2CReplyURL' => '',
            'Remark' => '',
            'PlatformID' => '',
        ]);

        $api->SendExtend = [];

        $api->Send = array_replace(
            $api->Send,
            array_intersect_key($params, $api->Send)
        );

        if (empty($api->Send['LogisticsType']) === true) {
            $api->Send['LogisticsType'] = LogisticsType::CVS;
            switch ($api->Send['LogisticsSubType']) {
                case LogisticsSubType::TCAT:
                    $api->Send['LogisticsType'] = LogisticsType::HOME;
                    break;
            }
        }

        if ($api->Send['IsCollection'] === IsCollection::NO) {
            $api->Send['CollectionAmount'] = 0;
        } elseif (isset($api->Send['CollectionAmount']) === false) {
            $api->Send['CollectionAmount'] = (int) $api->Send['GoodsAmount'];
        }

        switch ($api->Send['LogisticsType']) {
            case LogisticsType::HOME:
                $api->SendExtend = array_merge($api->SendExtend, [
                    'SenderZipCode' => '',
                    'SenderAddress' => '',
                    'ReceiverZipCode' => '',
                    'ReceiverAddress' => '',
                    'Temperature' => '',
                    'Distance' => '',
                    'Specification' => '',
                    'ScheduledDeliveryTime' => '',
                ]);
                break;
            case LogisticsType::CVS:
                $api->SendExtend = array_merge($api->SendExtend, [
                    'ReceiverStoreID' => '',
                    'ReturnStoreID' => '',
                ]);
                break;
        }

        $api->SendExtend = array_replace(
            $api->SendExtend,
            array_intersect_key($params, $api->SendExtend)
        );

        return $api->BGCreateShippingOrder();
    }

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
            $api = $this->getApi();
            $api->CheckOutFeedback($params);
            $result = true;
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * parseResult.
     *
     * @param mixed $params
     *
     * @return array
     */
    public function parseResult($params)
    {
        if (isset($params['CVSStoreID']) === true) {
            return $params;
        }

        if ($this->verifyHash($params) === false) {
            $params['RtnCode'] = '10400002';
        }

        $params['statusReason'] = isset($params['RtnMsg']) === true ? $params['RtnMsg'] : '配送異常，請和客服聯繫';
        // $params['statusReason'] = preg_replace('/(\.|。)$/', '', $this->getStatusReason($params['ResCode']));

        return $params;
    }
}
