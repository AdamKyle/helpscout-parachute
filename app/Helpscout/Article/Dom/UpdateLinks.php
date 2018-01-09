<?php

namespace App\Helpscout\Article\Dom;

use App\Helpscout\Article\Post\Body;
use \DOMDocument;
use \DOMNodeList;
use \App\Helpscout\Domain\Values\ArticleLink;

class UpdateLinks {

    private $articlePostBody;

    private $articleLinks = [];

    public function __construct(Body $articlePostBody) {
        $this->articlePostBody = $articlePostBody;
    }

    public function getDomForArticle() {
        $document = new DOMDocument();
        $document->loadHTML($this->articlePostBody->getText());

        return $document;
    }

    public function getAllLinkTags(DOMDocument $document) {
        return $document->getElementsByTagName('a');
    }

    public function createArticleLinks(DOMNodeList $links) {
        forEach($links as $link) {
            $attribute = $link->attributes[0]->nodeName;
            $articleLinks[]   = new ArticleLink($attribute, $link->getAttribute($attribute));
        }
    }
}
