<?php

namespace App\Helpscout\Collection\Post;

use HelpscoutApi\Contracts\CollectionPostBody as CollectionPostBodyContract;
use HelpscoutApi\Contracts\Site;

class Body implements CollectionPostBodyContract {

    private $siteId;

    private $name;

    private $visibility;

    public function siteId(Site $site) {
        $this->siteId = $site->getId();
    }

    public function name(String $name)  {
        $this->name = $name;
    }

    public function visibility(string $visibility) {
        $this->visibility = $visibility;
    }


    public function createPostBody() {
        $postBody = [
            'siteId' => $this->siteId,
            'name' => $this->name,
            'visibility' => $this->visibility,
            'reload' => true,
        ];

        return json_encode($postBody);
    }
}
