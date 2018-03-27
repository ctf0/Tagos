<script>
import Search from './mixins/search'

export default {
    mixins: [Search],
    props: ['namesList'],
    data() {
        return {
            names: this.namesList
        }
    },
    methods: {
        inSearchList(item) {
            return this.names.includes(item)
        }
    },
    watch: {
        searchFor(val) {
            if (val) {
                return this.names = this.namesList.filter((e) => {
                    return e.includes(val)
                })
            }

            return this.names = this.namesList
        }
    },
    render() {}
}
</script>