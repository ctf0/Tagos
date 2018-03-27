<?php

namespace ctf0\Tagos;

class TagosRoutes
{
    public static function routes()
    {
        $controller = config('tags.controller');

        app('router')->group([
            'prefix' => 'tags',
            'as'     => 'tagos.',
        ], function () use ($controller) {
            // editor
            app('router')->get('editor', "$controller@editor")->name('editor');
            app('router')->post('/', "$controller@store")->name('store');
            // show all
            app('router')->get('/', "$controller@index")->name('index');
            app('router')->get('type/{type}', "$controller@indexByType")->name('index_type');
            // update
            app('router')->put('{id}/update', "$controller@update")->name('update');
            app('router')->post('update-multi', "$controller@updateMulti")->name('update_multi');
            // delete
            app('router')->delete('{id}/destroy', "$controller@destroy")->name('destroy');
            app('router')->post('destroy-multi', "$controller@destroyMulti")->name('destroy_multi');
            // show specific
            app('router')->get('{slug}', "$controller@show")->name('show');
            app('router')->get('type/{type}/tag/{slug}', "$controller@showByType")->name('show_type');
        });
    }
}
