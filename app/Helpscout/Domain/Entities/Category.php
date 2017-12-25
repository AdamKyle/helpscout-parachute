<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Category as CategoryModel;
use App\Helpscout\Domain\Values\Collection;
use Illuminate\Support\Collection as IlluminateCollection;

class Category extends CategoryModel {

    public function new(IlluminateCollection $category, Collection $collection) {
        return $this::create([
            'category_id'     => $category['id'],
            'category_number' => $category['number'],
            'name'            => $category['name'],
            'collection_id'   => $collection->getDbId()
        ]);
    }
}
