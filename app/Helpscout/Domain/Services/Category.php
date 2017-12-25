<?php

namespace App\Helpscout\Domain\Services;

use \Categories;
use \Category as CategoryFacade;
use \Pool;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Values\Collection;
use App\HelpScout\Category\Post\Body;
use HelpscoutApi\Response\Response;

class Category {

    public function fetchAll(Collection $collection) {
        $categories = Categories::getAll($collection)->categories->items;

        forEach($categories as $category) {
            $collection = new Collection($category->collectionId);
            $collection->setDbId(CollectionEntity::where('collection_id', $category->collectionId)->first()->id);
            (new CategoryEntity())->new(collect($category), $collection);
        }
    }

    public function createMultiple(array $fileContents, Collection $collection) {
        $categories = [];

        forEach($fileContents as $fileContent) {
            $categories[] = $fileContent->getCategory();
        }

        $categories = array_unique($categories);

        forEach($categories as $category) {
            $categorySearch = CategoryEntity::where('name', $category)->first();

            if (is_null($categorySearch)) {
                $categoryBody = new Body();

                $categoryBody->collectionId($collection);
                $categoryBody->name($category);

                $contents = (new Response(CategoryFacade::create($categoryBody)))->getContents()->category;

                (new CategoryEntity())->new(collect($contents), $collection);
            }
        }
    }
}
