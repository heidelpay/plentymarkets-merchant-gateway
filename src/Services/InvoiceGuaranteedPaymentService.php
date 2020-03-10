<?php

namespace HeidelpayMGW\Services;

use HeidelpayMGW\Helpers\Loggable;
use HeidelpayMGW\Helpers\OrderHelper;
use Plenty\Modules\Order\Models\Order;
use HeidelpayMGW\Helpers\SessionHelper;
use Plenty\Modules\Order\Models\OrderItem;
use HeidelpayMGW\Models\PaymentInformation;
use Plenty\Modules\Document\Models\Document;
use HeidelpayMGW\Configuration\PluginConfiguration;
use Plenty\Modules\Order\Property\Models\OrderItemProperty;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use HeidelpayMGW\Repositories\InvoiceGuaranteedSettingRepository;

/**
 * Invoice Guaranteed payment service class
 *
 * Copyright (C) 2019 heidelpay GmbH
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
class InvoiceGuaranteedPaymentService extends AbstractPaymentService
{
    use Loggable;

    /** @var SessionHelper $sessionHelper  Saves information for current plugin session */
    private $sessionHelper;

    /** @var OrderHelper $orderHelper  Order manipulation with AuthHelper */
    private $orderHelper;

    /**
     * InvoiceGuaranteedPaymentService constructor
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
        /** @var array $data */
        $data = parent::prepareChargeRequest($payment);
        /** @var array $libResponse */
        $libResponse = $this->libCall->call(PluginConfiguration::PLUGIN_NAME.'::invoiceGuaranteed', $data);
        
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
     * Make API call to cancel charge
     *
     * @param PaymentInformation $paymentInformation  Heidelpay payment information
     * @param Order $order  Plenty Order
     * @param int $originalOrderId Original Plenty Order ID
     *
     * @return array  Response from SDK
     */
    public function cancelTransaction(PaymentInformation $paymentInformation, Order $order, int $originalOrderId): array
    {
        /** @var array $data */
        $data = parent::prepareCancelTransactionRequest($paymentInformation, $order);

        if ($paymentInformation->paymentMethod === PluginConfiguration::INVOICE_FACTORING) {
            /** @var InvoiceGuaranteedSettingRepository $invoiceGuaranteedSettingRepo */
            $invoiceGuaranteedSettingRepo = pluginApp(InvoiceGuaranteedSettingRepository::class);
            $reason = '';
            /** @var OrderItem $item */
            foreach ($order->orderItems as $item) {
                /** @var OrderItemProperty $property */
                foreach ($item->properties as $property) {
                    if ($property->typeId === OrderPropertyType::RETURNS_REASON) {
                        /** @var string $reason */
                        $reason = $invoiceGuaranteedSettingRepo->getReturnCode($property->value);
                    }
                }
            }
            $data['reason'] = $reason;
        }
        /** @var array $libResponse */
        $libResponse = $this->libCall->call(PluginConfiguration::PLUGIN_NAME.'::cancelTransaction', $data);
        /** @var string $commentText */
        $commentText = implode('<br />', [
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.addedByPlugin'),
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.successCancelAmount') . $data['amount']
        ]);
        
        if (!empty($libResponse['merchantMessage'])) {
            $commentText = implode('<br />', [
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.addedByPlugin'),
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.cancelTransactionError'),
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.merchantMessage') . $libResponse['merchantMessage']
            ]);
            $this->getLogger(__METHOD__)->error(
                PluginConfiguration::PLUGIN_NAME.'::translation.cancelTransactionError',
                [
                    'data' => $data,
                    'libResponse' => $libResponse
                ]
            );
        }
        $this->createOrderComment($originalOrderId, $commentText);

        $this->getLogger(__METHOD__)->debug(
            'translation.cancelTransaction',
            [
                'data' => $data,
                'libResponse' => $libResponse
            ]
        );
        
        return $libResponse;
    }

    /**
     * Update plentymarkets Order with external Order ID and comment
     *
     * @param int $orderId  Plenty Order ID
     * @param string $externalOrderId  Heidelpay Order ID
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
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.transferTo'),
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.iban') . $transaction['iban'],
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.bic') . $transaction['bic'],
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.holder') . $transaction['holder'],
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.descriptor') . $transaction['descriptor']
        ]);
        $this->createOrderComment($orderId, $commentText);
    }

    /**
     * Make API call ship to finalize transaction
     *
     * @param PaymentInformation $paymentInformation  Heidelpay payment information
     * @param integer $orderId  Plenty Order ID
     *
     * @throws Exception
     *
     * @return array
     */
    public function ship(PaymentInformation $paymentInformation, int $orderId): array
    {
        /** @var Order $order */
        $order = $this->orderHelper->findOrderById($orderId);
        $invoiceId = '';
        /** @var Document $document */
        foreach ($order->documents as $document) {
            if ($document->type ===  Document::INVOICE) {
                $invoiceId = $document->numberWithPrefix;
            }
        }

        if (empty($invoiceId)) {
            throw new \Exception($this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.noInvoice'));
        }

        /** @var array $libResponse */
        $libResponse = $this->libCall->call(PluginConfiguration::PLUGIN_NAME.'::invoiceShip', [
            'privateKey' => $this->apiKeysHelper->getPrivateKey(),
            'paymentId' => $paymentInformation->transaction['paymentId'],
            'invoiceId' => $invoiceId
        ]);

        $this->getLogger(__METHOD__)->debug(
            'translation.shipmentCall',
            [
                'orderId' => $orderId,
                'paymentId' => $paymentInformation->transaction['paymentId'],
                'invoiceId' => $invoiceId,
                'libResponse' => $libResponse
            ]
        );
        /** @var string $commentText */
        $commentText = implode('<br />', [
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.addedByPlugin'),
            $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.successShip')
        ]);

        
        if (!$libResponse['success']) {
            $this->getLogger(__METHOD__)->error(
                PluginConfiguration::PLUGIN_NAME.'translation.errorShip',
                [
                    'error' => $libResponse
                ]
            );

            $commentText = implode('<br />', [
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.addedByPlugin'),
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.errorShip'),
                $this->translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.merchantMessage') . $libResponse['merchantMessage']
            ]);
        }

        $this->createOrderComment($orderId, $commentText);

        return $libResponse;
    }
}
