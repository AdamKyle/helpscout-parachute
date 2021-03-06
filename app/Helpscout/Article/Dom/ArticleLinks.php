<?php

namespace App\Helpscout\Article\Dom;

use App\Helpscout\Article\Post\Body;
use \DOMDocument;
use \DOMNodeList;
use \App\Helpscout\Domain\Values\ArticleLink;

class ArticleLinks {

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

    public function getArticleLinks(DOMNodeList $links) {
        forEach($links as $link) {
            $attribute              = $link->attributes[0]->nodeName;
            $this->articleLinks[]   = new ArticleLink($attribute, $link->getAttribute($attribute));
        }

        return $this->articleLinks;
    }

    public function replaceAttributes(array $linkValues, DOMDocument $document) {
        $links = $this->getAllLinkTags($document);

        forEach($links as $index => $link) {
            if ($link->getAttribute('href') === $linkValues[$index]->getAttributeValue()) {
                $link->setAttribute('href', $linkValues[$index]->getNewLinkValue());
            } else {
                $link->setAttribute('href', env('SITE_BASE') . $linkValues[$index]->getAttributeValue());
            }
        }

        $this->articlePostBody->text($document->saveHTML());
    }

    public function getUpdatedBody() {
        return $this->articlePostBody;
    }
}
