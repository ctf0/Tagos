<div class="field is-grouped is-grouped-multiline">
    @foreach($tags as $tag)
        <div class="control">
            @if($tag->type)
                @if(isset($showType) && $showType == true)
                    <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tags has-addons">
                        <span class="tag is-danger">{{ $tag->name }}</span>
                        <span class="tag">{{ $tag->type }}</span>
                    </a>
                @else
                    <div class="tags">
                        <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tag is-danger">{{ $tag->name }}</a>
                    </div>
                @endif
            @else
                <div class="tags">
                    <a href="{{ route('tagos.show', $tag->slug) }}" class="tag is-danger">{{ $tag->name }}</a>
                </div>
            @endif
        </div>
    @endforeach
</div>