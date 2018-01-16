<?php

namespace App\Helpscout\Article\Fetch\Collection;

use App\Helpscout\Domain\Services\Article as ArticleService;
use App\Helpscout\Domain\Values\Collection as CollectionValue;
use HelpscoutApi\Params\Article as ArticleParams;
use App\Helpscout\Domain\Values\Article as ArticleValue;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Request\Requests;
use HelpscoutApi\Response\Response;
use \Pool;

class Base {

    protected function handleCollections(CollectionValue $collectionValue) {
        $articleService  = new ArticleService();

        $articles = $articleService->fetchAllFromCollection($collectionValue);

        if ($articles->articles->pages > 1) {
            $this->manageMultiplePages($articles->articles->pages, $articleService, $collectionValue);
        } else {
            $this->createOrUpdateArticle($articles->articles->items, $articleService);
        }
    }

    protected function manageMultiplePages(
        int $totalPages,
        ArticleService $articleService,
        CollectionValue $collectionValue)
    {
        $requests = new Requests();

        for($i = 1; $i <= $totalPages; $i++) {
            $articleParams = new ArticleParams();
            $articleParams->page((string) $i);

            $requests->pushRequest($articleService->createCollectionRequest($collectionValue, $articleParams));
            $requests->setConcurrency(20);
        }

        $this->handleRequests($requests, $articleService);
    }

    protected function handleRequests(Requests $requests, ArticleService $articleService) {
        Pool::pool(
            $requests,
            function($reason, $index) use($requests) {
                // Lets see what was in that request that failed:
                var_dump($requests->getRequests()[$index]->getBody()->getContents());

                throw new \Exception($reason);
            },
            function($response) use($articleService) {
                $contents = (new Response($response))->getContents()->articles->items;

                if (is_array($contents)) {
                    $this->createOrUpdateArticle($contents, $articleService);
                }
            }
        );
    }

    protected function createOrUpdateArticle(array $articles, ArticleService $articleService) {
        foreach($articles as $article) {
            $articleValue  = new ArticleValue($article->id);
            $singleArticle = $articleService->fetchSingle($articleValue);
            $articleFound  = ArticleEntity::where('name', $singleArticle->name)->first();

            if (is_null($articleFound)) {
                $this->createArticle($singleArticle);
            } else {
                $this->updateArticle($articleFound, $singleArticle);
            }

        }
    }

    protected function createArticle(\stdClass $article) {
        $articleEntity = (new ArticleEntity())->new(collect($article));

        forEach($article->categories as $category) {
            $category = CategoryEntity::where('category_id', $category)->first();

            if (!is_null($category)) {
                $articleEntity->categories()->attach($category->id);
            }
        }
    }

    protected function updateArticle(ArticleEntity $article, \stdClass $helpscoutArticle) {
        $article->updateExisting($article, collect($helpscoutArticle));
        $article->categories()->detach();

        forEach($helpscoutArticle->categories as $category) {
            $category = CategoryEntity::where('category_id', $category)->first();

            if (!is_null($category)) {
                $article->categories()->attach($category->id);
            }
        }
    }
}
