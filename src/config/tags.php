<?php

return [
    /*
     * The given function generates a URL friendly "slug" from the tag name property before saving it.
     */
    'slugger' => 'str_slug',

    /*
     * package controller
     * ex."https://github.com/ctf0/Tagos/wiki/Example-Controller"
     *
     * change this so you can assign middlewares to the routes
     * and resolve the extra locales for the editor so you can use it with already saved tags
     */
    'controller' => '\ctf0\Tagos\Controllers\TagosController',

    /*
     * package tag model
     * ex."https://github.com/ctf0/Tagos/wiki/Example-Model"
     *
     * so you can extend the functionality with ease
     * for example add revisions
     */
    'model' => Spatie\Tags\Tag::class,
];
