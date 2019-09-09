<?php

namespace HeidelpayMGW\Helpers;

use HeidelpayMGW\Configuration\PluginConfiguration;
use HeidelpayMGW\Repositories\PluginSettingRepository;
use Plenty\Plugin\Application;

/**
* Returns Api keys depending if it's sandbox or production mode
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
* @link  https://docs.heidelpay.com/
*
* @author  Rimantas  <development@heidelpay.com>
*
* @package  heidelpayMGW/helpers
*/
class ApiKeysHelper
{
    /** @var \HeidelpayMGW\Models\PluginSetting $settings */
    private $settings;

    public function __construct(
        PluginSettingRepository $pluginSettingRepository,
        Application $app
    ) {
        $this->settings = $pluginSettingRepository->get($app->getPlentyId());
    }

    /**
     * Get public API key depending on API mode
     *
     * @return string  Public key
     */
    public function getPublicKey(): string
    {
        return $this->getKey($this->settings->publicKey);
    }

    /**
     * Get private API key depending on API mode
     *
     * @return string  Private key
     */
    public function getPrivateKey(): string
    {
        return $this->getKey($this->settings->privateKey);
    }

    /**
     * Change first private or public key letter depending on API mode
     *
     * @param string $key  Private or public key
     *
     * @return string  Private or public key
     */
    private function getKey(string $key): string
    {
        if ($this->settings->apiMode === PluginConfiguration::API_SANDBOX) {
            $key[0] = 's';
        }
        if ($this->settings->apiMode === PluginConfiguration::API_PRODUCTION) {
            $key[0] = 'p';
        }

        return $key;
    }
}
