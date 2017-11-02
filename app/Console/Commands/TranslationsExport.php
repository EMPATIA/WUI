<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class TranslationsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export existing translations to files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("Translations export started (" . Carbon::now() . ")");

        app()->call([app()->make('App\Modules\Translations\Controllers\TranslationsController'), 'saveAllStrings'], []);
        $consumedTime = number_format(microtime(true)-LARAVEL_START,0);

        $errors = \Session::get('error');
        $success = \Session::get("message");
        if (!empty($errors))
            $this->error($errors);
        if (!empty($success))
            $this->info($success);

        $this->info("Consumed " . $consumedTime . " seconds");
    }
}
