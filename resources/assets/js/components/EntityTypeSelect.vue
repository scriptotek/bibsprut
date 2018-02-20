<template>
    <div>
        <v-select
            @input="onChange"
            :options='values'
            :value="value">
        </v-select>
        <input type="hidden" :name="name" :value="intValue">
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                intValue: String
            }
        },
        props: {
            name: {
                type: String,
                default: ''
            },
            values: {
                type: Array,
                default: [],
            },
            value: {
                type: String,
                default: '',
            },
        },
        methods: {
            onChange(newValue) {
                this.intValue = newValue
            }
        },
        mounted() {
            // if (this.tagData) {
            //     this.entities = JSON.parse(this.tagData);
            //     console.log(this.entities);
            // }
        }
    }
</script>
