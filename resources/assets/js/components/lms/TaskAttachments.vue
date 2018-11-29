<template>
    <div>
        <div class="mb-1" v-show="task.uploadImage" >
            <b-progress :value="task.uploadImage" show-progrss animated></b-progress>
        </div>
        <div class="mb-1" v-show="task.uploadAudio">
            <b-progress :value="task.uploadAudio" show-progrss animated></b-progress>
        </div>
        <img v-if="task.image" :src="task.image" class="img-fluid">
        <audio v-if="task.audio" style="width: 100%" controls>
            <source :src="task.audio"/>
        </audio>
        <input ref="taskImage" type="file" style="display:none" accept="image/*" v-on:change="uploadTaskImage($event)"/>
        <button v-if="!task.image" @click="$refs.taskImage.click()" class="btn btn-sm btn-secondary mt-2">
            <span class="fa fa-picture-o mr-1"></span>
            прикрепить изображение
        </button>
        <button class="btn btn-sm btn-secondary mt-2" @click="removeTaskImage" v-else>
            <span class="fa fa-picture-o mr-1"></span>
            убрать изображение
        </button>
        <input ref="taskAudio" type="file" style="display:none" accept="audio/*" v-on:change="uploadTaskAudio($event)"/>
        <button v-if="!task.audio" @click="$refs.taskAudio.click()" class="btn btn-sm btn-secondary mt-2">
            <span class="fa fa-music mr-1"></span>
            прикрепить аудио
        </button>
        <button class="btn btn-sm btn-secondary mt-2" @click="removeTaskAudio" v-else>
            <span class="fa fa-music mr-1"></span>
            убрать аудио
        </button>
    </div>
</template>
<script>
    import { postFile } from '../../helpers/api';
    export default {
        props: ['task'],
        data() {
            return {
            }
        },
        methods: {
            uploadTaskImage(event) {
                let _this = this;
                var formData = new FormData();
                formData.append('file',event.target.files[0]);
                this.$nextTick(function () {
                    postFile(_this, '/api/upload-file', formData, function(progress) {
                        _this.task.uploadImage = progress;
                    }, function (response) {
                        _this.task.uploadImage = 0;
                        _this.task.image = response.data;
                        _this.$emit('updateImage',response.data);
                    });
                });
            },
            uploadTaskAudio(event) {
                let _this = this;
                var formData = new FormData();
                formData.append('file',event.target.files[0]);
                this.$nextTick(function () {
                    postFile(_this, '/api/upload-file', formData, function(progress) {
                        _this.task.uploadAudio = progress;
                    }, function (response) {
                        _this.task.uploadAudio = 0;
                        _this.task.audio = response.data;
                        _this.$emit('updateAudio',response.data);
                    });
                });
            },
            removeTaskImage() {
                this.task.image = '';
                this.$emit('updateImage','');
            },
            removeTaskAudio() {
                this.task.audio = '';
                this.$emit('updateAudio','');
            },
        }
    }
</script>