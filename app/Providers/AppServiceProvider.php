<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('after_or_equal', function ($attribute, $value, $parameters, $validator) {
            return $value == $parameters[0] <= $value;
        });
        \Log::useDailyFiles(storage_path().'/logs/voting.log', 60, "info");
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
