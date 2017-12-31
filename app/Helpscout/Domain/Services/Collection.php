<?php

namespace App\Helpscout\Domain\Services;

use \Collections;
use \Collection as CollectionPost;
use App\Helpscout\Domain\Values\Site;
use App\Helpscout\Collection\Post\Body;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;

class Collection {

    /**
     * Fetches all collections and stores them in the database.
     */
    public function fetchAll() {
        return Collections::getAll()->collections->items;
    }

    public function find(string $collectionName) {
        $collections = Collections::getAll()->collections->items;

        forEach($collections as $collection) {
            if ($collection->name === $collectionName) {
                return $collection;
            }
        }

        return null;
    }

    public function findInDatabase(string $collectionName) {
        $collection = CollectionEntity::where('name', $collectionName)->first();
        if (is_null($collection)) {
            return null;
        }

        return $collection;
    }

    public function create(Site $site, string $visibility, string $collectionName) {
        $body = new Body();

        $body->siteId($site);
        $body->name($collectionName);
        $body->visibility($visibility);

        return CollectionPost::create($body);
    }
}
