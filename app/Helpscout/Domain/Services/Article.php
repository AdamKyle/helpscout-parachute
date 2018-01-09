<?php

namespace App\Helpscout\Domain\Services;

use \Articles;
use \Article as ArticleFacade;
use \Pool;
use cebe\markdown\Markdown;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Values\Category;
use App\Helpscout\Domain\Values\Article as ArticleValue;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Article\Create\Arguments;
use App\Helpscout\Domain\Services\Category as CategoryService;
use App\Helpscout\Files\Files;
Use App\Helpscout\Article\Post\Body;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Request\Requests;
use HelpscoutApi\Response\Response;
use App\Helpscout\Article\Dom\UpdateLinks;

class Article {

    public function fetchAll(Category $category) {
        return Articles::getAllFromCategory($category)->articles->items;
    }

    public function fetchAllFromCollection(Collection $collection) {
        return Article::getAllFromCollection($collection)->articles->items;
    }

    public function fetchSingle(ArticleValue $articleValue) {
        return Articles::getSingle($articleValue)->article;
    }

    public function create(Arguments $args, Collection $collection) {
        if (!file_exists($args->getPath())) {
            throw new \InvalidArgumentException($createArgs->getPath() . ' does not exist.');
        }

        $contents = $this->fetchAllFiles($args);

        $categoryService = new CategoryService();
        $categoryService->createMultiple($contents, $collection);

        $this->createMultiple($contents, $collection);
    }

    public function updateLinks(Arguments $args, Collection $collection) {
        if (!file_exists($args->getPath())) {
            throw new \InvalidArgumentException($createArgs->getPath() . ' does not exist.');
        }

        $contents = $this->fetchAllFiles($args);

        $categoryService = new CategoryService();
        $categoryService->createMultiple($contents, $collection);

        $this->updateMultiple($contents, $collection);
    }

    protected function updateMultiple(array $fileContents, Collection $collection) {
        $requests = new Requests();

        if (count($fileContents) === 0) {
            throw new \Exception('Cannot proceede, there were no files found. Check your path.');
        }

        forEach($fileContents as $fileContent) {
            $markdownToHtml = new Markdown();
            $body           = new Body();

            $body->collectionId($collection->getId());
            $body->name($fileContent->getFileName());
            $body->text($markdownToHtml->parse($fileContent->getContents()));
            $body->categories([
                CategoryEntity::where('name', $fileContent->getCategory())->first()->category_id
            ]);

            $updateArticleLinks = new UpdateLinks($body);
            $document = $updateArticleLinks->getDomForArticle();
            $links = $updateArticleLinks->getAllLinkTags($document);
            $updateArticleLinks->createArticleLinks($links);
        }
    }

    protected function createMultiple(array $fileContents, Collection $collection) {
        $requests = new Requests();

        if (count($fileContents) === 0) {
            throw new \Exception('Cannot proceede, there were no files found. Check your path.');
        }

        forEach($fileContents as $fileContent) {
            $markdownToHtml = new Markdown();
            $body           = new Body();

            $body->collectionId($collection->getId());
            $body->name($fileContent->getFileName());
            $body->text($markdownToHtml->parse($fileContent->getContents()));
            $body->categories([
                CategoryEntity::where('name', $fileContent->getCategory())->first()->category_id
            ]);

            $requests->pushRequest(ArticleFacade::createRequest($body));
            $requests->setConcurrency(20);
        }

        Pool::pool(
            $requests,
            function($reason, $index) use($requests) {
                // Lets see what was in that request that failed:
                dd($requests->getRequests()[$index]->getBody()->getContents());

                throw new \Exception($reason);
            },
            function($response) {
                $contents = (new Response($response))->getContents()->article;
                $article = (new ArticleEntity())->new(collect($contents));

                $categories = [];

                forEach($contents->categories as $category) {
                    $categories[] = CategoryEntity::where('category_id', $category)->first()->id;
                }

                $article->categories()->attach($categories);
            }
        );
    }

    protected function fetchAllFiles(Arguments $args) {
        $files = new Files($args->getCategoryIndex());

        $files->getAllContents(
            $args->getPath(),
            $args->getDirectoryNesting(),
            $args->shouldRemoveFirstElement()
        );

        return $files->getAllFiles();
    }
}
