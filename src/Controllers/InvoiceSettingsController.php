<?php

namespace HeidelpayMGW\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use HeidelpayMGW\Helpers\Loggable;
use HeidelpayMGW\Repositories\InvoiceSettingRepository;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Invoice settings controller for UI settings
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
 * @package  heidelpayMGW/controllers
 *
 * @author Rimantas <development@heidelpay.com>
 */
class InvoiceSettingsController extends Controller
{
    use Loggable;

    /** @var InvoiceSettingRepository $settingRepository */
    private $settingRepository;

    /** @var Response $response */
    private $response;

    /** @var Request $request */
    private $request;

    /**
     * InvoiceSettingsController constructor
     *
     * @param InvoiceSettingRepository $settingRepository  Repository class from which we get settings
     * @param Response $response
     * @param Request $request
     */
    public function __construct(InvoiceSettingRepository $settingRepository, Response $response, Request $request)
    {
        $this->settingRepository = $settingRepository;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Get settings from DB
     *
     * @return BaseResponse
     */
    public function getSettings($plentyId): BaseResponse
    {
        try {
            return $this->response->json([
                'success' => true,
                'settings' => $this->settingRepository->get($plentyId)
            ]);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->exception(
                'translation.exception',
                [
                    'error' => $e->getMessage()
                ]
            );

            return $this->response->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save settings to DB
     *
     * @return BaseResponse
     */
    public function saveSettings($plentyId): BaseResponse
    {
        try {
            return $this->response->json([
                'success' => true,
                'settings' => $this->settingRepository->save($plentyId, $this->request->except('plentymarkets'))
            ]);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->exception(
                'translation.exception',
                [
                    'error' => $e->getMessage()
                ]
            );

            return $this->response->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
