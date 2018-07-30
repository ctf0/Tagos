<script>
import Search from './mixins/search'
import debounce from 'lodash/debounce'

export default {
    mixins: [Search],
    props: ['namesList'],
    data() {
        return {
            names: this.namesList
        }
    },
    computed: {
        fuseLib() {
            let list = this.namesList.map((e, i) => {
                return {
                    id: i,
                    name: e
                }
            })

            return new Fuse(list, {keys: ['name']})
        }
    },
    methods: {
        inSearchList(item) {
            return this.names.includes(item)
        },
        updateList: debounce(function () {
            let val = this.searchFor

            if (val) {
                return this.names = this.fuseLib.search(val).map((e) => e.name)
            }

            return this.names = this.namesList
        }, 250)
    },
    watch: {
        searchFor() {
            this.updateList()
        }
    },
    render() {}
}
</script>
