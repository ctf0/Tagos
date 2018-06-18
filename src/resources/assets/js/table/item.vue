<template>
    <transition name="slide-up" mode="out-in">
        <div class="column is-3">
            <div :data-order="!more ? tagOrder : ''" class="box" @click="showMore()">
                <!-- select -->
                <section class="tag-ops">
                    <input :id="`tag-${tag.id}`" v-model="$parent.ids"
                           :value="tag.id"
                           type="checkbox"
                           class="cbx-checkbox">
                    <label :for="`tag-${tag.id}`" class="cbx is-marginless">
                        <svg width="14px" height="12px" viewBox="0 0 14 12"><polyline points="1 7.6 5 11 13 1"/></svg>
                    </label>
                </section>

                <template v-if="!more">
                    <!-- count -->
                    <p class="title">
                        <span class="icon"><icon name="anchor" scale="0.9"/></span>
                        <span>{{ tag.count }}</span>
                    </p>

                    <!-- title -->
                    <p class="title is-marginless">{{ getTitle(tag.name) }}</p>

                    <!-- slug -->
                    <p v-if="tag.count == 0" class="subtitle is-marginless">{{ getTitle(tag.slug) }}</p>
                    <a v-else
                       :href="getTagUrl"
                       target="_blank"
                       class="subtitle is-marginless has-text-info"
                       @click.stop>
                        {{ getTitle(tag.slug) }}
                    </a>

                    <div class="level m-t-20">
                        <!-- type -->
                        <div class="level-left">
                            <a v-if="tagType"
                               :href="getTagTypeUrl"
                               target="_blank"
                               class="tag is-primary"
                               @click.stop>
                                {{ tagType }}
                            </a>
                        </div>
                        <!-- delete -->
                        <div class="level-right">
                            <button class="button is-danger"
                                    @click.stop="FormSubmit('deleteRoute', 'delete')">
                                <span class="icon"><icon name="trash"/></span>
                            </button>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <!-- order -->
                    <div class="field">
                        <label class="label">{{ trans('order') }}</label>
                        <input v-model="tagOrder" type="text" class="tag-input" @click.stop>
                    </div>

                    <!-- name -->
                    <div class="field">
                        <label class="label">{{ trans('name') }}</label>
                        <ul class="tag-list" @click.stop>
                            <li v-for="(v, k) in tagName" :data-lang="k" class="tag-item">
                                <input v-model="tagName[k]"
                                       :placeholder="trans('tag_ph')"
                                       type="text"
                                       class="tag-input"
                                       @click.stop>
                            </li>
                        </ul>
                    </div>

                    <!-- type -->
                    <div class="field m-b-50">
                        <label class="label">{{ trans('type') }}</label>
                        <input v-model="tagType"
                               :placeholder="trans('type_ph')"
                               type="text"
                               class="tag-input"
                               @click.stop>
                    </div>

                    <!-- ops -->
                    <div class="level is-mobile">
                        <!-- update -->
                        <div class="level-left">
                            <button class="button is-success"
                                    @click.stop="FormSubmit('updateRoute', 'put')">
                                {{ trans('update') }}
                            </button>
                        </div>

                        <!-- delete -->
                        <div class="level-right">
                            <button class="button is-danger"
                                    @click.stop="FormSubmit('deleteRoute', 'delete')">
                                <span class="icon"><icon name="trash"/></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'tag-item',
    props: [
        'item',
        'index',
        'updateRoute',
        'deleteRoute',
        'showRoute',
        'typeRoute',
        'showTypeRoute'
    ],
    data() {
        return {
            more: false,
            tag: this.item,
            tagName: this.item.name,
            tagOrder: this.item.order,
            tagType: this.item.type
        }
    },
    mounted() {
        // multi delete
        EventHub.listen('tags-select-delete', (ids) => {
            ids.map((e) => {
                return e == this.tag.id
                    ? this.removeTagFromList(true)
                    : false
            })
        })

        // multi update
        EventHub.listen('tags-select-update', ({ids, type}) => {
            ids.map((e) => {
                if (e == this.tag.id) {
                    this.tag.type = type
                    this.tagType = type
                }

                return false
            })
        })
    },
    computed: {
        getTagUrl() {
            let type = this.tag.type
            let slug = this.getTitle(this.tag.slug)

            return type
                ? this.showTypeRoute.replace(0, slug).replace(1, type)
                : this.showRoute.replace(0, slug)
        },
        getTagTypeUrl() {
            return this.typeRoute.replace(0, this.tag.type)
        }
    },
    methods: {
        // helpers
        getTitle(title) {
            return this.$parent.getTitle(title)
        },
        showMore() {
            this.more = !this.more
        },
        trans(key) {
            return this.$parent.trans(key)
        },
        removeTagFromList(search = false) {
            let parent = this.$parent.tags

            parent.splice(search ? parent.indexOf(this.item) : this.index, 1)
        },

        // form
        FormSubmit(route, type) {
            // empty
            let test = Object.keys(this.tagName).reduce((res, k) => res && !(!!this.tagName[k] || this.tagName[k] === false || !isNaN(parseInt(this.tagName[k]))), true)
            if (test) {
                return this.$parent.showNotif(this.trans('no_val'), 'warning')
            }

            axios({
                method: type,
                url: this[route].replace(0, this.tag.id),
                data: {
                    name: this.tagName,
                    type: this.tagType,
                    order: this.tagOrder
                }
            }).then(({data}) => {
                if (data.tag) {
                    if (data.reload) {
                        return location.reload()
                    }

                    this.tag = data.tag
                }

                this.showMore()
                this.$parent.showNotif(data.msg)

                if (type == 'delete') {
                    this.removeTagFromList()
                }
            }).catch((err) => {
                this.$parent.showFormErrors(err.data.errors)
                console.error(err)
            })
        }
    }
}
</script>
