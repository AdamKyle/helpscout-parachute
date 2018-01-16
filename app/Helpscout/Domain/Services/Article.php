<?php

namespace App\Helpscout\Domain\Services;

use \Articles;
use \ArticlePut;
use \ArticleFacade;
use App\Helpscout\Request\Requests;
use App\Helpscout\Article\Post\Body;
use cebe\markdown\Markdown;
use App\Helpscout\Domain\Services\Pool;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Article\Create\Arguments;
use App\Helpscout\Domain\Values\Article as ArticleValue;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Services\File as FileService;
use App\Helpscout\Domain\Services\Category as CategoryService;
use App\Helpscout\Article\Put\Body as ArticlePutBody;
use App\Helpscout\Domain\Services\ArticleLinks as ArticleLinksService;
use HelpscoutApi\Params\Article as ArticleParams;

class Article {

    public function fetchAll(Category $category, ArticleParams $articleParams = null) {
        return Articles::getAllFromCategory($category, $articleParams)->articles->items;
    }

    public function fetchAllFromCollection(Collection $collection, ArticleParams $articleParams = null) {
        return Articles::getAllFromCollection($collection, $articleParams);
    }

    public function createCollectionRequest(Collection $collection, ArticleParams $articleParams = null) {
        return Articles::collectionGetRequest($collection, $articleParams);
    }

    public function fetchSingle(ArticleValue $articleValue) {
        return Articles::getSingle($articleValue)->article;
    }

    public function createOrUpdate(\stdClass $article) {
        $articleEntity = ArticleEntity::where('name', $article->name)->first();
        
        if (is_null($articleEntity)) {
            $this->create($article);
        } else {
            $this->update($articleEntity, $article);
        }

    }

    public function createFromFiles(Arguments $args, Collection $collection) {
        if (!file_exists($args->getPath())) {
            throw new \InvalidArgumentException($args->getPath() . ' does not exist.');
        }

        $contents = (new FileService())->fetchAllFiles($args);

        $categoryService = new CategoryService();
        $categoryService->createMultiple($contents, $collection);

        $this->createMultiple($contents, $collection);
    }

    protected function update(ArticleEntity $articleEntity, \stdClass $article) {
        $articleEntity = (new ArticleEntity())->updateExisting($articleEntity, collect($article));
        $categories    = [];

        forEach($article->categories as $category) {
            $categories[] = CategoryEntity::where('category_id', $category)->first()->id;
        }

        $articleEntity->categories()->detach();
        $articleEntity->categories()->attach($categories);
    }

    protected function create(\stdClass $article) {
        $articleEntity = (new ArticleEntity())->new(collect($article));

        $categories = [];

        forEach($article->categories as $category) {
            $categories[] = CategoryEntity::where('category_id', $category)->first()->id;
        }

        $articleEntity->categories()->attach($categories);
    }

    public function updateLinks(Arguments $args, Collection $collection) {
        if (!file_exists($args->getPath())) {
            throw new \InvalidArgumentException($args->getPath() . ' does not exist.');
        }

        $contents = (new FileService())->fetchAllFiles($args);

        $categoryService = new CategoryService();
        $categoryService->createMultiple($contents, $collection);

        $this->updateMultipleArticleLinks($contents, $collection);
    }

    protected function createMultiple(array $fileContents, Collection $collection) {
        $requests = new Requests();

        if (count($fileContents) === 0) {
            throw new \Exception('Cannot proceede, there were no files found. Check your path.');
        }

        forEach($fileContents as $fileContent) {
            $body = $this->setBody(new Body(), new Markdown(), $collection, $fileContents);
            $requests->pushRequest(ArticleFacade::createRequest($body));
        }

        $requests->setConcurrency(20);
        $pool = new Pool($requests, $this);
        $pool->pool();
    }

    protected function updateMultipleArticleLinks(array $fileContents, Collection $collection) {
        $requests = new Requests();

        if (count($fileContents) === 0) {
            throw new \Exception('Cannot proceede, there were no files found. Check your path.');
        }


        forEach($fileContents as $fileContent) {
            $body           = $this->setBody(new Body(), new Markdown(), $collection, $fileContent);
            $articlePutBody = new ArticlePutBody();
            $articleLinks   = new ArticleLinksService($body);
            $links          = $articleLinks->getLinks();
            $updatedLinks   = $articleLinks->replaceLinks($links);
            $document       = $articleLinks->getArticleLinks()->getDomForArticle();

            $articleLinks->getArticleLinks()->replaceAttributes($updatedLinks, $document);

            $body      = $articleLinks->getArticleLinks()->getUpdatedBody();
            $articleId = ArticleEntity::where('name', $body->getName())->first()->article_id;

            $articlePutBody->id($articleId);
            $articlePutBody->articlePostBody($body);
            $requests->pushRequest(ArticlePut::updateRequest($articlePutBody));
        }

        $requests->setConcurrency(20);
        $pool = new Pool($requests, $this);
        $pool->pool();
    }

    public function setBody(Body $body, Markdown $markdown, Collection $collection, FileInformation $fileContent) {
        $body->collectionId($collection->getId());
        $body->name($fileContent->getFileName());
        $body->text($markdown->parse($fileContent->getContents()));
        $body->categories([
            CategoryEntity::where('name', $fileContent->getCategory())->first()->category_id
        ]);

        return $body;
    }
}
