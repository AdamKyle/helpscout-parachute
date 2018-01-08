<?php

namespace App\Helpscout\Domain\Services;

use \Collections;
use \Collection as CollectionPost;
use App\Helpscout\Domain\Values\Site;
use App\Helpscout\Domain\Values\Collection as CollectionValue;
use App\Helpscout\Collection\Post\Body;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Article\Create\Arguments;

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

    public function handleCollection(CollectionEntity $collection = null, Arguments $args, Site $site) {

        $collectionEntity = new CollectionEntity();

        // handle collection
        if (is_null($collection)) {
            $response        = $this->create($site, 'private', $args->getCollectionName());
            $contents        = collect((new Response($response))->getContents()->collection);
            $collection      = $collectionEntity->new($contents);
            $collectionValue = new CollectionValue($collection->collection_id, $collection->name);
            $collectionValue->setDbId($collection->id);
        } else {
            $collectionValue = new CollectionValue($collection->collection_id, $collection->name);
            $collectionValue->setDbId($collection->id);
        }

        return $collectionValue;
    }

    public function create(Site $site, string $visibility, string $collectionName) {
        $body = new Body();

        $body->siteId($site);
        $body->name($collectionName);
        $body->visibility($visibility);

        return CollectionPost::create($body);
    }
}
