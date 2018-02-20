<script>
    import axios from 'axios';
    import { get } from 'lodash/object';

    export default {
        data: function() {
            return {
                tag_id: '',
                tag_name: '',
                tag_type: '',
            }
        },
        props: {
            tagData: {
                type: Object,
                default: {},
            }
        },
        mounted() {
            this.tag_id = this.tagData.id;
            this.tag_name = this.tagData.tag_name;
            this.tag_type = this.tagData.tag_type;
        },
        methods: {
        }
    }
</script>
<template>

    <h2>
        <span>{{ tag_name }}</span>
    </h2>


</template>
<style lang="sass">
</style>
