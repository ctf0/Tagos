<?php

namespace ctf0\Tagos\Traits;

use Spatie\Tags\HasTags as Spatie;
use Illuminate\Database\Eloquent\Model;

trait HasTags
{
    use Spatie;

    public static function bootHasTags()
    {
        // add support for softDelete
        static::deleted(function (Model $deletedModel) {
            if (method_exists($deletedModel, 'isForceDeleting') && !$deletedModel->isForceDeleting()) {
                return;
            }

            $tags = $deletedModel->tags()->get();
            $deletedModel->detachTags($tags);
        });
    }

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
