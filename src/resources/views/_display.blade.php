<div class="field is-grouped is-grouped-multiline">
    @foreach($tags as $tag)
        <div class="control">
            @if($tag->type)
                @if(isset($showType) && $showType == true)
                    <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tags has-addons">
                        <span class="tag is-primary">{{ $tag->name }}</span>
                        <span class="tag">{{ $tag->type }}</span>
                    </a>
                @else
                    <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tag is-primary">{{ $tag->name }}</a>
                @endif
            @else
                <a href="{{ route('tagos.show', $tag->slug) }}" class="tag is-primary">{{ $tag->name }}</a>
            @endif
        </div>
    @endforeach
</div>