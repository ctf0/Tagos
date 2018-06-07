# Tagos

[![Latest Stable Version](https://img.shields.io/packagist/v/ctf0/tagos.svg)](https://packagist.org/packages/ctf0/tagos) [![Total Downloads](https://img.shields.io/packagist/dt/ctf0/tagos.svg)](https://packagist.org/packages/ctf0/tagos) [![Donate with Bitcoin](https://en.cryptobadges.io/badge/micro/16ri7Hh848bw7vxbEevKHFuHXLmsV8Vc9L)](https://en.cryptobadges.io/donate/16ri7Hh848bw7vxbEevKHFuHXLmsV8Vc9L)

A Tag **Editor** and **Selector** based on [spatie/laravel-tags](https://github.com/spatie/laravel-tags).

<h4 align="center">Editor</h4>
<p align="center">
    <img src="https://user-images.githubusercontent.com/7388088/38246148-1a34865a-3741-11e8-99bd-51afd10df282.png">
</p>
<h4 align="center">Selector</h4>
<p align="center">
    <img src="https://user-images.githubusercontent.com/7388088/38068467-9407081a-3311-11e8-83a5-eea196fb00e3.png">
</p>

- package requires Laravel v5.5+

<br>

## Installation

- `composer require ctf0/tagos`

- publish the package assets with

    `php artisan vendor:publish --provider="ctf0\Tagos\TagosServiceProvider"`  
    `php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="migrations"`

- after installation, package will auto-add
    + package routes to `routes/web.php`
    + package assets compiling to `webpack.mix.js`

- install dependencies

    ```bash
    yarn add vue vue-awesome@v2 vue-notif axios
    # or
    npm install vue vue-awesome@v2 vue-notif axios --save
    ```

- add this one liner to your main js file and run `npm run watch` to compile your `js/css` files.
    - if you are having issues [Check](https://ctf0.wordpress.com/2017/09/12/laravel-mix-es6/)

    ```js
    require('../vendor/Tagos/js/manager')

    new Vue({
        el: '#app'
    })
    ```

<br>

## Features
- tags editor & selector.
- show tag suggestion as you type.
- easily add new tag name & type.
- taggable models softdelete.
- show tagged items by tag & by type.
- search for tags by name in tags index.
- shortcuts

    |   navigation   | keyboard | mouse (click) |
    |----------------|----------|---------------|
    | show all tags  |          | *(input)* 2x  |
    | add new tag    | enter    | *             |
    | hide tags list | esc      | anywhere      |

<br>

## Config
**config/tags.php**

```php
return [
    /*
     * The given function generates a URL friendly "slug" from the tag name property before saving it.
     */
    'slugger' => 'str_slug',

    /*
     * package controller
     * ex."https://github.com/ctf0/Tagos/wiki/Example-Controller"
     */
    'controller' => '\ctf0\Tagos\Controllers\TagosController',

    /*
     * package tag model
     * ex."https://github.com/ctf0/Tagos/wiki/Example-Model"
     */
    'model' => Spatie\Tags\Tag::class,
];
```

<br>

## Usage

- migrate the tags table with `php artisan migrate`

- there is also a seeder to quickly get you going
    ```php
    // database/seeds/DatabaseSeeder.php

    class DatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run()
        {
            //...

            $this->call(TagsTableSeeder::class);
        }
    }
    ```

- add `HasTags` trait to your model ex.`post`

    ```php
    use ctf0\Tagos\Traits\HasTags;
    use Illuminate\Database\Eloquent\Model;

    class Post extends Model
    {
        use HasTags;
    }
    ```

<br>

> #### Get All tags
```php
app('cache')->get('tagos');
```

> #### Attaching Tags

- show the tag selector
    + ex.`posts create view`

        ```blade
        @include('Tagos::partials.add')
        ```

    + ex.`posts edit view`

        ```blade
        @include('Tagos::partials.add', ['old' => app('tagos')->getModelTags($post)])
        ```

- save the tags
    + `store()`

        ```php
        $model = Post::create([...]);

        app('tagos')->saveTags($model, $request);
        ```

    + `update()`

        ```php
        $model = Post::find($id)->update([...]);

        app('tagos')->saveTags($model, $request);
        ```

<br>

> #### Display Model Tags

```blade
@include('Tagos::partials.display', [
    'tags' => $post->tags,
    'showType' => true // whether to show the tag type or not
])
```

<br>

#### Routes

| Method |             URL             |         Name        |                        Action                        |
|--------|-----------------------------|---------------------|------------------------------------------------------|
| GET    | tags/editor                 | tagos.editor        | \ctf0\Tagos\Controllers\TagosController@editor       |
| GET    | tags                        | tagos.index         | \ctf0\Tagos\Controllers\TagosController@index        |
| GET    | tags/type/{type}            | tagos.index_type    | \ctf0\Tagos\Controllers\TagosController@indexByType  |
| GET    | tags/{slug}                 | tagos.show          | \ctf0\Tagos\Controllers\TagosController@show         |
| GET    | tags/type/{type}/tag/{slug} | tagos.show_type     | \ctf0\Tagos\Controllers\TagosController@showByType   |
