@extends('Tagos::partials.shared')
@section('title', trans('Tagos::messages.tags'))

@section('content')
    <tagos-list inline-template
        select-first="{{ app()->getLocale() }}"
        :list="{{ json_encode($tags) }}"
        :translations="{{ json_encode([
            'no_val' => trans('Tagos::messages.no_val'), 
            'tag_exist' => trans('Tagos::messages.tag_exist'), 
            'tag_ph' => trans('Tagos::messages.tag_ph'), 
            'type_ph' => trans('Tagos::messages.type_ph'), 
            'update' => trans('Tagos::messages.update'), 
            'order' => trans('Tagos::messages.order'), 
            'name' => trans('Tagos::messages.name'), 
            'type' => trans('Tagos::messages.type'), 
        ]) }}">
        <div class="container">
            <div class="level">
                {{-- count --}}
                <div class="level-left">
                    <p class="title">
                        {{ trans('Tagos::messages.tags') }} "<span>@{{ itemsCount }}</span>"
                    </p>
                </div>

                {{-- search --}}
                <div class="level-right">
                    <div class="field has-addons has-addons-right">
                        {{-- type --}}
                        <p class="control">
                            <span class="select">
                                <select v-model="searchFieldType">
                                    <option v-for="n in searchFields" :key="n">@{{ n }}</option>
                                </select>
                            </span>
                        </p>
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
                </div>
            </div>

                {{-- update multi --}}
                <div class="level">
                    <div class="level-left">
                        <div class="field has-addons is-marginless">
                            {{-- delete multi --}}
                            <div class="control">
                                <form action="{{ route('tagos.destroy_multi') }}"
                                    method="POST"
                                    @submit.prevent="DeleteTags($event)">
                                    <button type="submit"
                                        class="button is-danger"
                                        :disabled="ids.length < 1">
                                        {{ trans('Tagos::messages.delete') }}
                                    </button>
                                </form>
                            </div>

                            {{-- select multi  --}}
                            <div class="control">
                                <div class="button" @click="selectAll()">
                                    <template v-if="ids.length < 1">{{ trans('Tagos::messages.select_all') }}</template>
                                    <template v-else>{{ trans('Tagos::messages.select_non') }}</template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- update --}}
                    <div class="level-right">
                        <form action="{{ route('tagos.update_multi') }}" method="POST" @submit.prevent="UpdateTags($event)">
                            <div class="field has-addons">
                                <div class="control">
                                    <input class="input" v-model="multiTypeName" placeholder="{{ trans('Tagos::messages.type_ph') }}">
                                </div>
                                <div class="control">
                                    <button type="submit"
                                        class="button is-success"
                                        :disabled="ids.length < 1">
                                        {{ trans('Tagos::messages.update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- add new --}}
                <div class="field">
                    <form action="{{ route('tagos.store') }}" method="post" @submit.prevent="SaveNew($event)">
                        <div class="field has-addons">
                            {{-- name --}}
                            <div class="control is-expanded input-box">
                                <div class="select toggle-locale">
                                    <select v-model="nameLocale">
                                        @foreach($locales as $code)
                                            <option value="{{ $code }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach($locales as $code)
                                    <input type="text"
                                        name="name[{{ $code }}]"
                                        class="input toggle-pad"
                                        v-show="showName('{{ $code }}')"
                                        @input="addNewItem('{{ $code }}', $event)"
                                        placeholder="{{ trans('Tagos::messages.tag_ph') }}">
                                @endforeach
                                @if($errors->has('name'))
                                    <p class="help is-danger">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                            {{-- type --}}
                            <div class="control">
                                <input type="text" class="input" v-model="newItem.type" placeholder="{{ trans('Tagos::messages.type_ph') }}">
                            </div>
                            {{-- submit --}}
                            <div class="control">
                                <button type="submit" class="button is-link">{{ trans('Tagos::messages.add_new') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- list --}}
            <tagos-item v-for="(tag, i) in tags"
                :key="tag.id"
                :item="tag"
                :index="i"
                show-route="{{ route('tagos.show', 0) }}"
                type-route="{{ route('tagos.index_type', 0) }}"
                show-type-route="{{ route('tagos.show_type', [1, 0]) }}"
                update-route="{{ route('tagos.update', 0) }}"
                delete-route="{{ route('tagos.destroy', 0) }}">
            </tagos-item>
        </div>
    </tagos-list>
@endsection
