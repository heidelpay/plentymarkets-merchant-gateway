<?php

namespace HeidelpayMGW\Repositories;

use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

/**
 * Base settings repository
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
class BaseSettingRepository
{
    /** @var DataBase $database */
    private $database;

    /** @var mixed $emptyModel  Holds model that we need to save or get from database */
    private $emptyModel;

    /** @var string $modelClass  Holds models class */
    private $modelClass;

    /**
     * BaseSettingRepository constructor
     *
     * @param mixed $emptyModel
     */
    public function __construct($emptyModel)
    {
        $this->emptyModel = $emptyModel;
        $this->modelClass = get_class($emptyModel);
        $this->database = pluginApp(DataBase::class);
    }

    /**
     * Returns setting model
     *
     * @return mixed
     */
    public function get($plentyId)
    {
        return $this->database->query($this->modelClass)
            ->where('plentyId', '=', $plentyId)
            ->get()[0] ?? $this->emptyModel;
    }

    /**
     * Saves settings from UI
     *
     * @param array $data
     */
    public function save($plentyId, array $data)
    {
        $data['plentyId'] = $plentyId;
        $model = $this->get($plentyId)->set($data);

        $this->database->save($model);
        
        return $model;
    }
}
