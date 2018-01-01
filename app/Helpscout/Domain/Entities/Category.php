<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Category as CategoryModel;
use App\Helpscout\Domain\Values\Collection;
use Illuminate\Support\Collection as IlluminateCollection;
use App\Helpscout\Domain\Values\Category as CategoryValue;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;

class Category extends CategoryModel {

    public function new(IlluminateCollection $category, Collection $collection) {
        return $this::create([
            'category_id'     => $category['id'],
            'category_number' => $category['number'],
            'name'            => $category['name'],
            'collection_id'   => $collection->getDbId()
        ]);
    }

    public function updateExisting($categoryEntity, IlluminateCollection $category, Collection $collection) {
        $categoryEntity->name          = $category['name'];
        $categoryEntity->collection_id = $collection->getDbId();

        $categoryEntity->save();
        return $categoryEntity;
    }

    public function findByNameAndCollectionId(string $name, string $collectionId) {
        $collectionEntity = CollectionEntity::where('collection_id', $collectionId)->first();
        $category = $collectionEntity->categories->where('name', $name)->first();

        if (is_null($category)) {
            return null;
        }

        $categoryValue = new CategoryValue($category->category_id);
        $categoryValue->setDbId($category->id);

        return $categoryValue;
    }
}
