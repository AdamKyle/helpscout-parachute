<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FetchCollections;
use App\Console\Commands\FetchCategories;
use App\Console\Commands\FetchArticles;
use App\Console\Commands\CreateArticles;
use App\Console\Commands\ResetDatabase;
use App\Console\Commands\DeleteCollection;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FetchCollections::class,
        FetchCategories::class,
        FetchArticles::class,
        CreateArticles::class,
        ResetDatabase::class,
        DeleteCollection::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
