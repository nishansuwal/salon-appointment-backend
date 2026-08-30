<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Repositories\Service\ServiceRepositoryInterface;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use App\Http\Traits\HandlesImagesTrait;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends AbstractCrudController
{
    use ResolvesIndexFiltersTrait, HandlesImagesTrait, ApiResponseTrait;
    protected string $resourceName = 'Service';

    protected const Service_IMAGE_PATH = 'uploads/serviceImage';

    public function __construct(
        ServiceRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }
    protected function resourceName(): string
    {
        return $this->resourceName;
    }

    public function index(Request $request)
    {
        return $this->indexResource(
            request: $request,
            repository: $this->repository,
            options: [
                'with' => [
                    'category:id,name,level',
                    'category.staff:id,user_id,position,is_active',
                    'images'
                ],
                'search' => [
                    'name',
                    'price',
                    'category.name',
                ],
            ],
            useIndexFilters: true
        );
    }

    public function show(int|string $id)
    {
        return $this->showResource(
            id: $id,
            repository: $this->repository,
            options: [
                'with' => [
                    'category:id,name,level',
                    'category.staff:id,user_id,position,is_active',
                    'category.staff.user:id,name,email',
                    'images'
                ],
            ]
        );
    }

    public function store(StoreServiceRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $data = $request->validated();
            unset(
                $data['images'],
                $data['image_sort_order'],
                $data['image_is_primary']
            );
            $service =  Service::create($data);

            $this->syncServiceImages($request, $service);

            return $this->successResponse(
                $service,
                'Service created successfully.'
            );
        });
    }

    public function update(
        UpdateServiceRequest $request,
        Service $service
    ) {
        $data = $request->validated();
        unset(
            $data['images'],
            $data['image_sort_order'],
            $data['image_is_primary']
        );
        $service->update($data);

        $this->syncServiceImages($request, $service);

        return $this->successResponse(
            $service,
            'Service updated successfully.'
        );
    }

    public function destroy(int|string $id)
    {
        return $this->destroyResource(
            id: $id,
            repository: $this->repository,
            name: $this->resourceName
        );
    }



    private function syncServiceImages($request, Service $service): void
    {
        $incomingImages = $request->input('images', []);
        $existingImageIds = collect($incomingImages)
            ->pluck('id')
            ->filter()
            ->toArray();

        $service->images()
            ->whereNotIn('id', $existingImageIds)
            ->get()
            ->each(function ($image) {
                $this->deleteImage(self::Service_IMAGE_PATH, $image->image_path);
                $image->delete();
            });

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {

                if (isset($incomingImages[$index]['id'])) {
                    continue;
                }

                $path = $this->uploadFile($file, self::Service_IMAGE_PATH);

                $service->images()->create([
                    'image_path' => $path,
                    'sort_order' => $request->image_sort_order[$index] ?? 0,
                    'image_is_primary' => $request->image_sort_order[$index] ?? 0,
                ]);
            }
        }
    }

    public function clientServices(Request $request)
    {
        $pageSize = (int) $request->input('pageSize', 12);
        $searchValue = $request->input('searchValue');
        $categoryId = $request->input('categoryId');
        $staffId = $request->input('staffId');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');

        $sortBy = $request->input('sortBy', 'latest');

        $with = [
            'category:id,parent_id,name,level',
            'category.parent:id,parent_id,name,level',
            'category.parent.staff:id,user_id,position,is_active',
            'category.staff:id,user_id,position,is_active',
            'images',
        ];

        $query = Service::query()
            ->with($with)
            ->where('status', 'active');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        if (!empty($categoryId)) {
            $query->whereHas('category', function ($q) use ($categoryId) {
                $q->where('parent_id', $categoryId);
            });
        }

        if (!empty($staffId)) {
            $query->where(function ($q) use ($staffId) {
                $q->whereHas('category.parent.staff', function ($q) use ($staffId) {
                    $q->where('staff_profiles.id', $staffId);
                })
                    ->orWhereHas('category.staff', function ($q) use ($staffId) {
                        $q->where('staff_profiles.id', $staffId);
                    });
            });
        }

        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }

        switch ($sortBy) {

            case 'latest':
                $query->latest();
                break;

            case 'oldest':
                $query->oldest();
                break;

            case 'price_high':
                $query->orderBy('price', 'desc');
                break;

            case 'price_low':
                $query->orderBy('price', 'asc');
                break;

            case 'discount_high':
                $query->orderBy('discount', 'desc');
                break;

            case 'discount_low':
                $query->orderBy('discount', 'asc');
                break;

            default:
                $query->latest();
                break;
        }

        $services = $query->paginate($pageSize);

        return $this->successResponse(
            $services,
            'Services retrieved successfully'
        );
    }

    public function getServiceBySlug(string $slug)
    {
        $service = Service::with([
            'category:id,parent_id,name,level',
            'category.parent:id,name,level',
            'images',
        ])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        $parentCategoryId = $service->category?->parent_id;

        $relatedServices = collect();

        if ($parentCategoryId) {
            $relatedServices = Service::with([
                'category:id,parent_id,name,level',
                'images',
            ])
                ->where('id', '!=', $service->id)
                ->where('status', 'active')
                ->whereHas('category', function ($query) use ($parentCategoryId) {
                    $query->where('parent_id', $parentCategoryId);
                })
                ->latest()
                ->get();
        }

        $reviews = Review::with(['user:id,name'])
            ->where('status', 'approved')
            ->whereHas('appointmentService.service', function ($query) use ($service) {
                $query->where('service_id', $service->id);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully.',
            'data' => [
                'service' => $service,
                'staffs' => $service->category?->parent?->staff ?? collect(),
                'related_services' => $relatedServices,
                'reviews' => $reviews,
            ],
        ]);
    }
}
