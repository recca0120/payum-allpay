<?php

namespace PayumTW\Allpay\Bridge\Allpay;

use Device;
use LogisticsType;

class AllpayLogistics extends \AllpayLogistics
{
    // 電子地圖
    public function CvsMap($ButtonDesc = '電子地圖', $Target = '_self')
    {
        // 參數初始化
        $ParamList = [
            'MerchantID' => '',
            'MerchantTradeNo' => '',
            'LogisticsSubType' => '',
            'IsCollection' => '',
            'ServerReplyURL' => '',
            'ExtraData' => '',
            'Device' => Device::PC,
        ];
        $this->PostParams = $this->GetPostParams($this->Send, $ParamList);
        $this->PostParams['LogisticsType'] = LogisticsType::CVS;

        // 參數檢查
        $this->ValidateID('MerchantID', $this->PostParams['MerchantID'], 10);
        $this->ServiceURL = $this->GetURL('CVS_MAP');
        $this->ValidateMerchantTradeNo();
        $this->ValidateLogisticsSubType();
        $this->ValidateIsCollection();
        $this->ValidateURL('ServerReplyURL', $this->PostParams['ServerReplyURL']);
        $this->ValidateString('ExtraData', $this->PostParams['ExtraData'], 20, true);
        $this->ValidateDevice(true);

        return $this->PostParams;
    }
}
