<?php

namespace ZerosDev\LaravelCaptcha;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/config.php' => config_path('captcha.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ZerosDev\LaravelCaptcha\Commands\Font::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Captcha', function() {
            return new Captcha;
        });
    }
}