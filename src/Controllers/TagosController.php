<?php

namespace ctf0\Tagos\Controllers;

use DB;
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
        $this->relation = DB::table('taggables');
        $this->locales  = [config('app.locale')];
        $this->tagClass = app(config('tags.model'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags     = $this->tagClass->ordered()->get();
        $showType = true;

        return view('Tagos::index', compact('tags', 'showType'));
    }

    public function indexByType($type)
    {
        $tags     = $this->tagClass->getWithType($type);
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
        $tags     = $this->tagClass->ordered()->get()->map(function ($tag) use ($relation) {
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
        $tag = $this->tagClass->Create($request->all());

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
        $tagModel  = $this->tagClass->get();
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
        foreach ($this->tagClass->whereIn('id', $request->ids)->get() as $model) {
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
        $this->tagClass->destroy($id);

        return response()->json([
            'msg' => trans('Tagos::messages.model_deleted'),
        ]);
    }

    public function destroyMulti(Request $request)
    {
        $this->tagClass->destroy($request->ids);

        return response()->json([
            'msg' => trans('Tagos::messages.models_deleted'),
        ]);
    }
}
