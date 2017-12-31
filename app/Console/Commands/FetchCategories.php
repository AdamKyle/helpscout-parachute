<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;
use App\Helpscout\Domain\Entities\Category as CategoryEntity;
use App\Helpscout\Domain\Values\Collection;
use App\Helpscout\Domain\Services\Category;


class FetchCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:categories {collection?}';

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
        $collectionName = $this->argument('collection');

        if (!is_null($collectionName)) {
            $collectionFound = CollectionEntity::where('name', $collectionName)->first();

            if (is_null($collectionFound)) {
                throw new \InvalidArgumentException($collectionName . ' does not exist.');
            }

            $collectionValue = new Collection($collectionFound->collection_id);
            $collectionValue->setDbId($collectionFound->id);

            $this->fetchAndCreate($collectionValue);
        } else {
            $collections = CollectionEntity::all();

            forEach($collections as $collection) {
                $collectionValue = new Collection($collection->collection_id);
                $collectionValue->setDbId($collection->id);

                $this->fetchAndCreate($collectionValue);
            }
        }
    }

    protected function fetchAndCreate(Collection $collectionValue) {
        $category   = new Category();
        $categories = $category->fetchAll($collectionValue);

        forEach($categories as $cat) {
            $collection = new Collection($cat->collectionId);
            $collection->setDbId(CollectionEntity::where('collection_id', $cat->collectionId)->first()->id);
            $category = (new CategoryEntity())->new(collect($cat), $collectionValue);
        }
    }
}
