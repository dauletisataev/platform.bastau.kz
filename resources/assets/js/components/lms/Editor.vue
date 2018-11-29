<template>
    <div class="row">
        <div class="col-1 pr-0">
            <b-tooltip title="Прикрепить файл или изображение">
                <span @click="test" class="pull-right" style="margin-top: 11px; cursor: pointer;"><span class="fa fa-paperclip"></span></span>
            </b-tooltip>
        </div>
        <div ref="editor" class="col-11 pl-0">
            <vue-editor :id="editorId" :editorOptions="$lms.quillConfig()"  :editorToolbar="$lms.quillToolbar()" v-model="html" useCustomImageHandler  @imageAdded="uploadImage"></vue-editor>
        </div>
    </div>
</template>
<script>
    import { VueEditor } from 'vue2-editor';
    import { post } from '../../helpers/api';
    import ImageResize from 'quill-image-resize-module';
    Quill.register('modules/imageResize', ImageResize);

    export default {

        props: ['text','id'],

        data() {
            return {
                html: this.text,
                editorId: this.id,
            }
        },
        watch: {
            html(value) {
                this.$emit('update',value);
            },
            text(text) {
                this.html = text;
            }
        },
        components: {
            VueEditor
        },
        methods: {
            uploadImage(file, Editor, cursorLocation, resetUploader) {
                let _this = this;
                let url = "";
                var formData = new FormData();
                let check = "img";
                let str = "";
                let fileName = file.name;
                formData.append('file', file);
                this.$nextTick(function () {
                    post(_this, '/api/upload-file', formData, function (response) {
                        url = response.data;
                        str = url.match(/\.[0-9a-z]+$/i);
                        switch(str[0]) {
                            case ".jpg":
                            case ".jpeg":
                            case ".gif":
                            case ".png":
                            case ".apng":
                            case ".svg":
                            case ".bmp":
                            case ".ico":
                                check = "img";
                                break;
                            default:
                                check = "file";
                        }
                        if(check === "img") {
                            Editor.insertEmbed(cursorLocation, 'image', url);
                        } else {
                            let html = "<a href='" + url + "'>" + fileName + "</a>";
                            Editor.pasteHTML(cursorLocation, html);
                        }
                        resetUploader();
                    });
                });

            },
            test() {
                this.$refs.editor.firstChild.firstChild.childNodes[2].lastChild.childNodes[2].firstChild.click();
            },
        }
    }
</script>