<?php

namespace App\Helpscout\Article\Post;

use HelpscoutApi\Contracts\ArticlePostBody as ArticlePostBodyContract;

class Body implements ArticlePostBodyContract {

    private $collectionId;

    private $name;

    private $text;

    private $categories = [];

    public function collectionID(String $collectionId) {
        $this->collectionId = $collectionId;
    }

    public function name(String $name)  {
        $this->name = $name;
    }

    public function text(String $text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }

    public function categories(array $categories) {
        $this->categories = $categories;
    }

    public function createPostBody() {
        $postBody = [
            'collectionId' => $this->collectionId,
            'name' => $this->name,
            'slug' => str_slug($this->name, '-'),
            'text' => $this->text,
            'categories' => $this->categories,
            'reload' => true,
        ];

        return json_encode($postBody);
    }
}
