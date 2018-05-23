@extends('Tagos::partials.shared')

@section('content')
    <tagos-index inline-template :names-list="{{ $tags->pluck('name') }}">
        <div class="column">
            {{-- search --}}
            <div class="field has-addons has-addons-right">
                {{-- input --}}
                <p class="control has-icons-left">
                    <input class="input"
                        type="text"
                        v-model="searchFor"
                        placeholder="{{ trans('Tagos::messages.find') }}">
                    <span class="icon is-left"><icon name="search"></icon></span>
                </p>
                {{-- clear --}}
                <p class="control">
                    <button class="button is-black"
                        :disabled="!searchFor"
                        @click="resetSearch()">
                        <span class="icon"><icon name="times"></icon></span>
                    </button>
                </p>
            </div>

            {{-- list --}}
            <div class="field is-grouped is-grouped-multiline">
                @foreach($tags as $tag)
                    <transition name="slide-up">
                        <div class="control" v-show="inSearchList('{{ $tag->name }}')">
                            @if($tag->type)
                                @if(isset($showType) && $showType == true)
                                    <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tags has-addons">
                                        <span class="tag is-primary">{{ $tag->name }}</span>
                                        <span class="tag">{{ $tag->type }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('tagos.show_type', [$tag->type, $tag->slug]) }}" class="tag is-primary" v-show="inSearchList('{{ $tag->name }}')">{{ $tag->name }}</a>
                                @endif
                            @else
                                <a href="{{ route('tagos.show', $tag->slug) }}" class="tag is-primary">{{ $tag->name }}</a>
                            @endif
                        </div>
                    </transition>
                @endforeach
            </div>
        </div>
    </tagos-index>
@endsection