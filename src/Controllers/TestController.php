<?php

namespace HeidelpayMGW\Controllers;

use Plenty\Plugin\Controller;
use HeidelpayMGW\Helpers\Loggable;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use HeidelpayMGW\Configuration\PluginConfiguration;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class for Database manipulation
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
 * @link https://docs.heidelpay.com/controllers
 *
 * @package  heidelpayMGW/
 *
 * @author Rimantas <development@heidelpay.com>
 */
class TestController extends Controller
{
    use Loggable;

    public function __construct()
    {
    }

    /**
     * Reset model
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return BaseResponse
     */
    public function reset(Request $request, Response $response, Migrate $migrate): BaseResponse
    {
        $model = $request->get('model');
        $migrate->deleteTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\'.$model);
        $migrate->createTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\'.$model);

        return $response->json('Ok');
    }

    /**
     * Update model
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return BaseResponse
     */
    public function update(Request $request, Response $response, Migrate $migrate): BaseResponse
    {
        $model = $request->get('model');
        if ($model === 'all') {
            $migrate->updateTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\PluginSetting');
            $migrate->updateTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\InvoiceSetting');
            $migrate->updateTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\InvoiceGuaranteedSetting');
            $migrate->updateTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\InvoiceGuaranteedB2BSetting');
        } else {
            $migrate->updateTable(PluginConfiguration::PLUGIN_NAME.'\\Models\\'.$model);
        }

        return $response->json('Ok');
    }

    /**
     * Show model
     *
     * @param Request $request
     * @param Response $response
     *
     * @return BaseResponse
     */
    public function show(Request $request, Response $response): BaseResponse
    {
        $model = $request->get('model');
        
        return $response->json(pluginApp(DataBase::class)->query(PluginConfiguration::PLUGIN_NAME.'\\Models\\'.$model)->get());
    }
}
