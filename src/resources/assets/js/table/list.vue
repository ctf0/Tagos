<script>
import TagosItem from './item.vue'
import Search from './../mixins/search'
import debounce from 'lodash/debounce'
import orderBy from 'lodash/orderBy'

export default {
    components: {TagosItem},
    name: 'tag-index',
    mixins: [Search],
    props: ['list', 'selectFirst', 'translations'],
    data() {
        return {
            nameLocale: '',
            newItem: {
                type: null,
                tag: {}
            },
            multiTypeName: '',
            tags: this.list,
            ids: [],
            searchFieldType: 'name',
            searchFields: [
                'name',
                'type'
            ]
        }
    },
    mounted() {
        this.nameLocale = this.selectFirst
        this.tags = orderBy(this.tags, ['count', 'order'], ['desc', 'asc'])
    },
    computed: {
        itemsCount() {
            return this.tags.length
        }
    },
    methods: {
        // save new item
        SaveNew(event) {
            let newTag = this.newItem.tag
            let newType = this.newItem.type

            // empty
            if (Object.keys(newTag).length == 0) {
                return this.showNotif(this.trans('no_val'), 'warning')
            }

            // exist
            let name = newTag[this.nameLocale]
            let check = this.tags.some((e) => {
                return e.name[this.nameLocale] == name && e.type == newType
            })

            if (check) {
                this.searchFor = name
                this.$refs.search.focus()
                return this.showNotif(this.trans('tag_exist'), 'danger')
            }

            // submit
            axios.post(event.target.action, {
                name: newTag,
                type: newType
            }).then(({data}) => {
                document.querySelectorAll('.toggle-pad').forEach((e) => {
                    e.value = ''
                })

                this.newItem = {
                    type: null,
                    tag: {}
                }
                this.list.push(data.item)
                this.showNotif(data.msg)
            }).catch((err) => {
                this.showFormErrors(err.data.errors)
                console.error(err)
            })
        },
        // delete multi
        DeleteTags(event) {
            axios.post(event.target.action, {
                ids: this.ids
            }).then(({data}) => {
                EventHub.fire('tags-select-delete', this.ids)
                this.ids = []
                this.showNotif(data.msg)
            }).catch((err) => {
                this.showFormErrors(err.data.errors)
                console.error(err)
            })
        },
        // update multi
        UpdateTags(event) {
            let ids = this.ids
            let type = this.multiTypeName

            axios.post(event.target.action, {
                ids: ids,
                type: type
            }).then(({data}) => {
                EventHub.fire('tags-select-update', {
                    ids: ids,
                    type: type
                })

                this.ids = []
                this.multiTypeName = ''
                this.showNotif(data.msg)
            }).catch((err) => {
                this.showFormErrors(err.data.errors)
                console.error(err)
            })
        },

        // multi locale
        showName(code) {
            return this.nameLocale == code
        },
        addNewItem(code, event) {
            return this.newItem.tag[code] = event.target.value
        },

        // helpers
        selectAll() {
            // clear
            if (this.ids.length > 0) {
                return this.ids = []
            }

            // add
            this.ids = this.tags.map((e) => e.id)
        },
        getTitle(title) {
            let locale = this.selectFirst
            let v = Object.keys(title).indexOf(locale)

            return title.hasOwnProperty(locale) ? Object.values(title)[v] : ''
        },
        trans(key) {
            return this.translations[key]
        },
        showFormErrors(err) {
            Object.values(err).map((e) => {
                this.showNotif(Object.values(e)[0], 'danger')
            })
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
        updateList: debounce(function () {
            let val = this.searchFor
            let list = this.list

            if (val) {
                const locale = this.selectFirst
                let type = this.searchFieldType
                let test = type == 'name'

                return this.tags = new Fuse(list, {keys: test ? [`name.${locale}`] : ['type']}).search(val)
            }

            return this.tags = list
        }, 250)
    },
    watch: {
        searchFor(val) {
            this.updateList()
        }
    },
    render() {}
}
</script>
