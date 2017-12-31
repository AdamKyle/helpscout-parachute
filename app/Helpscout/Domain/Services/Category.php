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
        return Categories::getAll($collection)->categories->items;
    }

    public function find(string $categoryName) {
        $categories = Categories::getAll()->categories->items;

        forEach($categories as $category) {
            if ($category->name === $categoryName) {
                return $category;
            }
        }

        return null;
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
