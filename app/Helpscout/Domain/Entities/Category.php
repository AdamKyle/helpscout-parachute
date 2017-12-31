<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Category as CategoryModel;
use App\Helpscout\Domain\Values\Collection;
use Illuminate\Support\Collection as IlluminateCollection;
use App\Helpscout\Domain\Values\Category as CategoryValue;

class Category extends CategoryModel {

    public function new(IlluminateCollection $category, Collection $collection) {
        return $this::create([
            'category_id'     => $category['id'],
            'category_number' => $category['number'],
            'name'            => $category['name'],
            'collection_id'   => $collection->getDbId()
        ]);
    }

    public function findByName(string $name) {
        $categoryCollecton = CategoryEntity::where('name', $name)->first();

        if (is_null($categoryCollecton)) {
            return null;
        }

        $categoryValue = new CategoryValue($categoryCollecton->category_id);
        $categoryValue->setDbId($categoryCollecton->id);

        return $categoryValue;
    }
}
