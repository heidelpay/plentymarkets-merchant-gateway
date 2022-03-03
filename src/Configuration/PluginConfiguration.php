<?php

namespace HeidelpayMGW\Configuration;

/**
* Constants for plugin
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
* @package  heidelpayMGW/configuration
*/
class PluginConfiguration
{
    // Plugin
    const PLUGIN_NAME = 'HeidelpayMGW';
    const PLUGIN_VERSION = '1.0.3';
    const PLUGIN_KEY = 'HeidelpayMGWPaymentPlugin';

    // Plugin payment methods
    const PAYMENT_KEY_INVOICE = "HeidelpayMGWINVOICE";
    const INVOICE_FRONTEND_NAME = "HeidelpayMGWInvoice";
    const PAYMENT_KEY_INVOICE_GUARANTEED = "HeidelpayMGWINVOICEGUARANTEED";
    const INVOICE_GUARANTEED_FRONTEND_NAME = "HeidelpayMGWInvoiceGuaranteed";
    const PAYMENT_KEY_INVOICE_GUARANTEED_B2B = "HeidelpayMGWINVOICEGUARANTEEDB2B";
    const INVOICE_GUARANTEED_FRONTEND_NAME_B2B = "HeidelpayMGWInvoiceGuaranteedB2b";
    const PAYMENT_KEY_CARDS = "HeidelpayMGWCARDS";
    const CARDS_FRONTEND_NAME = "HeidelpayMGWCards";
    const PAYMENT_KEY_SEPA_DIRECT_DEBIT = "HeidelpayMGWSEPADIRECTDEBIT";
    const DIRECT_DEBIT_FRONTEND_NAME = "HeidelpayMGWSepaDirectDebit";
    const PAYMENT_KEY_SEPA_DIRECT_DEBIT_GUARANTEED = "HeidelpayMGWSEPADIRECTDEBITGUARANTEED";
    const DIRECT_DEBIT_GUARANTEED_FRONTEND_NAME = "HeidelpayMGWSepaDirectDebitGuaranteed";
    const PAYMENT_KEY_PAYPAL = "HeidelpayMGWPAYPAL";
    const PAYPAL_FRONTEND_NAME = "HeidelpayMGWPaypal";
    const PAYMENT_KEY_IDEAL = "HeidelpayMGWIDEAL";
    const IDEAL_FRONTEND_NAME = "HeidelpayMGWiDEAL";
    const PAYMENT_KEY_SOFORT = "HeidelpayMGWSOFORT";
    const SOFORT_FRONTEND_NAME = "HeidelpayMGWSOFORT";
    const PAYMENT_KEY_FLEXIPAY_DIRECT = "HeidelpayMGWFLEXIPAYDIRECT";
    const FLEXIPAY_DIRECT_FRONTEND_NAME = "HeidelpayMGWFlexiPayDirect";
    
    // heidelpay payment methods
    const INVOICE_GUARANTEED = 'invoice-guaranteed';
    const INVOICE = 'invoice';

    // Payment mode
    const CHARGE = 'charge';
    const AUTHORIZATION_CAPTURE = 'authorizationCapture';
}
