<?php

namespace App\Helpscout\Domain\Services;

use \Articles;
use \Article as ArticleFacade;
use \Pool;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Values\Category;
use App\Helpscout\Domain\Values\Article as ArticleValue;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Article\Create\Arguments;
use App\HelpScout\Domain\Services\Category as CategoryService;
use App\Helpscout\Files\Files;
Use App\Helpscout\Article\Post\Body;
use App\HelpScout\Domain\Entities\Category as CategoryEntity;
use App\HelpScout\Request\Requests;
use HelpscoutApi\Response\Response;

class Article {

    public function fetchAll(Category $category) {
        $articles = Articles::getAllFromCategory($category)->articles->items;

        if (count($articles) > 0) {
            forEach($articles as $article) {
                $articleValue = new ArticleValue($article->id);
                $article      = Articles::getSingle($articleValue)->article;
                $articleModel = (new ArticleEntity())->new(collect($article));

                $articleModel->categories()->attach($category->getDbId());
            }
        }
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

    protected function createMultiple(array $fileContents, Collection $collection) {
        $requests = new Requests();

        forEach($fileContents as $fileContent) {
            $body = new Body();
            $body->collectionId($collection->getId());
            $body->name($fileContent->getFileName());
            $body->text($fileContent->getContents());
            $body->categories([
                CategoryEntity::where('name', $fileContent->getCategory())->first()->category_id
            ]);

            $requests->pushRequest(ArticleFacade::createRequest($body));
            $requests->setConcurrency(20);
        }

        Pool::pool(
            $requests,
            function($reason, $index) {
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
        $files = new Files();
        $files->getAllContents($args->getPath(), $args->getDirectoryNesting(), $args->shouldRemoveFirstElement());

        return $files->getAllFiles();
    }
}
