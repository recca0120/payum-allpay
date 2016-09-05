<?php

namespace PayumTW\Allpay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use PayumTW\Allpay\LogisticsApi as Api;

class CaptureLogisticsAction extends GatewayAwareAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use ApiAwareTrait,
        GatewayAwareTrait,
        GenericTokenFactoryAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof Api) {
            throw new UnsupportedApiException(sprintf('Not supported. Expected %s instance to be set as api.', Api::class));
        }

        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        if (isset($httpRequest->request['CVSStoreID']) === true) {
            $model->replace($this->api->parseResult($httpRequest->request));

            return;
        }

        $token = $request->getToken();
        $targetUrl = $token->getTargetUrl();
        // 無 SenderName 時則執行地圖查詢
        if (empty($model['SenderName']) === true) {
            if (empty($model['ServerReplyURL']) === true) {
                $model['ServerReplyURL'] = $targetUrl;
            }
            $params = $this->api->prepareMap($model->toUnsafeArray());

            throw new HttpPostRedirect(
                $params['apiEndpoint'],
                $params['params']
            );
        }

        if (empty($model['ServerReplyURL']) === true) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $token->getGatewayName(),
                $token->getDetails()
            );

            $model['ServerReplyURL'] = $model['LogisticsC2CReplyURL'] = $notifyToken->getTargetUrl();
        }

        if (empty($model['ClientReplyURL']) === true) {
            $model['ClientReplyURL'] = $targetUrl;
        }

        $payment = $this->api->preparePayment($model->toUnsafeArray());
        $model->replace($this->api->parseResult($payment));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
