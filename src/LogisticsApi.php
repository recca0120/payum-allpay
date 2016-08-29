<?php

namespace PayumTW\Allpay;

use Detection\MobileDetect;
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
    * @param array $fields
    *
    * @return array
    */
   protected function doRequest($method, array $fields)
   {
       $headers = [];
       $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));
       $response = $this->client->send($request);
       if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
           throw HttpException::factory($request, $response);
       }

       return $response;
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
            'ServerReplyURL'    => '',
            'MerchantTradeNo'   => '',
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsSubType'  => LogisticsSubType::UNIMART,
            'IsCollection'      => IsCollection::NO,
            'Device'            => $this->isMobile() ? Device::MOBILE : Device::PC,
        ]);

        $api->Send = array_replace(
            $api->Send,
            array_intersect_key($params, $api->Send)
        );

        $params = $api->CvsMap();

        return [
            'apiEndpoint' => $api->ServiceURL,
            'params'      => $params,
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
    public function payment(array $params)
    {
        switch ($params['LogisticsSubType']) {
            case LogisticsSubType::TCAT:
                $params['LogisticsType'] = LogisticsType::HOME;
                break;
            default:
                $params['LogisticsType'] = LogisticsType::CVS;
                break;
        }

        if ($params['IsCollection'] === IsCollection::NO) {
            $params['CollectionAmount'] = 0;
        } elseif (isset($params['CollectionAmount']) === false) {
            $params['CollectionAmount'] = (int) $params['GoodsAmount'];
        }

        $api = $this->getApi();
        $api->Send = array_merge($api->Send, [
            'MerchantTradeNo'      => '',
            'MerchantTradeDate'    => date('Y/m/d H:i:s'),
            'LogisticsType'        => '',
            'LogisticsSubType'     => LogisticsSubType::UNIMART,
            'GoodsAmount'          => 0,
            'CollectionAmount'     => 0,
            'IsCollection'         => IsCollection::NO,
            'GoodsName'            => '',
            'SenderName'           => '',
            'SenderPhone'          => '',
            'SenderCellPhone'      => '',
            'ReceiverName'         => '',
            'ReceiverPhone'        => '',
            'ReceiverCellPhone'    => '',
            'ReceiverEmail'        => '',
            'TradeDesc'            => '',
            'ServerReplyURL'       => '',
            'LogisticsC2CReplyURL' => '',
            'Remark'               => '',
            'PlatformID'           => '',
        ]);

        $api->SendExtend = [];

        switch ($params['LogisticsType']) {
            case LogisticsType::HOME:
                $api->SendExtend = array_merge($api->SendExtend, [
                    'SenderZipCode'         => '',
                    'SenderAddress'         => '',
                    'ReceiverZipCode'       => '',
                    'ReceiverAddress'       => '',
                    'Temperature'           => '',
                    'Distance'              => '',
                    'Specification'         => '',
                    'ScheduledDeliveryTime' => '',
                ]);
                break;
            case LogisticsType::CVS:
                $api->SendExtend = array_merge($api->SendExtend, [
                    'ReceiverStoreID' => '',
                    'ReturnStoreID'   => '',
                ]);
                break;
        }

        $api->Send = array_replace(
            $api->Send,
            array_intersect_key($params, $api->Send)
        );

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
            $params['ResCode'] = '10400002';
        }

        $params['statusReason'] = array_get($params, 'RtnMsg');
        // $params['statusReason'] = preg_replace('/(\.|。)$/', '', $this->getStatusReason($params['ResCode']));

        return $params;
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

        return $statusReason;
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
