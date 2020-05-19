<?php

namespace HeidelpayMGW\Containers;

use Plenty\Plugin\Templates\Twig;
use HeidelpayMGW\Helpers\ApiKeysHelper;
use HeidelpayMGW\Helpers\PaymentHelper;
use HeidelpayMGW\Configuration\PluginConfiguration;
use HeidelpayMGW\Repositories\InvoiceGuaranteedSettingRepository;
use HeidelpayMGW\Repositories\InvoiceGuaranteedB2bSettingRepository;

/**
* Returns rendered BuyNowButton twig template
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
* @link  https://docs.heidelpay.com/
*
* @author  Rimantas  <development@heidelpay.com>
*
* @package  heidelpayMGW/containers
*/
class BuyNowButtonContainer
{
    /**
     * Return rendered twig template with required data
     *
     * @param Twig $twig  Twig templating engine
     * @param PaymentHelper $paymentHelper  Payment helper class
     * @param InvoiceGuaranteedSettingRepository $invoiceGuaranteedRepo  Invoice guaranteed settings repository to get guaranteed or factoring flag
     * @param InvoiceGuaranteedB2bSettingRepository $invoiceGuaranteedB2bRepo  Invoice guaranteed B2B settings repository to get guaranteed or factoring flag
     * @param ApiKeysHelper $apiKeysHelper  Returns Api keys depending if it's sandbox or production mode
     *
     * @return string
     */
    public function call(
        Twig $twig,
        PaymentHelper $paymentHelper,
        InvoiceGuaranteedSettingRepository $invoiceGuaranteedRepo,
        InvoiceGuaranteedB2bSettingRepository $invoiceGuaranteedB2bRepo,
        ApiKeysHelper $apiKeysHelper
    ): string {
        $data = [
            'mopList' => $paymentHelper->getPaymentMethodList(),
            'publicKey' => $apiKeysHelper->getPublicKey(),
            'routeName' => PluginConfiguration::PLUGIN_NAME,
            'invoice' => PluginConfiguration::PAYMENT_KEY_INVOICE,
            'invoiceGuaranteed' => PluginConfiguration::PAYMENT_KEY_INVOICE_GUARANTEED,
            'useInvoiceFactoring' => $invoiceGuaranteedRepo->get()->guaranteedOrFactoring,
            'invoiceGuaranteedB2b' => PluginConfiguration::PAYMENT_KEY_INVOICE_GUARANTEED_B2B,
            'useInvoiceB2bFactoring' => $invoiceGuaranteedB2bRepo->get()->guaranteedOrFactoring,
            'cards' => PluginConfiguration::PAYMENT_KEY_CARDS,
            'sepaDirectDebit' => PluginConfiguration::PAYMENT_KEY_SEPA_DIRECT_DEBIT,
            'sepaDirectDebitGuaranteed' => PluginConfiguration::PAYMENT_KEY_SEPA_DIRECT_DEBIT_GUARANTEED,
            'sepaDirectDebitMandateError' => 'Please agree to SEPA Direct Debit Mandate',
            'paypal' => PluginConfiguration::PAYMENT_KEY_PAYPAL,
            'ideal' => PluginConfiguration::PAYMENT_KEY_IDEAL,
            'sofort' => PluginConfiguration::PAYMENT_KEY_SOFORT,
            'flexipayDirect' => PluginConfiguration::PAYMENT_KEY_FLEXIPAY_DIRECT
        ];

        return $twig->render(
            PluginConfiguration::PLUGIN_NAME.'::content.BuyNowButton',
            $data
        );
    }
}
