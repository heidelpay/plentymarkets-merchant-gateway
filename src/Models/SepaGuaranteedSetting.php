<?php

namespace HeidelpayMGW\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use HeidelpayMGW\Configuration\PluginConfiguration;

/**
 * SEPA guaranteed settings model
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
 * @package  heidelpayMGW/models
 *
 * @author Rimantas <development@heidelpay.com>
 */
class SepaGuaranteedSetting extends Model
{
    /** @var int $id Model ID in the database. We don't need autoincrement so we set ID always to 1 */
    public $id = 1;

    /** @var bool $isActive */
    public $isActive = false;

    /** @var string $displayName  Payment method display name */
    public $displayName = 'SEPA direct debit guaranteed';

    /** @var string $basketMinTotal  Minimum basket amount for payment method */
    public $basketMinTotal = '';

    /** @var string $basketMaxTotal  Maximum basket amount for payment method */
    public $basketMaxTotal = '';

    /** @var string $iconURL  Path to icon of payment method */
    public $iconURL = '';

    /** @var string $companyName  Company name to display in checkout in SEPA popup */
    public $companyName = '';

    /**
     * Database table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return PluginConfiguration::PLUGIN_NAME.'::'.explode('\\', __CLASS__)[2];
    }

    /**
     * Set parameters of the model
     *
     * @param array $data
     *
     * @return SepaGuaranteedSetting
     */
    public function set($data): SepaGuaranteedSetting
    {
        // if parameter is null we set default value
        $this->isActive = $data['isActive'] ?? false;
        $this->displayName = $data['displayName'] ?? '';
        $this->basketMinTotal = $data['basketMinTotal'] ?? '';
        $this->basketMaxTotal = $data['basketMaxTotal'] ?? '';
        $this->iconURL = $data['iconURL'] ?? '';
        $this->companyName = $data['companyName'] ?? '';
        
        return $this;
    }
}
