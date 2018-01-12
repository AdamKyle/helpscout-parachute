<?php

namespace App\Helpscout\Article\Fetch\Collection;

use App\Helpscout\Article\Fetch\Collection\Article;
use App\Helpscout\Article\Fetch\Collection\Articles;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;

class FetchFactory {

    private $collection;

    public function __construct($collection) {
        $this->collection = $collection;
    }

    public function getFactoryInstance() {
        if ($this->collection instanceof EloquentCollection) {
            return new Articles($this->collection);
        }

        return new Article($this->collection);
    }
}
