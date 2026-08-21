<?php

namespace App\Repositories\Testimonial;

use App\Models\Testimonial;
use App\Repositories\AbstractCrudRepository;

class TestimonialRepository extends AbstractCrudRepository implements TestimonialRepositoryInterface
{
    protected string $modelClass = Testimonial::class;
}
