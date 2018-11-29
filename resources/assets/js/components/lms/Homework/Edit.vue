<template>
    <div>
        <div class="w-75 m-auto">
            <div class="card mb-5" v-for="(task_group,index) in test">
                <div class="card-header">
                    <span style="font-weight:500">Раздел:</span>
                    <b-dropdown @click="" size="sm" class="d-inline-block ml-1">
                        <template slot="text">
                            {{ getSectionName(task_group.section_id) }}
                            <span class="fa fa-angle-down ml-1"></span>
                        </template>
                        <b-dropdown-item v-for="section in $common.data.sections" :key="section.id" @click="task_group.section_id=section.id">{{ section.name }}</b-dropdown-item>
                    </b-dropdown>
                    <b-tooltip content="убрать раздел" style="display: inline;">
                        <button type="button" class="close pull-right" @click="removeTaskGroup(task_group)" style="font-size:1rem;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </b-tooltip>
                </div>
                <div class="card-block">
                    <div v-for="(task,index2) in task_group.tasks">
                        <div class="mb-2 clearfix">
                            <small class="badge badge-default" style="font-weight:500">Задание #{{ index+1 }}</small>
                            <div class="pull-right">
                                <a href="javascript:void(0);" class="badge badge-danger" style="font-weight:500" @click="deleteTask(task_group,task)">
                                    Удалить
                                </a>
                            </div>
                        </div>
                        <div class="p-2 bg-faded rounded mb-4 small text-muted">
                            Тип задания: {{ $common.data.task_types[task.task_type_id-1].help }}
                        </div>
                        <div class="form-group mb-4" enctype="multipart/form-data">
                            <label for="field5" class="small text-muted d-block" style="font-weight:500">вопрос или описание задания</label>

                            <textarea class="d-block form-control form-control-lg p-0" style="border: none; height: 58px; margin-top: 0px; margin-bottom: 0px;" placeholder="Введите вопрос или описание задание здесь" id="field5" v-model="task.description"></textarea>
                            <form-error style="display:inline-block;" v-if="errors && errors['homework.'+index+'.tasks.'+index2+'.description']" :errors="errors">
                                {{ errors['homework.'+index+'.tasks.'+index2+'.description'][0] }}
                            </form-error>
                            <attachments :task="task" @updateImage="task.image = $event" @updateAudio="task.audio = $event"></attachments>
                        </div>
                        <div class="form-group">
                            <label class="small text-muted" style="font-weight: 500">
                                {{ task.questionDesc }}
                            </label>
                            <div class="input-group mt-1"  style="flex-wrap: wrap;" v-for="(question,index3) in task.questions">
                                    <span class="input-group-addon" v-if="task.task_type_id == 3">
                                        <input type="checkbox" :false-value="0" :true-value="1" v-model="question.is_correct">
                                    </span>
                                <input :id="'input'+'_'+index3+'_'+index2+'_'+index" @keyup.enter="onEnter(task,index3)" type="text" class="form-control" :class="{'form-control-sm': task.task_type_id != 5}" v-model="question.content">
                                <span v-if="task.task_type_id != 5" class="input-group-btn">
                                        <button @click="deleteQuestion(task,question)" class="btn btn-secondary">×</button>
                                    </span>
                                <form-error style="width:100%;" v-if="errors && errors['homework.'+index+'.tasks.'+index2+'.questions.'+index3+'.content']" :errors="errors">
                                    {{ errors['homework.'+index+'.tasks.'+index2+'.questions.'+index3+'.content'][0] }}
                                </form-error>
                            </div>
                            <button v-if="task.task_type_id != 5" @click="addQuestion(task)" class="btn btn-secondary btn-sm mt-1">добавить еще</button>
                        </div>
                        <div v-if="task.task_type_id == 6" class="form-group">
                            <label class="small text-muted" style="font-weight:500">объём (кол-во слов)</label>
                            <input type="number" v-model="task.words" class="form-control">
                            <form-error class="d-block" v-if="errors && errors['homework.'+index+'.tasks.'+index2+'.words']" :errors="errors">
                                {{ errors['homework.'+index+'.tasks.'+index2+'.words'][0] }}
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
                    <div class="mb-2">
                        <small class="badge badge-default" style="font-weight:500">Новое задание</small>
                    </div>
                    <div class="row">
                        <div class="mb-2 col-4" style="cursor: pointer" v-for="task_type in $common.data.task_types">
                            <div class="card px-3 py-2 bg-faded" @click="addTask(task_group,task_type.id)">
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
            <a href="javascript:void(0)" class="btn btn-primary btn-block mt-4" @click="addTaskGroup">добавить раздел</a>
        </div>
        <a href="javascript:void(0)" @click="$emit('save')"  class="rounded h2 p-3 bg-primary text-white rounded-circle" style="position:fixed;right:20px;bottom:20px;"><div class="fa fa-fw fa-save"></div></a>
    </div>
</template>
<script>

    export default {

        props: ['test','errors'],

        data() {
            return {
            }
        },
        components: {
            FormError : require('../../FormError.vue'),
            'attachments': require('../TaskAttachments.vue'),
        },
        methods: {
            addTaskGroup() {
                this.test.push({id: '', tasks: [], section_id: 1});
            },
            getSectionName(id) {
                var name = '';
                if(this.$common.data.sections) {
                    this.$common.data.sections.forEach(function(section) {
                        if(id==section.id) name = section.name;
                    });
                }
                return name;
            },
            removeTaskGroup(task_group) {
                var index = this.test.indexOf(task_group);
                if (index > -1) this.test.splice(index, 1);
            },
            deleteTask(task_group,task) {
                var index = task_group.tasks.indexOf(task);
                if(index > -1) task_group.tasks.splice(index,1);
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
                task_group.tasks.push({id: '', task_type_id: type_id, description: description, image: '', audio: '', questions: [{id:'',content:'',is_correct: 0}], words: '', rating: 0, questionDesc: questionDesc, uploadImage: 0, uploadAudio: 0});
            },
            addQuestion(task) {
                task.questions.push({id: '', content: '', is_correct: 0});
            },
            deleteQuestion(task,question) {
                var index = task.questions.indexOf(question);
                if(index > -1) task.questions.splice(index,1);
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
        }
    }
</script>