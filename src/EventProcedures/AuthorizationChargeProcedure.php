<?php

namespace HeidelpayMGW\EventProcedures;

use HeidelpayMGW\Helpers\OrderHelper;
use Plenty\Modules\Order\Models\Order;
use HeidelpayMGW\Helpers\PaymentHelper;
use HeidelpayMGW\Repositories\PaymentInformationRepository;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Throwable;

/**
 * Authorization charge event
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
 * @package  heidelpayMGW/eventprocedures
 *
 * @author Rimantas <development@heidelpay.com>
 */
class AuthorizationChargeProcedure
{
    /**
     * Handle authorization charge event
     *
     * @param EventProceduresTriggered $event
     * @param PaymentHelper $paymentHelper
     * @param PaymentInformationRepository $paymentInformationRepository
     * @param OrderHelper $orderHelper
     *
     * @return void
     * @throws Throwable
     */
    public function handle(
        EventProceduresTriggered $event,
        PaymentHelper $paymentHelper,
        PaymentInformationRepository $paymentInformationRepository,
        OrderHelper $orderHelper
    ): void {
        /** @var Order $order */
        $order = $event->getOrder();

        /** @var Order $originalOrder */
        $originalOrder = $orderHelper->getOriginalOrder($order);
        
        $paymentInformation = $paymentInformationRepository->getByOrderId($originalOrder->id);
        if (empty($paymentInformation)) {
            return;
        }
        
        $paymentService = $paymentHelper->getPluginPaymentService($originalOrder->id, (int)$originalOrder->methodOfPaymentId);
        $paymentService->chargeAuthorization($paymentInformation, $order);
    }
}
