<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HandlesImagesTrait;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Repositories\Brand\BrandRepositoryInterface;

class BrandController extends Controller
{
    use ApiResponseTrait, HandlesImagesTrait;

    protected const LOGO_PATH = 'uploads/brand';

    public function __construct(
        protected BrandRepositoryInterface $brandRepository
    ) {}

    public function index(Request $request)
    {
        try {
            $filters = $this->getIndexFilters($request);

            $brands = $this->brandRepository->index(
                $filters['search'],
                $filters['perPage'],
                $filters['sort'],
                $filters['column']
            );

            return $this->successResponse($brands);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'fetching brands');
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = $this->brandRepository->store(
                $this->prepareBrandData($request)
            );

            return $this->successResponse(
                $brand,
                'Brand created successfully.'
            );
        } catch (\Throwable $e) {
            return $this->handleException($e, 'creating brand');
        }
    }

    public function update(StoreBrandRequest $request, Brand $brand)
    {
        try {
            $data = $this->prepareBrandData($request, $brand);

            $this->brandRepository->update($brand, $data);

            return $this->successResponse(
                $brand->refresh(),
                'Brand updated successfully.'
            );
        } catch (\Throwable $e) {
            return $this->handleException($e, 'updating brand');
        }
    }

    public function show(Brand $brand)
    {
        return $this->successResponse(
            $this->brandRepository->find($brand)
        );
    }

    public function destroy(Brand $brand)
    {
        try {
            $this->deleteBrandLogo($brand);

            $this->brandRepository->delete($brand);

            return $this->successResponse(
                "",
                'Brand deleted successfully.'
            );
        } catch (\Throwable $e) {
            return $this->handleException($e, 'deleting brand');
        }
    }

    /* =======================
       Helper Methods
    ======================== */

    private function prepareBrandData(
        StoreBrandRequest $request,
        ?Brand $brand = null
    ): array {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            $this->deleteBrandLogo($brand);

            $data['logo'] = $this->handleImageUpload(
                $request,
                'logo',
                self::LOGO_PATH
            );
        }

        return $data;
    }

    private function deleteBrandLogo(?Brand $brand): void
    {
        if ($brand && $brand->logo) {
            $this->deleteImage('', $brand->logo);
        }
    }

    private function getIndexFilters(Request $request): array
    {
        return [
            'perPage' => (int) $request->input('pageSize', 10),
            'search'  => $request->input('searchValue', ''),
            'sort'    => in_array($request->input('sort'), ['asc', 'desc'])
                            ? $request->input('sort')
                            : 'desc',
            'column'  => $request->input('column', 'id'),
        ];
    }

    private function handleException(\Throwable $e, string $action)
    {
        Log::error("Error {$action}:", [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return $this->errorResponse(
            'An unexpected error occurred. Please try again later.'
        );
    }
}
