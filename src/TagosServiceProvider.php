<?php

namespace ctf0\Tagos;

use ctf0\Tagos\Commands\PackageSetup;
use ctf0\Tagos\Observers\TagObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class TagosServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->packagePublish();
        $this->cacheAndObserver();
        $this->command();
    }

    /**
     * [packagePublish description].
     *
     * @return [type] [description]
     */
    protected function packagePublish()
    {
        // config
        $this->publishes([
            __DIR__ . '/config' => config_path(),
        ], 'config');

        // seeds
        $this->publishes([
            __DIR__ . '/database/seeds' => database_path('seeds'),
        ], 'seeds');

        // resources
        $this->publishes([
            __DIR__ . '/resources/assets' => resource_path('assets/vendor/Tagos'),
        ], 'assets');

        // trans
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Tagos');
        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('lang/vendor/Tagos'),
        ], 'trans');

        // views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Tagos');
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/Tagos'),
        ], 'views');
    }

    /**
     * model events cacheAndObserver.
     *
     * @return [type] [description]
     */
    protected function cacheAndObserver()
    {
        $model = $this->app['config']->get('tags.model');

        if ($model && Schema::hasTable('tags')) {
            $this->app['cache']->rememberForever('tagos', function () use ($model) {
                return $this->app->make($model)->ordered()->get();
            });

            $this->app->make($model)->observe(TagObserver::class);
        }
    }

    /**
     * package commands.
     *
     * @return [type] [description]
     */
    protected function command()
    {
        $this->commands([
            PackageSetup::class,
        ]);
    }

    /**
     * Register any package services.
     *
     * @return [type] [description]
     */
    public function register()
    {
        $this->app->singleton('tagos', function () {
            return new Tagos();
        });
    }
}
