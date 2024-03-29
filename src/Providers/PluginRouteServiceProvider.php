<?php

namespace HeidelpayMGW\Providers;

use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;
use Plenty\Plugin\RouteServiceProvider;
use HeidelpayMGW\Controllers\RedirectController;
use HeidelpayMGW\Controllers\WebhooksController;
use HeidelpayMGW\Configuration\PluginConfiguration;
use HeidelpayMGW\Controllers\PaymentTypeController;

/**
 * Handles plugin's routing
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
 * @package  heidelpayMGW/providers
 *
 * @author Rimantas <development@heidelpay.com>
 */
class PluginRouteServiceProvider extends RouteServiceProvider
{
    /**
     * Map routes to controllers
     *
     * @param Router $router  Unprotected routes
     * @param ApiRouter $apiRouter  Protected routes for settings UI
     *
     * @return void
     */
    public function map(
        Router $router,
        ApiRouter $apiRouter
    ) {
        $apiRouter->version(
            ['v1'],
            ['namespace' => 'HeidelpayMGW\Controllers', 'middleware' => 'oauth'],
            function ($apiRouter) {
                // Plugin settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/plugin-settings', 'PluginSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/plugin-settings', 'PluginSettingsController@saveSettings');
                // Invoice settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/invoice-settings', 'InvoiceSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/invoice-settings', 'InvoiceSettingsController@saveSettings');
                // Invoice guaranteed B2C settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/invoice-guaranteed-settings', 'InvoiceGuaranteedSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/invoice-guaranteed-settings', 'InvoiceGuaranteedSettingsController@saveSettings');
                // Invoice guaranteed B2B settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/invoice-guaranteed-b2b-settings', 'InvoiceGuaranteedB2bSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/invoice-guaranteed-b2b-settings', 'InvoiceGuaranteedB2bSettingsController@saveSettings');
                // Credit / Debit card settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/cards-settings', 'CardsSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/cards-settings', 'CardsSettingsController@saveSettings');
                // SEPA Direct Debit settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/sepa-direct-debit-settings', 'SepaDirectDebitSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/sepa-direct-debit-settings', 'SepaDirectDebitSettingsController@saveSettings');
                // SEPA Direct Debit guaranteed settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/sepa-direct-debit-guaranteed-settings', 'SepaDirectDebitGuaranteedSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/sepa-direct-debit-guaranteed-settings', 'SepaDirectDebitGuaranteedSettingsController@saveSettings');
                // PayPal settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/paypal-settings', 'PaypalSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/paypal-settings', 'PaypalSettingsController@saveSettings');
                // iDEAL settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/ideal-settings', 'IdealSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/ideal-settings', 'IdealSettingsController@saveSettings');
                // Sofort settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/sofort-settings', 'SofortSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/sofort-settings', 'SofortSettingsController@saveSettings');
                // FlexiPay Direct settings
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/flexipay-direct-settings', 'FlexiPayDirectSettingsController@getSettings');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/flexipay-direct-settings', 'FlexiPayDirectSettingsController@saveSettings');

                //Test
                $apiRouter->get(PluginConfiguration::PLUGIN_NAME.'/show', 'TestController@show');
                
                //Plugin DB manipulation
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/reset', 'TestController@reset');
                $apiRouter->post(PluginConfiguration::PLUGIN_NAME.'/update', 'TestController@update');
            }
        );
        
        $router->post(PluginConfiguration::PLUGIN_NAME.'/payment-type', PaymentTypeController::class.'@heidelpayMGWPaymentType');
        $router->post(PluginConfiguration::PLUGIN_NAME.'/webhooks', WebhooksController::class.'@handleWebhook');
        $router->get(PluginConfiguration::PLUGIN_NAME.'/process-redirect', RedirectController::class.'@processRedirect');
    }
}
