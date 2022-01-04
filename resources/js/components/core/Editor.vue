<template>
    <div class="blog-post-editor">
        <quill-editor v-model="content"
            ref="myQuillEditor"
            :options="editorOptions">
        </quill-editor>
    </div>
</template>

<script>

    import { quillEditor } from 'vue-quill-editor'

    export default {
        name: "Editor",
        props: ['body', 'editorOptions'],
        components: {
            quillEditor
        },
        data() {
            return {
                content: this.body,
                editorOption: this.options,
                delta: undefined
            }
        },
        watch: {
            content(value) {
                let delta = this.$refs.myQuillEditor.quill

                if(value !== '') {
                    this.$emit('delta', delta)
                }else{
                    this.$emit('delta', '')
                }
            },
            body() {
                if(this.body == '') {
                    this.content = this.body
                }
            },
            options() {
                if(typeof this.editorOptions !== 'undefined') {
                    this.editorOption = this.editorOptions;
                } else {
                    this.editorOption = {
                        placeholder: 'Type your post',
                        readOnly: true,
                        theme: 'snow'
                    };
                }
            }
        }
    }
</script>

<style>
</style>
