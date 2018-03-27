<?php

namespace ctf0\Tagos;

use Illuminate\Support\ServiceProvider;

class TagosServiceProvider extends ServiceProvider
{
    protected $file;

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->file = app('files');

        $this->packagePublish();

        // append extra data
        if (!app('cache')->store('file')->has('ct-tagos')) {
            $this->autoReg();
        }
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
     * [autoReg description].
     *
     * @return [type] [description]
     */
    protected function autoReg()
    {
        // routes
        $route_file = base_path('routes/web.php');
        $search     = 'Tagos';

        if ($this->checkExist($route_file, $search)) {
            $data = "\n// Tagos\nctf0\Tagos\TagosRoutes::routes();";

            $this->file->append($route_file, $data);
        }

        // mix
        $mix_file = base_path('webpack.mix.js');
        $search   = 'Tagos';

        if ($this->checkExist($mix_file, $search)) {
            $data = "\n// Tagos\nmix.sass('resources/assets/vendor/Tagos/sass/style.scss', 'public/assets/vendor/Tagos/style.css').version();";

            $this->file->append($mix_file, $data);
        }

        // run check once
        app('cache')->store('file')->rememberForever('ct-tagos', function () {
            return 'added';
        });
    }

    /**
     * [checkExist description].
     *
     * @param [type] $file   [description]
     * @param [type] $search [description]
     *
     * @return [type] [description]
     */
    protected function checkExist($file, $search)
    {
        return $this->file->exists($file) && !str_contains($this->file->get($file), $search);
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
