<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\AbstractCrudRepository;
use App\Repositories\Setting\SettingRepositoryInterface;

class SettingRepository extends AbstractCrudRepository implements SettingRepositoryInterface
{
    protected string $modelClass = Setting::class;

    public function getConfiguration(): Setting
    {
        return Setting::query()->first() ?? Setting::create(['name' => 'Salon']);
    }

    public function updateConfiguration(array $data): Setting
    {
        $setting = $this->getConfiguration();
        $setting->update($data);

        return $setting->refresh();
    }

}