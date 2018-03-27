<template>
    <transition name="slide-up" mode="out-in">
        <tr @click="showMore()" class="link"
            @mouseenter="!more ? $event.target.classList.add('is-selected') : false"
            @mouseleave="$event.target.classList.remove('is-selected')">

            <!-- select -->
            <td :class="{'align-top': more}" style="text-align: center">
                <p>
                    <input type="checkbox" :id="`tag-${tag.id}`"
                           v-model="$parent.ids"
                           class="cbx-checkbox"
                           :value="tag.id">
                    <label :for="`tag-${tag.id}`" class="cbx is-marginless">
                        <svg width="14px" height="12px" viewBox="0 0 14 12"><polyline points="1 7.6 5 11 13 1"/></svg>
                    </label>
                </p>
            </td>

            <!-- order -->
            <td :class="{'align-top': more}" style="text-align: center">
                <p>{{ tag.order }}</p>
                <p v-show="more" class="align-edit">
                    <input type="text" v-model="tagOrder" class="tag-input" @click.stop>
                </p>
            </td>

            <!-- name -->
            <td :class="{'align-top': more}">
                <p>{{ getTitle(tag.name) }}</p>
                <ul class="tag-list" v-show="more">
                    <li v-for="(v, k) in tagName" class="tag-item">
                        <span class="tag-key">{{ k }} :</span>
                        <input type="text" v-model="tagName[k]" class="tag-input" :placeholder="trans('tag_ph')" @click.stop>
                    </li>
                </ul>
            </td>

            <!-- slug -->
            <td :class="{'align-top': more}">
                <p v-if="tag.count == 0">{{ getTitle(tag.slug) }}</p>
                <p v-else><a :href="getTagUrl" target="_blank" @click.stop>{{ getTitle(tag.slug) }}</a></p>
            </td>

            <!-- type -->
            <td :class="{'align-top': more}">
                <p><a :href="getTagTypeUrl" target="_blank" @click.stop>{{ tag.type || '&nbsp;' }}</a></p>
                <p v-show="more" class="align-edit">
                    <input type="text" v-model="tagType" class="tag-input" :placeholder="trans('type_ph')" @click.stop>
                </p>
            </td>

            <!-- count -->
            <td :class="{'align-top': more}">
                <p>{{ tag.count }}</p>
            </td>

            <!-- ops -->
            <td :class="{'align-top': more}">
                <div class="field is-grouped is-grouped-centered">
                    <p class="control">
                        <button class="is-inline-block button is-success" @click.stop="FormSubmit('updateRoute', 'put')" v-if="more">{{ trans('update') }}</button>
                    </p>
                    <p class="control">
                        <button class="is-inline-block button is-danger" @click.stop="FormSubmit('deleteRoute', 'delete')">{{ trans('delete') }}</button>
                    </p>
                </div>
            </td>
        </tr>
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