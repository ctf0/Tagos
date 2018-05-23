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
                <div v-if="filteredList.length && (showTagList || showAllTags)"
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
        <transition-group tag="div"
                          name="slide-up"
                          class="field is-grouped is-grouped-multiline">
            <div v-for="(item,i) in selectedTags" :key="i" class="control">
                <div class="tags has-addons">
                    <span class="tag is-info is-marginless">{{ item.name }}</span>
                    <span v-if="item.type" class="tag is-primary is-marginless">{{ item.type }}</span>
                    <span class="tag is-danger is-delete is-marginless link" @click="removeFromList(i)"/>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<style scoped lang="scss">
    .tag-input {
        position: relative;

        .tag-list {
            position: absolute;
            top: calc(100% - 1px);
            width: 100%;
            padding: 0.5rem 0.75rem 0.25rem;
            left: 0;
            z-index: 1;
            background: white;
            border: 1px solid #dde3e6;
            border-top: none;
            border-radius: 0 0 3px 3px;
            overflow: hidden;
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
            showTagList: true,
            selectedTags: this.oldList || []
        }
    },
    created() {
        document.addEventListener('keydown', this.shortCuts)
        document.addEventListener('click', (e) => {
            if (!this.isFocused('wrapper', e)) {
                this.hideLists()
            }
        })
    },
    computed: {
        filteredList() {
            if (this.tagName) {
                return this.fullList().filter((e) => {
                    return e.name.includes(this.tagName)
                })
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
            let list = this.filteredList.length

            // enter
            if (key == 13) {
                e.preventDefault()
                e.stopPropagation()

                // type
                if (this.isFocused('tagType', e)) {
                    if (!this.tagName) {
                        return this.showNotif(this.trans('no_val'), 'warning')
                    }

                    return this.addToList()
                }

                // name
                if (!list && this.isFocused('tagName', e)) {
                    return this.addToList()
                }
            }

            // esc
            if (key == 27) {
                return this.hideLists()
            }
        },

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

                return this.selectedTags.push(item)
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
            if (this.showAllTags) return this.showAllTags = false
            if (this.showTagList) return this.showTagList = false
        },

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
        }
    },
    watch: {
        tagName(val) {
            if (val == '') {
                this.showTagList = true
            }
        }
    }
}
</script>