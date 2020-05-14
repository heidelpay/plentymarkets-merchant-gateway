<?php
namespace HeidelpayMGW\Models;

use HeidelpayMGW\Configuration\PluginConfiguration;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * PluginSetting model
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
class PluginSetting extends Model
{
    /** @var int $id Model ID in the database. We don't need autoincrement so we set ID always to 1 */
    public $id = 1;

    /** @var string $publicKey heidelpay API key */
    public $publicKey = '';

    /** @var string $privateKey heidelpay API key */
    public $privateKey = '';

    /** @var string $apiMode heidelpay API mode */
    public $apiMode = '';

    /**
     * Database table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        /** @noinspection OffsetOperationsInspection */
        return PluginConfiguration::PLUGIN_NAME.'::'. explode('\\', __CLASS__)[2];
    }

    /**
     * Set parameters of the model
     *
     * @param array $data  Parameters to set
     *
     * @return PluginSetting
     */
    public function set($data): PluginSetting
    {
        // if parameter is null we set default value
        $this->publicKey = $data['publicKey'] ?? '';
        $this->privateKey = $data['privateKey'] ?? '';
        $this->apiMode = $data['apiMode'] ?? '';
        
        return $this;
    }
}
