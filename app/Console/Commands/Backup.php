<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection;
use App\Models\Category;
use App\Models\Article;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'back:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the documentation from the database';

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
        // First update the database.
        // $this->call('update:docs');

        // Get all Collections:
        $collections = Collection::all();

        forEach($collections as $collection) {
            $this->handleCategories($collection);
        }
    }

    protected function handleCategories(Collection $collection) {
        $categories = $collection->categories;

        forEach($categories as $category) {
            $this->handleArticles($collection, $category);
        }
    }

    protected function handleArticles(Collection $collection, Category $category) {
        $articles = $collection->articles;

        forEach($articles as $article) {
            $category = $article->categories->first();
            $this->createDocument($collection, $category, $article);
        }
    }

    protected function createDocument(Collection $collection, Category $category, Article $article) {
        \Storage::disk('helpscout_docs')->put($collection->name . '/' . $category->name . '/' . $article->name . '.html', $article->content);
    }
}
