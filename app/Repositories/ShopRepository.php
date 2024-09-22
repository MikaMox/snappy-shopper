<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Interfaces\ShopRepositoryInterface;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Collection;

class ShopRepository implements ShopRepositoryInterface
{
    protected $model;

    public function __construct(Shop $shop)
    {
        $this->model = $shop;
    }

    public function getAllShops(): Collection
    {
        return $this->model->all();
    }
}


