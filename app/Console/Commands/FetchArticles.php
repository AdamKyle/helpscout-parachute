<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Values\Category;
use App\Helpscout\Domain\Values\Article as ArticleValue;
use App\Helpscout\Domain\Values\Collection as CollectionValue;
use App\Helpscout\Domain\Services\Article;
use App\Helpscout\Domain\Services\Collection;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles {category?} {collectionId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all articles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $categoryName      = $this->argument('category');
        $collectionId      = $this->argument('collectionId');
        $categoryCollecton = CategoryEntity::all();
        $articleService    = new Article();

        if (!is_null($categoryName) && !is_null($collectionId)) {
            $categoryValue = (new CategoryEntity())->findByNameAndCollectionId($categoryName, $collectionId);

            if (is_null($categoryValue)) {
                throw new \Exception($categoryName . ' was not found.');
            }

            $articles = $articleService->fetchAll($categoryValue);
            $this->createOrUpdate($articles, $articleService, $categoryValue);
        } else {
            forEach($categoryCollecton as $category) {
                $categoryValue = new Category($category->category_id);
                $categoryValue->setDbId($category->id);

                $articles = $articleService->fetchAll($categoryValue);

                $this->createOrUpdate($articles, $articleService, $categoryValue);
            }
        }
    }

    protected function createOrUpdate(array $articles, Article $articleService, Category $categoryValue) {
        if (count($articles) > 0) {
            forEach($articles as $article) {
                $foundArticle = ArticleEntity::where('article_id', $article->id)->first();

                if (is_null($foundArticle)) {
                    $this->create($article, $articleService, $categoryValue);
                } else {
                    $this->update($foundArticle, $article, $articleService, $categoryValue);
                }
            }
        }
    }

    protected function create(\stdClass $article, Article $articleService, Category $categoryValue) {
        $articleValue  = new ArticleValue($article->id);
        $singleArticle = $articleService->fetchSingle($articleValue);
        $articleModel  = (new ArticleEntity())->new(collect($singleArticle));

        $articleModel->categories()->attach($categoryValue->getDbId());
    }

    protected function update(ArticleEntity $foundArticle, \stdClass $article, Article $articleService, Category $categoryValue) {
        $articleValue  = new ArticleValue($article->id);
        $singleArticle = $articleService->fetchSingle($articleValue);
        $articleModel  = (new ArticleEntity())->updateExisting($foundArticle, collect($singleArticle));

        // We assume one article has one category and thus we detatch all and reattach incase they might have changed.
        $articleModel->categories()->detach();
        $articleModel->categories()->attach($categoryValue->getDbId());
    }
}
