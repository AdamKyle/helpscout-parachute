<?php

namespace App\Helpscout\Domain\Entities;

use App\Models\Collection as CollectionModel;
use App\Helpscout\Articles\Create\Arguments;
use Illuminate\Support\Collection as IlluminateCollection;

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
}
