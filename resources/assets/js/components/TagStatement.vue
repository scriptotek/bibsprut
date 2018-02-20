<template>
    <div class="stmt" style="display:flex">
        <div class="stmt-property" style="flex:0 0 250px;">
            <v-select
                :options="propertyOptions"
                :value="selectedProperty"
                @input="onChangeProperty"></v-select>
        </div>
        <div class="stmt-value" style="flex:1 1 auto">
            <v-select
                taggable
                label="tag_name"
                :filterable="false"
                :options="valueOptions"
                :value="selectedValue"
                @input="onChangeValue"
                @search="onSearch">
                <template slot="no-options">
                    type to search...
                </template>
                <template slot="option" slot-scope="tag">
                    <div>
                        <span v-if="tag.id">
                            <span style="float:right; opacity: 0.5;">#{{ tag.id }}</span>
                            {{ tag.tag_name }}
                        </span>
                        <span v-else>
                            <em>+ Opprett «{{ tag.tag_name }}»</em>
                        </span>
                    </div>
                </template>
                <template slot="selected-option" slot-scope="tag">
                    <div class="selected">
                        <span v-if="tag.id">
                            {{ tag.tag_name }}
                        </span>
                        <span v-else>
                            <em>{{ tag.tag_name }}</em>
                        </span>
                    </div>
                </template>
              </v-select>
        </div>
    </div>
</template>

<script>

    import { debounce } from 'lodash/function';
    import { get } from 'lodash/object';

    export default {
        data: function() {
            return {
                selectedProperty: null,
                selectedValue: null,
                propertyOptions: [],
                valueOptions: [],
                editMode: false,
            }
        },
        props: {
            properties: {
                type: Array,
            },
            value: {
                type: Object,
            },
        },
        mounted() {
            this.propertyOptions = this.properties;
            this.propertyOptions.forEach((x) => {
                if (x.value == this.value.tag_role_id) {
                    this.selectedProperty = x;
                }
            });
            this.valueOptions = [this.value];
            this.selectedValue = this.value;
        },
        methods: {
            onChangeProperty(newValue) {
                if (newValue != this.selectedProperty) {
                    console.log('emit update:property', newValue, this.selectedProperty)
                    this.selectedProperty = newValue
                    this.$emit('update:property', this.selectedProperty)
                    // this.selectedValue = null;
                }
            },
            onChangeValue(newValue) {
                if (newValue != this.selectedValue) {
                    console.log('emit update:value', this.selectedValue)
                    this.selectedValue = newValue
                    this.$emit('update:value', this.selectedValue)
                }
            },
            onSearch(search, loading) {
                loading(true);
                this.search(loading, search, this);
            },
            search: debounce((loading, search, vm) => {
                fetch(
                    `/entities.json?q=${escape(search)}`
                ).then(res => {
                    res.json().then(data => (vm.valueOptions = data));
                    loading(false);
                });
            }, 350)
        }
    }
</script>
<style lang="scss">

</style>
