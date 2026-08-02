<?php

namespace App\Repositories\Gallery;

use App\Models\Gallery;
use App\Repositories\AbstractCrudRepository;

class GalleryRepository extends AbstractCrudRepository implements GalleryRepositoryInterface
{
    protected string $modelClass = Gallery::class;
}
