<?php

namespace App\Helpscout\Domain\Services;

Use App\Helpscout\Article\Post\Body;
use \App\Helpscout\Domain\Values\ArticleLink;
use App\Helpscout\Article\Dom\ArticleLinks as ArticleLinksDom;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;

class ArticleLinks {

    private $articleLinks;

    public function __construct(Body $body) {
        $this->articleLinks = new ArticleLinksDom($body);
    }

    public function getLinks() {
        $document     = $this->articleLinks->getDomForArticle();
        $linkTags     = $this->articleLinks->getAllLinkTags($document);

        return $this->articleLinks->getArticleLinks($linkTags);
    }

    public function replaceLinks(array $links) {
        $linkValues = [];

        forEach($links as $link) {
            $linkValues[] = $this->updateLink($link);
        }
        
        return $linkValues;
    }

    public function getArticleLinks() {
        return $this->articleLinks;
    }

    /**
     * Replace the links.
     *
     * First we explode the attribute value to get the pieces.
     * Second we then check if the first value in the array is empty, this would be where the /
     * would live
     * Next we go and find the article name at the end of the path: /some/path/article-name
     * so we are looking for article-name.
     *
     * If we find it, we use the public_url to be the new url instead of /some/path/article-name.
     * If we don't find it, we assume it lives on the SITE_BASE, which is set in the env.
     *
     * If the url starts with http:// or any other variation, we use the artibute value as the link.
     */
    protected function updateLink(ArticleLink $link) {
        $linkPieces = explode('/', $link->getAttributeValue());

        // This means the link's href starts with /path/to/path and not http://
        if (empty($linkPieces[0])) {
            $foundArticle = ArticleEntity::where('name', end($linkPieces))->first();

            if (is_null($foundArticle)) {
                $link->setNewLinkValue(env('SITE_BASE') . $link->getAttributeValue());
            } else {
                $link->setNewLinkValue($foundArticle->public_url);
            }
        } else {
            // If it does contain an http:// then just ignore it.
            $link->setNewLinkValue($link->getAttributeValue());
        }

        return $link;
    }
}
