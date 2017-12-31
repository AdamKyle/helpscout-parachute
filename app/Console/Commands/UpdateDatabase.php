<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Services\Collection;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Entities\Article as ArticleEntity;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;

class UpdateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the docs in the database';

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
        $collection  = new Collection();
        $collections = $collection->fetchAll();

        forEach($collections as $col) {
            if (is_null($collection->findInDatabase($col->name))) {

                // Create the collection and then call fetch categories and articles.
                $collectionDb = (new CollectionEntity())->new(collect($col));
                $this->call('fetch:categories', ['collection' => $col->name]);

                forEach($collectionDb->categories as $category) {
                    $this->call('fetch:articles', ['category' => $category->name]);
                }
            }
        }
    }
}
