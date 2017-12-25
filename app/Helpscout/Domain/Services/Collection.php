<?php

namespace App\Helpscout\Domain\Services;

use \Collections;
use \Collection as CollectionPost;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Values\Site;
use App\Helpscout\Collection\Post\Body;

class Collection {

    /**
     * Fetches all collections and stores them in the database.
     */
    public function fetchAll() {
        $collections = Collections::getAll()->collections->items;

        forEach($collections as $collection) {
            (new CollectionEntity())->new(collect($collection));
        }
    }

    public function create(Site $site, string $visibility, string $collectionName) {
        $body = new Body();

        $body->siteId($site);
        $body->name($collectionName);
        $body->visibility($visibility);

        return CollectionPost::create($body);
    }
}
