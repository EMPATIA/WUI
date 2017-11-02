<?php

namespace App\Console;

use App\ComModules\Performance;
use App\Http\Controllers\UserAnalysisController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\UsersImport::class,
        Commands\TranslationsImport::class,
        Commands\TranslationsExport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();

        $schedule->call (function() {
            $performance = new Performance();
            $performance->getDataPerformanceAndSave();
        })->cron('* * * * * *');

        $schedule->call(function() {
            UserAnalysisController::getAndStoreAccessData();
        })->cron('5 * * * * *');
    }
}
