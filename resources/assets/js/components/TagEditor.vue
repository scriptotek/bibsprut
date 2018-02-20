<script>
    import axios from 'axios';
    import { get } from 'lodash/object';

    export default {
        data: function() {
            return {
                saveSuccessful: false,
                errors: [],
                tags: [],
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
            tagData: {
                type: String,
                default: '',
            }
        },
        mounted() {
            if (this.tagData) {
                this.tags = JSON.parse(this.tagData);
                console.log(this.tags);
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
                this.tags.push({
                    tag_name: '',
                    tag_id: '_new',
                })
            },
            setProperty (idx, newValue) {
                console.log('set prop', idx, newValue.value)
                let tag = this.tags[idx];
                tag.tag_role_id = newValue.value;
                this.$set(this.tags, idx, tag);
            },
            setValue (idx, newValue) {
                console.log('set val', idx, newValue.tag_name)
                let tag = this.tags[idx];
                tag.id = newValue.id;
                tag.tag_name = newValue.tag_name;
                this.$set(this.tags, idx, newValue)
            },
            save () {
                this.errors = [];
                this.saveSuccessful = false;
                axios.put(`/videos/${this.videoId}/updateTags`, {
                    youtube_id: this.videoId,
                    tags: this.tags,
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
            <li v-for="(tag, idx) in tags">
                <tag-statement
                    :properties="properties"
                    :value="tag"
                    @update:property="val => setProperty(idx, val)"
                    @update:value="val => setValue(idx, val)">
                </tag-statement>
            </li>
        </ul>
        <ul>
            <li v-for="error in errors" class="text-danger">{{ error }}</li>
        </ul>
        <div>
            <button class="btn btn-success" @click="addTag">Add tag</button>
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
