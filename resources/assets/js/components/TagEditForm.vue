<script>
    import axios from 'axios';
    import { get } from 'lodash/object';

    export default {
        data: function() {
            return {
                entity_id: '',
                entity_label: '',
                entity_type: '',
            }
        },
        props: {
            entityData: {
                type: Object,
                default: {},
            }
        },
        mounted() {
            this.entity_id = this.entityData.id;
            this.entity_label = this.entityData.entity_label;
            this.entity_type = this.entityData.entity_type;
        },
        methods: {
        }
    }
</script>
<template>

    <h2>
        <span>{{ entity_label }}</span>
    </h2>


</template>
<style lang="sass">
</style>
