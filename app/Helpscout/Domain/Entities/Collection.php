<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Collection as CollectionModel;
use App\Helpscout\Articles\Create\Arguments;
use Illuminate\Support\Collection as IlluminateCollection;
use App\Helpscout\Domain\Values\Collection as CollectionValue;

class Collection extends CollectionModel {

    public function exists(string $name) {
        return $this::where('name', $name)->first();
    }

    public function new(IlluminateCollection $collection) {
        return $this::create([
            'collection_id'     => $collection['id'],
            'collection_number' => $collection['number'],
            'name'              => $collection['name'],
            'visibility'        => $collection['visibility'],
            'site_id'           => $collection['siteId'],
        ]);
    }

    public function findByName(string $name) {
        $collectionFound = $this::where('name', $name)->first();

        if (is_null($collectionFound)) {
            return null;
        }

        $collectionValue = new CollectionValue($collectionFound->collection_id);
        $collectionValue->setDbId($collectionFound->id);

        return $collectionValue;
    }
}
