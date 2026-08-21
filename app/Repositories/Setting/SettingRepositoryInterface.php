<?php

namespace App\Repositories\Setting;
use App\Models\Setting;

interface SettingRepositoryInterface
{
    public function getConfiguration();

    public function updateConfiguration(array $data): Setting;

}
