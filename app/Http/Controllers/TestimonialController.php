<?php

namespace App\Http\Controllers;

use App\Http\Requests\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Testimonial\UpdateTestimonialRequest;
use App\Repositories\Testimonial\TestimonialRepositoryInterface;
use App\Http\Traits\HandlesImagesTrait;

class TestimonialController extends AbstractCrudController
{
    use HandlesImagesTrait;
    public function __construct(TestimonialRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function useIndexFilters(): bool
    {
        return false;
    }


    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload(
                $request,
                'image',
                'uploads/testimonial'
            );
        }
        return $this->storeResource($data, $this->repository, $this->resourceName());
    }

    public function update(UpdateTestimonialRequest $request, int|string $id)
    {
        $data = $request->validated();

        $testimonial = $this->repository->findOrFail($id);

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload(
                $request,
                'image',
                'uploads/testimonial',
                $testimonial->image
            );
        }
        return $this->updateResource($id, $data, $this->repository, $this->resourceName());
    }
    protected function resourceName(): string
    {
        return 'testimonial';
    }
}
