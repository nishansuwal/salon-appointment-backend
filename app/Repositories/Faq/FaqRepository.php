<?php
namespace App\Repositories\Faq;
use App\Models\Faq;
use App\Repositories\AbstractCrudRepository;
class FaqRepository extends AbstractCrudRepository implements FaqRepositoryInterface { protected string $modelClass = Faq::class; }
