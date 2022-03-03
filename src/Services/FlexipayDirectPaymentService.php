<?php

namespace HeidelpayMGW\Services;

use HeidelpayMGW\Helpers\Loggable;
use HeidelpayMGW\Helpers\OrderHelper;
use HeidelpayMGW\Helpers\SessionHelper;
use HeidelpayMGW\Models\PaymentInformation;
use HeidelpayMGW\Configuration\PluginConfiguration;

/**
 * FlexiPay Direct payment service
 *
 * Copyright (C) 2020 heidelpay GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link https://docs.heidelpay.com/
 *
 * @package  heidelpayMGW/services
 *
 * @author Rimantas <development@heidelpay.com>
 */
class FlexipayDirectPaymentService extends AbstractPaymentService
{
    use Loggable;

    /** @var SessionHelper $sessionHelper  Saves information for current plugin session */
    private $sessionHelper;

    /** @var OrderHelper $orderHelper  Order manipulation with AuthHelper */
    private $orderHelper;

    /**
     * FlexipayDirectPaymentService constructor
     *
     * @param SessionHelper $sessionHelper  Saves information for current plugin session
     * @param OrderHelper $orderHelper  Order manipulation with AuthHelper
     */
    public function __construct(
        SessionHelper $sessionHelper,
        OrderHelper $orderHelper
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->orderHelper = $orderHelper;

        parent::__construct();
    }

    /**
     * Make a charge call with HeidelpayMGW PHP-SDK
     *
     * @param array $payment  Payment type information from Frontend JS
     *
     * @return array  Payment information from SDK
     */
    public function charge(array $payment): array
    {
        $data = $this->prepareChargeRequest($payment);

        $libResponse = $this->libCall->call(PluginConfiguration::PLUGIN_NAME.'::charge', $data);

        $this->getLogger(__METHOD__)->debug(
            'translation.charge',
            [
                'data' => $data,
                'libResponse' => $libResponse
            ]
        );
        
        return $libResponse;
    }

    /**
     * Prepare information for HeidelpayMGW charge call
     *
     * @param array $payment
     *
     * @return array
     */
    public function prepareChargeRequest(array $payment)
    {
        $data = parent::prepareChargeRequest($payment);
        $data['route'] = parent::getBaseUrl().'/'.PluginConfiguration::PLUGIN_NAME.'/process-redirect';

        return $data;
    }

    /**
     * Update plentymarkets Order with external Order ID and comment
     *
     * @param int $orderId
     * @param string $externalOrderId
     *
     * @return void
     */
    public function addExternalOrderId(int $orderId, string $externalOrderId)
    {
        parent::addExternalOrderId($orderId, $externalOrderId);
        /** @var array $transaction */
        $transaction = $this->sessionHelper->getValue('paymentInformation')['transaction'];
        if (empty($transaction)) {
            return;
        }
        /** @var string $commentText */
        $commentText = implode('<br />', [
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.addedByPlugin'),
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.paymentCompleted'),
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.shortId') . $transaction['shortId'],
        ]);
        $this->createOrderComment($orderId, $commentText);
    }

    /**
     * Make API call ship to finalize transaction
     *
     * @param PaymentInformation $paymentInformation  heidelpay payment information
     * @param integer $orderId  Plenty Order ID
     *
     * @return array
     */
    public function ship(PaymentInformation $paymentInformation, int $orderId): array
    {
        return parent::ship($paymentInformation, $orderId);
    }
}
