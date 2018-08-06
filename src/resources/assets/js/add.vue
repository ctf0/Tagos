<template>
    <div class="control">
        <!-- tags to be saved -->
        <input :value="JSON.stringify(selectedTags)"
               type="hidden"
               name="tags">

        <div ref="wrapper" class="field has-addons">
            <div class="control tag-input is-expanded">
                <!-- tag name -->
                <input ref="tagName"
                       v-model="tagName"
                       :placeholder="trans('tag_ph')"
                       class="input"
                       @dblclick="showAllTags = true">

                <!-- tags list -->
                <div v-show="filteredList.length && showAllTags"
                     class="tag-list field is-grouped is-grouped-multiline">
                    <div v-for="(item,i) in filteredList"
                         :key="i"
                         class="control link"
                         @click="addToList(item)">
                        <div class="tags has-addons">
                            <span class="tag is-info is-marginless">{{ item.name }}</span>
                            <span v-if="item.type" class="tag is-dark is-marginless">{{ item.type }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tag type -->
            <div class="control">
                <input ref="tagType"
                       v-model="tagType"
                       :placeholder="trans('type_ph')"
                       class="input">
            </div>

            <!-- add -->
            <div class="control">
                <button type="button" class="button is-dark" @click="addToList()">{{ trans('add_new') }}</button>
            </div>
        </div>

        <!-- selected tags -->
        <transition-group :style="listPadding"
                          tag="div"
                          name="slide-up"
                          class="field is-grouped is-grouped-multiline">
            <div v-for="(item,i) in selectedTags" :key="i" class="control">
                <div class="tags has-addons">
                    <span class="tag is-info is-marginless">{{ item.name }}</span>
                    <span v-if="item.type" class="tag is-primary is-marginless">{{ item.type }}</span>
                    <span class="tag is-danger is-delete is-marginless link" @click="removeFromList(i)"/>
                </div>
            </div>

            <!-- clear all -->
            <div v-tippy
                 v-show="selectedTags.length > 2"
                 key="delete"
                 :title="trans('clear_list')"
                 class="delete"
                 @click="clearTagsList()"/>
        </transition-group>
    </div>
</template>

<style scoped lang="scss">
    .field {
        transition: all 0.2s;
    }

    .tag-input {
        position: relative;

        .tag-list {
            position: absolute;
            top: 100%;
            width: 100%;
            padding: 1rem 0 0;
            left: 0;
        }
    }
</style>

<script>
export default {
    props: ['oldList', 'tagsList', 'translation'],
    data() {
        return {
            tagName: null,
            tagType: null,
            showAllTags: false,
            selectedTags: this.oldList || [],
            listPadding: ''
        }
    },
    created() {
        window.addEventListener('resize', this.updatePadding)
        document.addEventListener('keydown', this.shortCuts)
        document.addEventListener('click', (e) => {
            if (!this.isFocused('wrapper', e)) {
                this.hideLists()
            }
        })
    },
    computed: {
        fuseLib() {
            return new Fuse(this.fullList(), {keys: ['name']})
        },
        filteredList() {
            let val = this.tagName

            if (val) {
                return this.fuseLib.search(val)
            }

            if (this.showAllTags) {
                return this.fullList()
            }

            return []
        }
    },
    methods: {
        shortCuts(e) {
            let key = e.keyCode

            // enter
            if (key == 13) {
                e.preventDefault()
                e.stopPropagation()

                // name
                if (this.isFocused('tagName', e)) {
                    this.addToList()
                }

                // type
                if (this.isFocused('tagType', e)) {
                    if (!this.tagName) {
                        return this.showNotif(this.trans('no_val'), 'warning')
                    }

                    this.addToList()
                }
            }

            // esc
            if (key == 27) {
                return this.hideLists()
            }
        },

        // ops
        fullList() {
            let list = this.selectedTags.length ? this.selectedTags : []

            return this.tagsList.filter((obj) => {
                return !list.some((e) => {
                    return obj.name == e.name && obj.type == e.type
                })
            })
        },
        addToList(item = null) {
            if (item) {
                if (item.type == null && this.tagType) {
                    item.type = this.tagType
                }

                this.selectedTags.push(item)
                return this.updatePadding()
            }

            let str = this.tagName
            str ? this.selectedTags.push({name: str.trim(), type: this.tagType}) : this.showNotif(this.trans('no_val'), 'warning')

            this.tagName = ''
            this.$refs.tagName.focus()
        },
        removeFromList(i) {
            return this.selectedTags.splice(i, 1)
        },
        hideLists() {
            return this.showAllTags = false
        },
        clearTagsList() {
            this.selectedTags = []
        },

        // helpers
        isFocused(item, e) {
            return this.$refs[item].contains(e.target)
        },
        trans(key) {
            return this.translation[key]
        },
        showNotif(msg, s = 'success') {
            let title
            let duration

            switch (s) {
                case 'danger':
                    title = 'Error'
                    duration = null
                    break
                case 'warning':
                    title = 'Warning'
                    duration = 3
                    break
                default:
                    title = 'Success'
                    duration = 2
            }

            EventHub.fire('showNotif', {
                title: title,
                body: msg,
                type: s,
                duration: duration
            })
        },
        updatePadding(s = true) {
            !s
                ? this.listPadding = {'padding-top': 0}
                : this.$nextTick(() => {
                    this.listPadding = {'padding-top': document.querySelector('.tag-list').clientHeight + 'px'}
                })
        }
    },
    watch: {
        tagName(val) {
            if (val) {
                this.showAllTags = true
                return this.updatePadding()
            }

            this.hideLists()
        },
        filteredList(val) {
            if (!val.length) this.hideLists()
        },
        showAllTags(val) {
            if (!val) this.updatePadding(false)
        }
    }
}
</script>
