<?php

namespace HeidelpayMGW\Helpers;

use IO\Services\UrlBuilder\UrlQuery;

/**
* Returns the API keys
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
* @package  heidelpayMGW/helpers
*/
class UrlHelper
{
    /** @var SessionHelper $sessionHelper */
    private $sessionHelper;

    public function __construct(SessionHelper $sessionHelper)
    {
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * Get public API key from settings
     *
     * @return string  Public key
     */
    public function getRedirectUrl(string $url): string
    {
        $language = $this->sessionHelper->getValue('frontendLocale');

        $query = pluginApp(UrlQuery::class, ['path' => $url, 'lang' => $language]);

        $includeLanguage = false;
        if ($language != 'de') {
            $includeLanguage = true;
        }

        return $query->toRelativeUrl($includeLanguage);
    }
}
