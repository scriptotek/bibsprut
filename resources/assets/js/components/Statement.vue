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
                label="entity_label"
                :filterable="false"
                :options="valueOptions"
                :value="selectedValue"
                @input="onChangeValue"
                @search="onSearch">
                <template slot="no-options">
                    type to search...
                </template>
                <template slot="option" slot-scope="entity">
                    <div>
                        <span v-if="entity.id">
                            <span style="float:right; opacity: 0.5;">#{{ entity.id }}</span>
                            {{ entity.entity_label }}
                        </span>
                        <span v-else>
                            <em>+ Opprett «{{ entity.entity_label }}»</em>
                        </span>
                    </div>
                </template>
                <template slot="selected-option" slot-scope="entity">
                    <div class="selected">
                        <span v-if="entity.id">
                            {{ entity.entity_label }}
                        </span>
                        <span v-else>
                            <em>{{ entity.entity_label }}</em>
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
                if (x.value == this.value.entity_relation_id) {
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
