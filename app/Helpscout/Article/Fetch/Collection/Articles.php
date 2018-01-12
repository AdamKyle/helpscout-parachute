<?php

namespace App\Helpscout\Articles\Fetch\Collection;

use Illuminate\Database\Eloquent\Collection;
use App\Helpscout\Domain\Values\Collection as CollectionValue;
use App\Helpscout\Article\Fetch\Collection\Base;

class Articles extends Base {

    private $collections;

    public function __construct(Collection $collections) {
        $this->collections = $collections;
    }

    public function createFromCollections() {
        forEach($this->collections as $collection) {
            $collectionValue = new CollectionValue($collection->collection_id);
            $this->handleCollections($collectionValue);
        }
    }
}
