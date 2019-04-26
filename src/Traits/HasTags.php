<?php

namespace ctf0\Tagos\Traits;

use Spatie\Tags\HasTags as Spatie;
use Illuminate\Database\Eloquent\Model;

trait HasTags
{
    use Spatie;

    /**
     * support custom model.
     *
     * @return [type] [description]
     */
    public static function getTagClassName(): string
    {
        return config('tags.model');
    }
}
