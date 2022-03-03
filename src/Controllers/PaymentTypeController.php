<?php

namespace HeidelpayMGW\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use HeidelpayMGW\Helpers\Loggable;
use HeidelpayMGW\Helpers\SessionHelper;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Gets payment type from checkout and saves to session
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
 * @package  heidelpayMGW/controllers
 *
 * @author Rimantas <development@heidelpay.com>
 */
class PaymentTypeController extends Controller
{
    use Loggable;

    /**
     * Set session value to payment from Frontend
     *
     * @param Response $response
     * @param Request $request
     * @param SessionHelper $sessionHelper
     *
     * @return BaseResponse
     */
    public function heidelpayMGWPaymentType(Response $response, Request $request, SessionHelper $sessionHelper): BaseResponse
    {
        /** @var array $frontendData */
        $frontendData = $request->except('plentyMarkets');

        /** @var array $data */
        $data = $frontendData['data'];
        /** @var string $heidelpayBirthDate */
        $heidelpayBirthDate = $frontendData['heidelpayBirthDate'];
        /** @var array $heidelpayB2bCustomer */
        $heidelpayB2bCustomer = $frontendData['heidelpayB2bCustomer'];

        $sessionHelper->setValue('paymentResource', $data);
        $sessionHelper->setValue('heidelpayBirthDate', $heidelpayBirthDate);
        $sessionHelper->setValue('heidelpayB2bCustomer', $heidelpayB2bCustomer);
        $sessionHelper->setValue('frontendLocale', $frontendData['lang']);

        return $response->json([
            'success' => true
        ]);
    }
}
