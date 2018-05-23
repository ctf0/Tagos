<?php

namespace ctf0\Tagos\Controllers;

trait Ops
{
    protected function populateLocales($item)
    {
        foreach ($this->locales as $one) {
            if (!array_get($item, $one)) {
                $item[$one] = null;
            }
        }

        return $item;
    }

    protected function getModelsByTag($slug)
    {
        $tag = $this->tagClass->where('slug', $slug)->first();

        return $this->relation->get()->where('tag_id', $tag->id)->groupBy(function ($item) {
            return $item->taggable_type;
        })->map(function ($val, $model) use ($tag) {
            return app($model)->with(['user', 'tags'])->withAnyTags([$tag->name])->get();
        });
    }

    protected function getModelsByType($type, $slug)
    {
        $tag = $this->tagClass
            ->where('slug', $slug)
            ->where('type', $type)
            ->first();

        return $this->relation->get()->where('tag_id', $tag->id)->groupBy(function ($item) {
            return $item->taggable_type;
        })->map(function ($val, $model) {
            return $val->map(function ($item) use ($model) {
                return app($model)->with(['user', 'tags'])->find($item->taggable_id);
            });
        });
    }
}
