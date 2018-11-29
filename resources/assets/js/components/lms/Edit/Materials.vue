<template>
    <div class="row">
        <div class="col-9">
            <div v-for="(material,index) in materials" class="card mb-4">
                <div v-if="material.material_type_id == 1" class="card-header h6">
                    <span class="fa fa-align-center mr-2"></span> Текст
                    <span class="pull-right">
                    <span @click="up(index)" style="cursor: pointer" v-if="index!=0" class="fa fa-angle-up"></span>
                    <span @click="down(index)" style="cursor: pointer" v-if="index!=(lesson.pages[selectedPage].materials.length-1)" class="fa fa-angle-down"></span>
                    <b-tooltip content="убрать блок" style="display: inline;">
                        <button type="button" class="close ml-2" @click="removeMaterial(material)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </span>
                </div>
                <div v-if="material.material_type_id == 2" class="card-header h6">
                    <span class="fa fa-video-camera mr-2"></span> Видео
                    <span class="pull-right">
                    <span @click="up(index)" style="cursor: pointer" v-if="index!=0" class="fa fa-angle-up"></span>
                    <span @click="down(index)" style="cursor: pointer" v-if="index!=(lesson.pages[selectedPage].materials.length-1)" class="fa fa-angle-down"></span>
                    <b-tooltip content="убрать блок" style="display: inline;">
                        <button type="button" class="close ml-2" @click="removeMaterial(material)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </span>
                </div>
                <div v-if="material.material_type_id == 3" class="card-header h6">
                    <span class="fa fa-headphones mr-2"></span> Аудио
                    <span class="pull-right">
                    <span @click="up(index)" style="cursor: pointer" v-if="index!=0" class="fa fa-angle-up"></span>
                    <span @click="down(index)" style="cursor: pointer" v-if="index!=(lesson.pages[selectedPage].materials.length-1)" class="fa fa-angle-down"></span>
                    <b-tooltip content="убрать блок" style="display: inline;">
                        <button type="button" class="close ml-2" @click="removeMaterial(material)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </span>
                </div>
                <div v-if="material.material_type_id == 4" class="card-header h6">
                    <span class="fa fa-sticky-note mr-2"></span> Заметка для преподавателя
                    <span class="pull-right">
                    <span @click="up(index)" style="cursor: pointer" v-if="index!=0" class="fa fa-angle-up"></span>
                    <span @click="down(index)" style="cursor: pointer" v-if="index!=(lesson.pages[selectedPage].materials.length-1)" class="fa fa-angle-down"></span>
                    <b-tooltip content="убрать блок" style="display: inline;">
                        <button type="button" class="close ml-2" @click="removeMaterial(material)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </span>
                </div>
                <div v-if="material.material_type_id == 5" class="card-header h6">
                    <span class="fa fa-check-square-o mr-2"></span> Упражнение
                    <span class="pull-right">
                    <span @click="up(index)" style="cursor: pointer" v-if="index!=0" class="fa fa-angle-up"></span>
                    <span @click="down(index)" style="cursor: pointer" v-if="index!=(lesson.pages[selectedPage].materials.length-1)" class="fa fa-angle-down"></span>
                    <b-tooltip content="убрать блок" style="display: inline;">
                        <button type="button" class="close ml-2" @click="removeMaterial(material)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </span>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.content'] }" class="card-block" v-if="material.material_type_id == 1 || material.material_type_id == 4">
                    <editor :text="material.content" @update="material.content = $event" :id="'quillContainer'+index"></editor>
                    <form-error v-if="errors && errors['materials.'+index+'.content']" :errors="errors">
                        {{ errors['materials.'+index+'.content'][0] }}
                    </form-error>
                </div>
                <div class="card-block" v-if="material.material_type_id == 2" v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.content'] }" >
                    <div class="mb-1">
                        <b-progress v-show="material.upload" :value="material.upload" show-progrss animated></b-progress>
                    </div>
                    <video v-if="material.content && !matchYoutubeUrl(material.content)" width="100%" height="auto" controls>
                        <source :src="material.content"/>
                    </video>
                    <iframe v-if="material.content && matchYoutubeUrl(material.content)" id="ytplayer" type="text/html" width="100%" height="360"
                            :src="'http://www.youtube.com/embed/'+matchYoutubeUrl(material.content)"
                            frameborder="0"></iframe>
                    <div class="form-group" enctype="multipart/form-data">
                        <input ref="fileInput" :id="index+'video'" type="file" style="display:none" accept="video/*" v-on:change="upload($event,material)"/>
                        <label class="small text-muted">Вставьте ссылку на видео или <a href="#" @click="emitClickVideo(index)" data-toggle="modal" data-target="#upload">загрузите</a></label>
                        <input v-model="material.content" class="form-control" type="text" placeholder="http://youtube.com/watch?v=RQaeZ13e12e">
                    </div>
                    <button class="btn btn-secondary btn-sm mb-3" @click="material.showAdditional=!material.showAdditional"><span class="fa fa-align-center mr-2"></span>{{ material.showAdditional ? 'удалить транскрибацию' : 'добавить транскрибацию' }}</button>
                    <textarea style="width:100%;" v-show="material.showAdditional" v-model="material.additional_content"></textarea>
                    <form-error style="display: inline-block;" v-if="errors && errors['materials.'+index+'.content']" :errors="errors">
                        {{ errors['materials.'+index+'.content'][0] }}
                    </form-error>
                </div>
                <div class="card-block" v-if="material.material_type_id == 3" v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.content'] }" >
                    <div class="mb-1">
                        <b-progress v-show="material.upload" :value="material.upload" show-progrss animated></b-progress>
                    </div>
                    <audio v-if="material.content" style="width: 100%" controls>
                        <source :src="material.content"/>
                    </audio>
                    <div class="form-group" enctype="multipart/form-data">
                        <input ref="fileInput" :id="index+'audio'" type="file" style="display:none" accept="audio/*" v-on:change="upload($event,material)"/>
                        <label class="small text-muted">Вставьте ссылку на аудио или <a href="#" @click="emitClickAudio(index)" data-toggle="modal" data-target="#upload">загрузите</a></label>
                        <input v-model="material.content" class="form-control" type="text" placeholder="http://example.com/audo.vaw">
                    </div>
                    <button class="btn btn-secondary btn-sm mb-3" @click="material.showAdditional=!material.showAdditional"><span class="fa fa-align-center mr-2"></span>{{ material.showAdditional ? 'удалить транскрибацию' : 'добавить транскрибацию' }}</button><br>
                    <textarea style="width:100%;" v-show="material.showAdditional" v-model="material.additional_content"></textarea>
                    <form-error v-if="errors && errors['materials.'+index+'.content']" :errors="errors">
                        {{ errors['materials.'+index+'.content'][0] }}
                    </form-error>
                </div>

                <!-- Упражнение -->
                <div v-if="material.material_type_id == 5">
                    <div v-if="previewMaterialIndex == index && previewMode">
                        <preview-task :task="material.task_group.tasks[0]"></preview-task>
                    </div>
                    <div v-else>
                        <div class="py-3 px-4 bg-faded mb-4 small text-muted" v-if="material.task_group.tasks[0] && material.task_group.tasks[0].task_type_id">
                            <div class="h6">{{ $common.data.task_types[material.task_group.tasks[0].task_type_id-1].name }} {{ $common.data.task_types[material.task_group.tasks[0].task_type_id-1].description }}.</div>
                            <div>{{ $common.data.task_types[material.task_group.tasks[0].task_type_id-1].help }}</div>
                            <button class="btn btn-sm btn-outline-secondary mt-1" @click="deleteTask(material,material.task_group.tasks[0])">изменить тип</button>
                        </div>
                        <div class="card-block collapse show pt-0" v-if="material.material_type_id == 5">
                            <div v-for="(task,index2) in material.task_group.tasks">
                                <div class="form-group mb-4" enctype="multipart/form-data">
                                    <label for="field5" class="small text-muted d-block" style="font-weight:500">вопрос или описание задания</label>
                                    <textarea v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.description'] }" class="d-block form-control form-control-lg p-0" style="border: none; height: 58px; margin-top: 0px; margin-bottom: 0px;" placeholder="Введите вопрос или описание задание здесь" id="field5" v-model="task.description"></textarea>
                                    <form-error style="display:inline-block;" v-if="errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.description']" :errors="errors">
                                        {{ errors['materials.'+index+'.task_group.tasks.'+index2+'.description'][0] }}
                                    </form-error>
                                    <attachments :task="task" @updateImage="task.image = $event" @updateAudio="task.audio = $event"></attachments>
                                </div>
                                <div class="form-group">
                                    <label class="small text-muted" style="font-weight: 500">
                                        {{ task.questionDesc }}
                                    </label>
                                    <div class="input-group mt-1" style="flex-wrap: wrap;" v-for="(question,index3) in task.questions" v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.questions.'+index3+'.content']}">
                                    <span class="input-group-addon" v-if="task.task_type_id == 3">
                                        <input type="checkbox" :false-value="0" :true-value="1" v-model="question.is_correct">
                                    </span>
                                        <input :id="'input'+'_'+index3+'_'+index2+'_'+index" @keyup.enter="onEnter(task,index3,index2,index)" type="text" class="form-control" :class="{'form-control-sm': task.task_type_id != 5}" v-model="question.content" autofocus>
                                        <span v-if="task.task_type_id != 5" class="input-group-btn">
                                        <button @click="deleteQuestion(task,question)" class="btn btn-secondary">×</button>
                                    </span>
                                        <form-error style="width:100%;" v-if="errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.questions.'+index3+'.content']" :errors="errors">
                                            {{ errors['materials.'+index+'.task_group.tasks.'+index2+'.questions.'+index3+'.content'][0] }}
                                        </form-error>
                                    </div>

                                    <button v-if="task.task_type_id != 5" @click="addQuestion(task)" class="btn btn-secondary btn-sm mt-1">добавить еще</button>
                                </div>
                                <div v-if="task.task_type_id == 6" class="form-group" v-bind:class="{ 'has-error': errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.words']}">
                                    <label class="small text-muted" style="font-weight:500">объём (кол-во слов)</label>
                                    <input type="number" v-model="task.words" class="form-control">
                                    <form-error class="d-block" v-if="errors && errors['materials.'+index+'.task_group.tasks.'+index2+'.words']" :errors="errors">
                                        {{ errors['materials.'+index+'.task_group.tasks.'+index2+'.words'][0] }}
                                    </form-error>
                                </div>
                                <div class="form-group" v-if="task.task_type_id == 6" >
                                    <label class="small text-muted" style="font-weight:500">система оценивания</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="eval" type="radio" v-model="task.rating" value="0">
                                            15 баллов (5/5/5)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" name="eval" type="radio" v-model="task.rating" value="1">
                                            10 баллов (3/4/3)
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-4">
                            </div>
                            <div class="row mt-4" v-if="material.task_group.tasks.length==0">
                                <div class="mb-2 col-4" style="cursor: pointer" v-for="task_type in $common.data.task_types">
                                    <div class="card px-3 py-2 bg-faded" @click="addTask(material.task_group,task_type.id)">
                            <span class="lead d-block" style="font-weight:400">
                                {{ task_type.name }}
                            </span>
                                        <span class="small text-muted">
                                {{ task_type.description }}
                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button :disabled="material.task_group.tasks.length==0" class="btn btn-sm" @click="checkTask(index)" :class="{'btn-primary': !(previewMaterialIndex == index && previewMode)}"><span class="fa fa-eye mr-1"></span> {{ (previewMaterialIndex == index && previewMode) ? "вернуться" : "предпросмотр" }}</button>
                    </div>
                </div>
                <!-- Упражнение -->

            </div>




            <div style="color: #D4D4D4;border: 4px dashed #E9E9E9;" class="text-center py-4 my-4" v-show="selectedPage === parseInt(selectedPage, 10)">
                <div class="lead text-muted mb-2">Добавить блок:</div>
                <button class="btn btn-secondary btn-sm text-left" @click="addText()">
                    <span class="fa fa-align-center mr-1"></span>
                    текст
                </button>
                <button class="btn btn-secondary btn-sm text-left" @click="addVideo()">
                    <span class="fa fa-video-camera mr-1"></span>
                    видео
                </button>
                <button class="btn btn-secondary btn-sm text-left" @click="addAudio()">
                    <span class="fa fa-headphones mr-1"></span>
                    аудио
                </button>
                <button class="btn btn-secondary btn-sm text-left" @click="addTaskGroup()">
                    <span class="fa fa-check-square-o mr-1"></span>
                    упражнение
                </button>
                <button class="btn btn-secondary btn-sm text-left" @click="addNote()">
                    <span class="fa fa-sticky-note mr-1"></span>
                    заметка для преподавателя
                </button>
            </div>


            <a href="javascript:void(0)" @click="$emit('save')"  class="rounded h2 p-3 bg-primary text-white rounded-circle" style="position:fixed;right:20px;bottom:20px;z-index:999;"><div class="fa fa-fw fa-save"></div></a>

        </div>
        <div class="col-3">
            <div class="card">
                <draggable @change="onChange" v-model="lesson.pages" :options="{draggable:'.item', handle: '.btn-drag'}" class="list-group list-group-flush" style="cursor:pointer">
                    <div v-for="(page,index) in lesson.pages" class="item" :class="pageButtonClass(index)" @click="selectPage(index)" style="cursor:pointer;display: flow-root">
                        <div class="fa fa-fw fa-circle-o text-muted mr-2"></div>
                        <span  style="display:inline-block;max-width:68%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ page.name }}</span>
                        <span class="pull-right">
                            <button type="button" class="btn btn-link ml-1 btn-sm btn-drag" style="color:#D7D7D7;cursor: move;cursor: grab;cursor: -moz-grab;cursor: -webkit-grab;">
                                <span class="fa fa-arrows"></span>
                            </button>
                            <span @click="editPage(index)" class="btn btn-sm btn-secondary" style="padding: .15rem;z-index: ">
                                <span class="fa fa-fw fa-pencil"></span>
                            </span>
                        </span>
                    </div>
                </draggable>
                <div class="card-block clearfix">
                    <button class="btn btn-secondary pull-left" @click="$refs.modalPage.show()">
                        Добавить страницу
                    </button>
                </div>
            </div>
        </div>
        <b-modal ref="modalPage" title="Добавить страницу">
            <div class="form-group">
                <label style="font-weight:500" class="d-block">Название</label>
                <input type="text" v-model="newPage" class="form-control" placeholder="укажите название страницы">
            </div>
            <div slot="modal-footer">
                <button class="btn btn-secondary" @click="$refs.modalPage.hide()">Отменить</button>
                <button class="btn btn-primary" :disabled="newPage == ''" @click="addPage()">Добавить</button>
            </div>
        </b-modal>
        <b-modal ref="modalPageEdit" title="Редактировать страницу">
            <div class="form-group">
                <label style="font-weight:500" class="d-block">Название</label>
                <input type="text" v-model="newName" class="form-control" placeholder="укажите название страницы">
            </div>
            <div slot="modal-footer">
                <div class="w-100">
                    <button class="btn btn-danger" @click="deletePage()">Удалить</button>
                    <button class="btn btn-secondary" @click="$refs.modalPageEdit.hide()">Отменить</button>
                    <button class="btn btn-primary" :disabled="newName == ''" @click="savePage()">Сохранить</button>
                </div>
            </div>
        </b-modal>
    </div>

</template>
<script>

    import { post, postFile } from '../../../helpers/api';
    export default {

        props: ['lesson','errors'],

        data() {
            return {
                allowPreview: false,
                previewMaterialIndex: '',
                previewMode: false,
                newPage: '',
                selectedPage: '',
                newName: '',
                newNameIndex: '',
            }
        },
        computed: {
            materials() {
                let array = [];
                if(this.selectedPage === parseInt(this.selectedPage, 10)) {
                    if(this.lesson.pages[this.selectedPage].materials) {
                        array = this.lesson.pages[this.selectedPage].materials.sort(function(a,b) {
                            if(a.material_order > b.material_order) return 1;
                            if(a.material_order < b.material_order) return -1;
                            return 0;
                        });
                    }
                }
                return array;
            }
        },
        components: {
            FormError : require('../../FormError.vue'),
            'preview-task': require('../../../views/lesson-templates/Content/TaskGroup/TaskPreview.vue'),
            'editor': require('../Editor.vue'),
            'attachments': require('../TaskAttachments.vue'),
        },
        mounted() {
            this.lesson.pages.length > 0 ? this.selectedPage = 0 : this.selectedPage = '';
        },
        methods: {
            addText() {
                this.lesson.pages[this.selectedPage].materials.push({id: '', material_type_id: '1', content: '', material_order: this.lastOrder()+1});
            },
            addVideo() {
                this.lesson.pages[this.selectedPage].materials.push({id: '', material_type_id: '2', content: '', additional_content: '', upload: 0, showAdditional: false, material_order: this.lastOrder()+1});
            },
            addAudio() {
                this.lesson.pages[this.selectedPage].materials.push({id: '', material_type_id: '3', content: '', additional_content: '', upload: 0, showAdditional: false, material_order: this.lastOrder()+1});
            },
            addNote() {
                this.lesson.pages[this.selectedPage].materials.push({id: '', material_type_id: '4', content: '', material_order: this.lastOrder()+1});
            },
            addTaskGroup() {
                this.lesson.pages[this.selectedPage].materials.push({id: '', material_type_id: '5', task_group: {id: '', tasks: []}, material_order: this.lastOrder()+1})
            },
            removeMaterial(material) {
                var index = this.lesson.pages[this.selectedPage].materials.indexOf(material);
                if (index > -1) this.lesson.pages[this.selectedPage].materials.splice(index, 1);
            },
            matchYoutubeUrl(url) {
                var p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                if(url.match(p)){
                    return url.match(p)[1];
                }
                return false;
            },
            upload(event,material) {
                let _this = this;
                var formData = new FormData();
                formData.append('file',event.target.files[0]);
                this.$nextTick(function () {
                    postFile(_this, '/api/upload-file', formData, function(progress) {
                        material.upload = progress;
                    }, function (response) {
                        material.upload = 0;
                        material.content = response.data
                    }, function (error) {
                        console.log(error)
                    });
                });
            },
            emitClickAudio(index) {
                $('#'+index+'audio').click();
            },
            emitClickVideo(index) {
                $('#'+index+'video').click();
            },
            addTask(task_group, type_id) {
                var description = '';
                var questionDesc = '';
                switch(type_id) {
                    case 1: description = 'Complete each sentence by drag and drop words from the list.';
                        questionDesc = 'предложения или словосочетания';
                        break;
                    case 2: description = 'Complete the sentences.';
                        questionDesc = 'предложения или словосочетания';
                        break;
                    case 3: questionDesc = 'предложения или словосочетания';
                        break;
                    case 4: description = 'Select the correct words from lists.';
                        questionDesc = 'предложения или словосочетания';
                        break;
                    case 5: description = 'Drag and drop the words to make the sentence.';
                        questionDesc = 'предложение';
                        break;
                    case 6: description = 'Write a short essay about...';
                        questionDesc = 'требования или вопросы, которые необходимо раскрыть';
                        break;
                }
                task_group.tasks.push({id: '', task_type_id: type_id, description: description, image: '', audio: '', questions: [{id:'',content:'',is_correct: 0}], words: '', rating: 0, questionDesc: questionDesc, uploadAudio: 0, uploadImage: 0});
            },
            deleteTask(material,task) {
                var index = material.task_group.tasks.indexOf(task);
                if(index > -1) material.task_group.tasks.splice(index,1);
            },
            addQuestion(task) {
                task.questions.push({id: '', content: '', is_correct: 0});
            },
            onEnter(task,index) {
                if(task.task_type_id != 5) {
                    task.questions.push({id: '', content: '', is_correct: 0});
                }
            },
            deleteQuestion(task,question) {
                var index = task.questions.indexOf(question);
                if(index > -1) task.questions.splice(index,1);
            },
            checkTask(index) {
                // Нужен рефакторинг. Отправлять только упражнение
                let _this = this;
                this.allowPreview = false;
                post(_this,'/api/lesson-template-item-check-task/'+this.selectedPage+'/'+index, this.lesson, function (response) {
                    _this.previewMaterialIndex = index;
                    _this.allowPreview = true;
                    _this.previewMode = !_this.previewMode;
                    _this.errors = '';
                }, function (error) {
                    _this.errors = error.response.data;
                });
            },
            up(index) {
                let material = this.lesson.pages[this.selectedPage].materials[index];
                let previous = this.lesson.pages[this.selectedPage].materials[index-1];
                material.material_order = previous.material_order-1;
                Vue.set(this.lesson.pages[this.selectedPage].materials,index,material);
            },
            down(index) {
                let material = this.lesson.pages[this.selectedPage].materials[index];
                let next = this.lesson.pages[this.selectedPage].materials[index+1];
                material.material_order = next.material_order+1;
                Vue.set(this.lesson.pages[this.selectedPage].materials.materials,index,material);
            },
            updateText(parentData, value) {
                parentData = value;
            },

            onEnter(task,index,temp,temp2) {
                if(task.task_type_id != 5) {
                    task.questions.push({id: '', content: '', is_correct: 0});
                    this.$nextTick(function() {
                        let i = task.questions.length - 1;
                        $('#input'+'_'+i+"_"+temp+'_'+temp2).focus();
                    });
                }
            },
            addPage() {
                this.lesson.pages.push({id: '', name: this.newPage, materials: [], sort: this.lastPageOrder()+1 });
                this.newPage = '';
                this.selectedPage = this.lesson.pages.length-1;
                this.$refs.modalPage.hide();
            },
            pageButtonClass(value) {
                return this.selectedPage === value ? "list-group-item list-group-item-info list-group-item-action" : "list-group-item list-group-item-action";
            },
            lastOrder() {
                let value = 0;
                if(this.selectedPage === parseInt(this.selectedPage, 10)) {
                    if(this.lesson.pages[this.selectedPage].materials.length > 0) {
                        value = this.lesson.pages[this.selectedPage].materials[this.lesson.pages[this.selectedPage].materials.length-1].material_order;
                    }
                }
                return value;
            },
            editPage(index) {
                let _this = this;
                this.newName = this.lesson.pages[index].name;
                this.newNameIndex = index;
                this.$nextTick(function() {
                    _this.$refs.modalPageEdit.show();
                });
            },
            deletePage() {
                if(this.lesson.pages.length>1) {
                    this.selectedPage--;
                } else {
                    this.selectedPage = '';
                }
                this.lesson.pages.splice(this.newNameIndex, 1);
                this.$nextTick(function() {
                    this.$refs.modalPageEdit.hide();
                });
            },
            savePage() {
                this.lesson.pages[this.newNameIndex].name = this.newName;
                this.$nextTick(function() {
                    this.$refs.modalPageEdit.hide();
                });
            },
            lastPageOrder() {
                let value = 0;
                this.lesson.pages.forEach(function(page) {
                    if(page.sort > value) value = page.sort;
                });
                return value;
            },
            onChange(obj) {
                let _this = this;
                let i = 0;
                if(obj && obj.moved) {
                    this.lesson.pages.forEach(function(page) {
                        page.sort = i;
                        i++;
                    });
                }
            },
            selectPage(value) {
                event.stopPropagation();
                this.$nextTick(function() {
                    this.selectedPage = value;
                });
            }

        },
    }
</script>
