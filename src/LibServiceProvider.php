<?php

namespace Hanoivip\Userinfo;

use Illuminate\Support\ServiceProvider;

class LibServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/id.php' => config_path('id.php'),
            __DIR__.'/../views' => resource_path('views/vendor/hanoivip'),
            __DIR__.'/../lang' => resource_path('lang/vendor/hanoivip'),
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom( __DIR__.'/../lang', 'hanoivip');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
    
    public function register()
    {
        
    }
}
