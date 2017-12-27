<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Entities\ArticleCategory;
use \ArticleDelete;
use \CategoryDelete;
use \CollectionDelete;
use App\Helpscout\Domain\Values\Article;
use App\Helpscout\Domain\Values\Category;
use App\Helpscout\Domain\Values\Collection;
use GuzzleHttp\Exception\BadResponseException;

class DeleteCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:collection {collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all articles in a collection regardless of category';

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
        $collection = CollectionEntity::where('name', $this->argument('collection'))->first();
        $articles   = $collection->articles;
        $categories = $collection->categories;

        // Delete all articles from helpscout and the database
        forEach($articles as $article) {
            $articleValue = new Article($article->article_id);
            ArticleDelete::delete($articleValue);
            $article->delete();
        }

        // Delete all categories from helpscout and the database
        forEach($categories as $category) {
            $categoryValye = new Category($category->category_id);
            CategoryDelete::delete($categoryValye);

            // Remove the category relationship with articles.
            $articleCategory = ArticleCategory::where('category_id', $category->id)->delete();
            $category->delete();
        }

        // Delete the collection from helpscout and the database
        $collectionValue = new Collection($collection->collection_id);
        CollectionDelete::delete($collectionValue);
        $collection->delete();
    }
}
