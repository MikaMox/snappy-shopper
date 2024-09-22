<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Interfaces\PostcodeRepositoryInterface;
use App\Models\Postcode;

class PostcodeRepository implements PostcodeRepositoryInterface
{
    protected $model;

    public function __construct(Postcode $postcode)
    {
        $this->model = $postcode;
    }

    public function findPostcodeByCode($postcode): ?Postcode
    {
        return $this->model->where('postcode', $postcode)->first();
    }
}


