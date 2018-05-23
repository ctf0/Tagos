<?php

namespace ctf0\Tagos;

class Tagos
{
    protected $tagClass;

    public function __construct()
    {
        $this->tagClass = app('cache')->get('tagos');
    }

    /**
     * resolve all tags.
     *
     * @return [type] [description]
     */
    public function getTags()
    {
        $res = $this->tagClass->map(function ($item) {
            return [
                'name'=> $item->name,
                'type'=> $item->type,
            ];
        });

        return json_encode($res);
    }

    /**
     * resolve model tags.
     *
     * @param mixed $model
     *
     * @return [type] [description]
     */
    public function getModelTags($model)
    {
        if (!$model->tags->count()) {
            return json_encode([]);
        }

        $res = $model->tags->map(function ($item) {
            return [
                'name'=> $item->name,
                'type'=> $item->type,
            ];
        });

        return json_encode($res);
    }

    /**
     * save & sync tags correctly to model.
     *
     * @param [type] $model   [description]
     * @param [type] $request [description]
     *
     * @return [type] [description]
     */
    public function saveTags($model, $request)
    {
        $items = [];
        $tags  = json_decode($request->tags, true);

        foreach ($tags as $one) {
            $items[] = app(config('tags.model'))->findOrCreate($one['name'], $one['type']);
        }

        return $model->syncTags($items);
    }
}
