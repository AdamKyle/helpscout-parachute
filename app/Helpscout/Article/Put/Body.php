<?php

namespace App\Helpscout\Article\Put;

use HelpscoutApi\Contracts\ArticlePutBody;
use HelpscoutApi\Contracts\ArticlePostBody;

class Body implements ArticlePutBody {

    private $articleId;

    private $articlePostBody;

    public function id(string $articleId) {
        $this->articleId = $articleId;
    }

    public function getId(): string {
        return $this->articleId;
    }

    public function articlePostBody(ArticlePostBody $articlePostBody) {
        $this->articlePostBody = $articlePostBody;
    }

    public function createPutBody() {
        $postBody = [
            'id' => $this->articleId,
            'collectionId' => $this->articlePostBody->getCollectionId(),
            'name' => $this->articlePostBody->getName(),
            'slug' => str_slug($this->articlePostBody->getName(), '-'),
            'text' => $this->articlePostBody->getText(),
            'categories' => $this->articlePostBody->getCategories(),
            'reload' => true,
        ];

        return json_encode($postBody);
    }
}
