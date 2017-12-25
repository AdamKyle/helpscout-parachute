<?php

namespace App\HelpScout\Category\Post;

use HelpscoutApi\Contracts\CategoryPostBody as CategoryPostBodyCollection;
use HelpscoutApi\Contracts\Collection;

class Body implements CategoryPostBodyCollection {

    private $collectionId;

    private $name;

    public function collectionId(Collection $collection) {
        $this->collectionId = $collection->getId();
    }

    public function name(String $name)  {
        $this->name = $name;
    }

    public function createPostBody() {
        $postBody = [
            'collectionId' => $this->collectionId,
            'name' => $this->name,
            'reload' => true,
        ];

        return json_encode($postBody);
    }
}
