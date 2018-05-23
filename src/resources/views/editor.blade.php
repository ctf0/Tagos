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
            'delete' => trans('Tagos::messages.delete'), 
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

            {{-- add new --}}
            <div class="field m-t-20">
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
            <br>

            {{-- update multi --}}
            <div class="level" v-if="ids.length > 1">
                <div class="level-left"></div>
                <div class="level-right">
                    {{-- update --}}
                    <div class="level-item">
                        <form action="{{ route('tagos.update_multi') }}" method="POST" @submit.prevent="UpdateTags($event)">
                            <div class="field has-addons">
                                <div class="control">
                                    <input class="input" v-model="multiTypeName" placeholder="{{ trans('Tagos::messages.type_ph') }}">
                                </div>
                                <div class="control">
                                    <button type="submit" class="button is-success">{{ trans('Tagos::messages.update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- list --}}
            <table class="table is-fullwidth">
                <thead>
                    <tr>
                        <template v-if="tags.length == 0">
                            <th class="no_table_data">{{ trans('Tagos::messages.no_entries') }}</th>
                        </template>
                        <template v-else>
                            <th width="1%" class="is-dark">
                                <div class="field has-addons is-marginless">
                                    {{-- select multi  --}}
                                    <div class="control">
                                        <div class="button is-borderless is-light">
                                            <input type="checkbox" id="all" class="cbx-checkbox"
                                                @click="selectAll()"
                                                :checked="ids.length">
                                            <label for="all" class="cbx is-marginless">
                                                <svg width="14px" height="12px" viewBox="0 0 14 12"><polyline points="1 7.6 5 11 13 1"/></svg>
                                            </label>
                                        </div>
                                    </div>
                                    {{-- delete multi --}}
                                    <div class="control">
                                        <form action="{{ route('tagos.destroy_multi') }}"
                                            method="POST"
                                            @submit.prevent="DeleteTags($event)">
                                            <button type="submit"
                                                class="button is-borderless"
                                                :disabled="ids.length < 2">
                                                {{ trans('Tagos::messages.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </th>
                            <th width="1%" nowrap class="is-dark has-text-centered">{{ trans('Tagos::messages.order') }}</th>
                            <th class="is-dark">{{ trans('Tagos::messages.name') }}</th>
                            <th class="is-dark">{{ trans('Tagos::messages.slug') }}</th>
                            <th class="is-dark">{{ trans('Tagos::messages.type') }}</th>
                            <th width="1%" nowrap class="is-dark">{{ trans('Tagos::messages.count') }}</th>
                            <th width="1%" nowrap class="is-dark">{{ trans('Tagos::messages.ops') }}</th>
                        </template>
                    </tr>
                </thead>

                <tbody>
                    <tr is="tagos-item"
                        v-for="(tag, i) in tags"
                        :key="tag.id"
                        :item="tag"
                        :index="i"
                        show-route="{{ route('tagos.show', 0) }}"
                        type-route="{{ route('tagos.index_type', 0) }}"
                        show-type-route="{{ route('tagos.show_type', [1, 0]) }}"
                        update-route="{{ route('tagos.update', 0) }}"
                        delete-route="{{ route('tagos.destroy', 0) }}">
                    </tr>
                </tbody>
            </table>
        </div>
    </tagos-list>
@endsection