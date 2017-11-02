<?php
namespace App\Providers;

use Form;
use Illuminate\Support\ServiceProvider;
use Session;
use Request;
use ONE;
use Carbon\Carbon;

class FormMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require base_path() . '/resources/macros/oneForm.php'; //It can be register like this
        require base_path() . '/resources/macros/oneVote.php';
        require base_path() . '/resources/macros/oneMessage.php';
        require base_path() . '/resources/macros/oneLogin.php';
        require base_path() . '/resources/macros/oneVoteEmpavilleSchools.php';
        require base_path() . '/resources/macros/oneCommentDefault.php';
        require base_path() . '/resources/macros/oneAnnotator.php';
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
