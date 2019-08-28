<?php
set_time_limit(0);

use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Constants\ApiResponseCodes;

$heidelpay = new \heidelpayPHP\Heidelpay(SdkRestApi::getParam('privateKey'));

try {
    $transaction = $heidelpay->ship(
        SdkRestApi::getParam('paymentId'),
        SdkRestApi::getParam('invoiceId')
    );

    if (!$transaction->isError()) {
        return [
            'success' => true,
        ];
    }

    return [
        'merchantMessage' => $transaction->getMessage()->getCustomer(),
        'messageCode' => $transaction->getMessage()->getCode()
    ];
} catch (HeidelpayApiException $e) {
    return [
        'merchantMessage' => $e->getMerchantMessage(),
        'clientMessage' => $e->getClientMessage(),
        'code' => $e->getCode()
    ];
} catch (Exception $e) {
    return [
        'merchantMessage' => $e->getMessage(),
        'code' => $e->getCode()
    ];
}
