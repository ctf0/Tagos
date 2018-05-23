<?php

namespace ctf0\Tagos\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagosController extends Controller
{
    use Ops;

    protected $relation;
    protected $locales;
    protected $tagClass;

    public function __construct()
    {
        $this->relation = app('db')->table('taggables');
        $this->locales  = [config('app.locale')];
        $this->tagClass = app('cache')->get('tagos');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags     = $this->tagClass;
        $showType = true;

        return view('Tagos::index', compact('tags', 'showType'));
    }

    public function indexByType($type)
    {
        $tags     = $this->tagClass->where('type', $type);
        $showType = false;

        return view('Tagos::index', compact('tags', 'showType'));
    }

    /**
     * Display tags editor.
     *
     * @return [type] [description]
     */
    public function editor()
    {
        $locales  = $this->locales;
        $relation = $this->relation->get();
        $tags     = $this->tagClass->map(function ($tag) use ($relation) {
            return [
                'count' => $relation->where('tag_id', $tag->id)->count(),
                'order' => $tag->order_column,
                'type'  => $tag->type,
                'name'  => $this->populateLocales($tag->getTranslations('name')),
                'slug'  => $this->populateLocales($tag->getTranslations('slug')),
                'id'    => $tag->id,
           ];
        });

        return view('Tagos::editor', compact('tags', 'locales'));
    }

    /**
     * Display Taged Items.
     *
     * @param mixed $tag
     *
     * @return [type] [description]
     */
    public function show($tag)
    {
        $models = $this->getModelsByTag($tag);

        return view('Tagos::show', compact('models'));
    }

    public function showByType($type, $slug)
    {
        $models = $this->getModelsByType($type, $slug);

        return view('Tagos::show', compact('models'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tag = app(config('tags.model'))->Create($request->all());

        return response()->json([
            'msg'  => trans('Tagos::messages.model_created'),
            'item' => [
                'count' => 0,
                'order' => $tag->order_column,
                'type'  => $tag->type,
                'name'  => $this->populateLocales($tag->getTranslations('name')),
                'slug'  => $this->populateLocales($tag->getTranslations('slug')),
                'id'    => $tag->id,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(['order' => 'required']);

        $reload    = false;
        $tagModel  = app(config('tags.model'));
        $new_order = $request->order;

        $tag = $tagModel->find($id);
        $tag->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        if ($new_order != $tag->order_column) {
            $reload = true;
            $tag->swapOrderWithModel($tagModel->where('order_column', $new_order)->first());
        }

        return response()->json([
            'msg'  => trans('Tagos::messages.model_updated'),
            'tag'  => [
                'count' => $this->relation->get()->where('tag_id', $tag->id)->count(),
                'order' => $tag->order_column,
                'type'  => $tag->type,
                'name'  => $this->populateLocales($tag->getTranslations('name')),
                'slug'  => $this->populateLocales($tag->getTranslations('slug')),
                'id'    => $tag->id,
            ],
            'reload' => $reload,
        ]);
    }

    public function updateMulti(Request $request)
    {
        foreach (app(config('tags.model'))->whereIn('id', $request->ids)->get() as $model) {
            $model->update(['type' => $request->type]);
        }

        return response()->json([
            'msg' => trans('Tagos::messages.model_updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        app(config('tags.model'))->destroy($id);

        return response()->json([
            'msg' => trans('Tagos::messages.model_deleted'),
        ]);
    }

    public function destroyMulti(Request $request)
    {
        app(config('tags.model'))->destroy($request->ids);

        return response()->json([
            'msg' => trans('Tagos::messages.models_deleted'),
        ]);
    }
}
