<?php

namespace Ktquez\Tinymce;

use Illuminate\Support\ServiceProvider;

class TinymceServiceProvider extends ServiceProvider
{

	public function boot()
	{
		$this->publishes([
            __DIR__.'/config/tinymce.php' => base_path('config/tinymce.php')
        ], 'config');

        $this->publishes([
        	__DIR__.'/assets/js/tinymce/' => base_path('public/vendor/js/tinymce')        	
        ], 'public');

		$this->loadViewsFrom(__DIR__.'/resources/view','tinymce');
	}

	public function register()
	{
		
	}

}