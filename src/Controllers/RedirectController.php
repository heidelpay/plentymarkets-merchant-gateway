<?php

namespace HeidelpayMGW\Controllers;

use IO\Helper\RouteConfig;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Response;
use HeidelpayMGW\Helpers\Loggable;
use HeidelpayMGW\Helpers\UrlHelper;
use HeidelpayMGW\Helpers\SessionHelper;
use HeidelpayMGW\Configuration\PluginConfiguration;
use HeidelpayMGW\Repositories\PluginSettingRepository;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Redirect URL controller
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
 * @package  heidelpayMGW/
 *
 * @author Rimantas <development@heidelpay.com>
 */
class RedirectController extends Controller
{
    use Loggable;

    /** @var SessionHelper $sessionHelper */
    private $sessionHelper;

    /** @var LibraryCallContract $libCall */
    private $libCall;

    /** @var PluginSettingRepository $pluginSettings */
    private $pluginSettings;

    /**
     * RedirectController constructor
     *
     * @param SessionHelper $sessionHelper
     * @param LibraryCallContract $libCall
     * @param PluginSettingRepository $pluginSettingRepository
     */
    public function __construct(
        SessionHelper $sessionHelper,
        LibraryCallContract $libCall,
        PluginSettingRepository $pluginSettingRepository
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->libCall = $libCall;
        $this->pluginSettings = $pluginSettingRepository->get();
    }

    /**
     * Accept HeidelpayMGW request from redirect and decide if continue or redirect to checkout
     *
     * @param Response  $response
     *
     * @return BaseResponse
     */
    public function processRedirect(Response $response): BaseResponse
    {
        $paymentInformation = $this->sessionHelper->getValue('paymentInformation');

        $libResponse = $this->libCall->call(PluginConfiguration::PLUGIN_NAME.'::checkPayment', [
            'privateKey' => $this->pluginSettings->privateKey,
            'paymentId' => $paymentInformation['transaction']['paymentId'],
        ]);

        $this->getLogger(__METHOD__)->debug(
            'translation.processRedirect',
            [
                'paymentInformation' => $paymentInformation,
                'libResponse' => $libResponse,
            ]
        );

        if (!empty($libResponse['merchantMessage']) || !empty($libResponse['clientMessage'])) {
            $this->getLogger(__METHOD__)->error(
                PluginConfiguration::PLUGIN_NAME.'::translation.processRedirectError',
                [
                    'libResponse' => $libResponse,
                ]
            );
            return $response->redirectTo(pluginApp(UrlHelper::class)->getRedirectUrl(RouteConfig::CHECKOUT));
        }
        if ($libResponse['status'] == 'canceled') {
            return $response->redirectTo(pluginApp(UrlHelper::class)->getRedirectUrl(RouteConfig::CHECKOUT));
        }
        $paymentInformation['transaction']['status'] = $libResponse['status'];
        unset($paymentInformation['transaction']['redirectUrl']);
        $this->sessionHelper->setValue('paymentInformation', $paymentInformation);
        $this->sessionHelper->setValue('paymentResource', $libResponse);

        return $response->redirectTo(pluginApp(UrlHelper::class)->getRedirectUrl(RouteConfig::PLACE_ORDER));
    }
}
