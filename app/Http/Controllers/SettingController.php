<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Http\Traits\HandlesImagesTrait;
use App\Http\Traits\ApiResponseTrait;

class SettingController extends Controller
{
    use HandlesImagesTrait, ApiResponseTrait;
    public function __construct(
        protected SettingRepositoryInterface $settingRepository
    ) {}

    public function show()
    {
        return response()->json($this->settingRepository->getConfiguration());
    }

    public function update(UpdateSettingRequest $request)
    {
        $data = $request->validated();

        $setting = $this->settingRepository->getConfiguration();

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->handleImageUpload(
                $request,
                'logo',
                'uploads/setting',
                $setting->logo
            );
        }
        $setting = $this->settingRepository->updateConfiguration($data);

        return $this->successResponse(
            $setting,
            'Settings updated successfully.'
        );
    }
}
