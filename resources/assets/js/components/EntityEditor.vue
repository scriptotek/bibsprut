<script>
    import axios from 'axios';
    import { get } from 'lodash/object';

    export default {
        data: function() {
            return {
                saveSuccessful: false,
                errors: [],
                entities: [],
                properties: [],
            }
        },
        props: {
            videoId: {
                type: String,
                default: ''
            },
            propertyData: {
                type: String,
                default: '',
            },
            entityData: {
                type: String,
                default: '',
            }
        },
        mounted() {
            if (this.entityData) {
                this.entities = JSON.parse(this.entityData);
                console.log(this.entities);
            }
            if (this.propertyData) {
                this.properties = JSON.parse(this.propertyData).map((x) => {
                return {
                    label: x.label,
                    value: x.id,
                };
            });
            }
        },
        methods: {
            addTag () {
                this.entities.push({
                    id: null,
                    entity_label: '',
                })
            },
            setProperty (idx, newValue) {
                console.log('set prop', idx, newValue.value)
                let entity = this.entities[idx];
                entity.entity_relation_id = newValue.value;
                this.$set(this.entities, idx, entity);
            },
            setValue (idx, newValue) {
                console.log('set val', idx, newValue.entity_label)
                let entity = this.entities[idx];
                entity.id = newValue.id;
                entity.entity_label = newValue.entity_label;
                this.$set(this.entities, idx, newValue)
            },
            save () {
                this.errors = [];
                this.saveSuccessful = false;
                axios.put(`/videos/${this.videoId}/updateEntities`, {
                    youtube_id: this.videoId,
                    entities: this.entities,
                })
                .then(response => {
                    if (response.data.status == 'ok') {
                        this.saveSuccessful = true;
                    }
                    // JSON responses are automatically parsed.
                    console.log(response.data)
                })
                .catch(e => {
                    console.log(e.response.data)
                    this.errors.push('An error occured, see console for details:' + get(e.response, 'data.message'))
                })
            },
        }
    }
</script>
<template>
    <div>
        <ul>
            <li v-for="(entity, idx) in entities">
                <statement
                    :properties="properties"
                    :value="entity"
                    @update:property="val => setProperty(idx, val)"
                    @update:value="val => setValue(idx, val)">
                </statement>
            </li>
        </ul>
        <ul>
            <li v-for="error in errors" class="text-danger">{{ error }}</li>
        </ul>
        <div>
            <button class="btn btn-success" @click="addTag">Add entity</button>
            <button class="btn btn-primary" @click="save">Save</button>
            <span v-if="saveSuccessful" class="text-success" style="padding-left: 1em;">
                <i class="fa fa-check"></i>
                Saved!
            </span>
        </div>
    </div>
</template>
<style lang="sass">
</style>
