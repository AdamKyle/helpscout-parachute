<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpscout\Domain\Services\Collection;
use App\Helpscout\Domain\Entities\Collection as CollectionEntity;

class FetchCollections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:collections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Collections';

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
        $collection = new Collection();
        $collections = $collection->fetchAll();

        forEach($collections as $collection) {
            (new CollectionEntity())->new(collect($collection));
        }
    }
}
