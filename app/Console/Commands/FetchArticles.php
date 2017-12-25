<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Values\Category;
use App\Helpscout\Domain\Services\Article;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles';

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
        $categories = CategoryEntity::all();

        forEach($categories as $category) {
            $categoryValue = new Category($category->category_id);
            $categoryValue->setDbId($category->id);

            $article = new Article();
            $article->fetchAll($categoryValue);
        }
    }
}
