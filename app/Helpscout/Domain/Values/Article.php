<?php

namespace App\Helpscout\Domain\Values;

use HelpscoutApi\Contracts\Article as ArticleContract;

class Article implements ArticleContract {

    private $articleId;

    public function __construct(string $articleId) {
        $this->articleId = $articleId;
    }

    public function getId(): string {
        return $this->articleId;
    }
}
