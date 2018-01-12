<?php

namespace App\Helpscout\Article\Fetch\Collection;

use App\Helpscout\Domain\Entities\Collection;
use App\Helpscout\Domain\Values\Collection as CollectionValue;
use App\Helpscout\Article\Fetch\Collection\Base;

class Article extends Base {

    private $collection;

    public function __construct(Collection $collection) {
        $this->collection = $collection;
    }

    public function createFromCollection() {
        $collectionValue = new CollectionValue($this->collection->collection_id);
        $this->handleCollections($collectionValue);
    }
}
