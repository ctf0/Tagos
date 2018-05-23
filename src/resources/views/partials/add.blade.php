<tagos :old-list="{{ old('tags') ?? $old ?? '[]' }}"
    :tags-list="{{ app('tagos')->getTags() ?? '[]' }}"
    :translation="{{ json_encode([
        'tag_exist' => trans('Tagos::messages.tag_exist'), 
        'no_val' => trans('Tagos::messages.no_val'), 
        'add_new' => trans('Tagos::messages.add_new'), 
        'tag_ph' => trans('Tagos::messages.tag_ph'), 
        'type_ph' => trans('Tagos::messages.type_ph'), 
    ]) }}">
</tagos>