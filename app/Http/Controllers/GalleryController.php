<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gallery\StoreGalleryRequest;
use App\Http\Requests\Gallery\UpdateGalleryRequest;
use App\Repositories\Gallery\GalleryRepositoryInterface;
use App\Http\Traits\HandlesImagesTrait;

class GalleryController extends AbstractCrudController
{
    use HandlesImagesTrait;
    public function __construct(GalleryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function useIndexFilters(): bool
    {
        return false;
    }


    public function store(StoreGalleryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload(
                $request,
                'image',
                'gallery'
            );
        }
        return $this->storeResource($data, $this->repository, $this->resourceName());
    }
    public function update(UpdateGalleryRequest $request, int|string $id)
    {
        $data = $request->validated();

        $gallery = $this->repository->findOrFail($id);

        if ($request->hasFile('image')) {
            $data['image'] = $this->handleImageUpload(
                $request,
                'image',
                'gallery',
                $gallery->image
            );
        }
        return $this->updateResource($id, $data, $this->repository, $this->resourceName());
    }
    protected function resourceName(): string
    {
        return 'Gallery';
    }
}
