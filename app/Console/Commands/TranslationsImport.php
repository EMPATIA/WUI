<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class TranslationsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import existing translations to Database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("Translations import started (" . Carbon::now() . ")");

        $response = app()->call([app()->make('App\Modules\Translations\Controllers\TranslationsController'), 'getAllStrings'], []);

        $consumedTime = number_format(microtime(true)-LARAVEL_START,0);

        $this->info($response);
        $this->info("Consumed " . $consumedTime . " seconds");
    }
}
