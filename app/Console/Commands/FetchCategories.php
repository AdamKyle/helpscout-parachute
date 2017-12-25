<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Domain\Services\Category;


class FetchCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store all categories';

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
        $collections = CollectionEntity::all();
        $category    = new Category();

        forEach($collections as $collection) {
            $collectionValue = new Collection($collection->collection_id);
            $collectionValue->setDbId($collection->id);

            $category->fetchAll($collectionValue);
        }
    }
}
