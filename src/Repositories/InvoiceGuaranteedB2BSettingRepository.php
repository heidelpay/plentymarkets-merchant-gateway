<?php

namespace HeidelpayMGW\Repositories;

use HeidelpayMGW\Models\InvoiceGuaranteedB2BSetting;
use Plenty\Plugin\Application;

/**
 * Invoice guaranteed B2B settings repository
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
 * @package  heidelpayMGW/repositories
 *
 * @author Rimantas <development@heidelpay.com>
 */
class InvoiceGuaranteedB2BSettingRepository extends BaseSettingRepository
{
    /**
     * InvoiceGuaranteedB2BSettingRepository constructor
     */
    public function __construct()
    {
        parent::__construct(pluginApp(InvoiceGuaranteedB2BSetting::class));
    }

    /**
     * Get Heidelpay return reason
     *
     * @param string $returnId  Plenty return reason ID
     *
     * @return string
     */
    public function getReturnCode(string $returnId): string
    {
        $model = $this->get(pluginApp(Application::class)->getPlentyId());

        if ($model->reasonCodeCancel === $returnId) {
            return 'CANCEL';
        }
        if ($model->reasonCodeReturn === $returnId) {
            return 'RETURN';
        }
        if ($model->reasonCodeCredit === $returnId) {
            return 'CREDIT';
        }

        return 'CANCEL';
    }
}
