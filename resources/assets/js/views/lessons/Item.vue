<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2" v-if="lesson">
            <div class="bg-primary text-white rounded p-4 mb-4">
                <div class="small text-info mb-2">
                    <router-link :to="{ name: 'group', params: { id: lesson.group.id }}" class="text-info"><span class="fa fa-angle-left mr-2"></span>Вернуться в группу</router-link>
                </div>
                <div class="display-4 mb-2" style="font-size:2.5rem;">
                    {{ lesson.title }}
                </div>
                <div class="small text-info">
                    <span class="mr-4"><span class="fa fa-signal mr-1"></span>{{ lesson.level ? lesson.level.name : lesson.group.level.name }}</span>
                    <span v-if="lesson.passed==1">
                    <span class="mr-4"><span class="fa fa-list-ol mr-1"></span>№{{ lesson.number }}</span>
                    <span class="mr-4" v-if="lesson.mark"><span class="fa fa-meh-o mr-1"></span>{{ lesson.mark }}%</span>
                    <span class="mr-4" v-else="!lesson.mark"><span class="fa fa-meh-o mr-1"></span>Нет оценок</span>
                    </span>
                    <span class="mr-4"><span class="fa fa-calendar-o mr-1"></span> {{ lesson.date ? lesson.date : "Не установлено" }} </span>
                    <span class="mr-4"><span class="fa fa-clock-o mr-1"></span> {{ lesson.duration ? lesson.duration : "Не установлена" }} </span>
                    <router-link  v-if="!lesson.is_started" :to="{name:'lesson', params: {id: lesson.id, group_id: lesson.group.id, toContent: true, fromContent: true}}" tag="a" class="mr-4"><span class="text-info" style="text-decoration: underline;"><span class="fa fa-pencil mr-2"></span>Изменить параметры</span></router-link>
                    <span class="mr-4" v-if="!lesson.is_started"><a href="#" @click="deleteLesson()" class="text-info" style="text-decoration: underline;"><span class="fa fa-trash mr-2"></span>Удалить занятие</a></span>
                </div>
            </div>
            <div v-if="lesson.unmarked_tasks && lesson.unmarked_tasks.length>0 && !$user.isManager()" class="alert alert-danger clearfix">
                <div class="pull-left" style="font-weight:500">В занятии есть непроверенные задания студентов, которые вам необходимо проверить вручную</div>
                <button class="btn btn-sm btn-danger pull-right" data-toggle="modal" @click="unmarked()" data-target="#checktasks">проверить</button>
            </div>
            <div class="clearfix mb-5">
                <div class="pull-left">
                    <div class="btn-group">
                        <button :disabled="edit" @click="activeTab='materials'" class="btn btn-secondary border-right-0" :class="{'active': activeTab=='materials' || activeTab=='task_group' }" >
                            Материал
                        </button>
                        <b-tooltip style="display: inline;" content="Изменить материал" v-if="!lesson.is_started">
                            <button :disabled="(edit && activeTab!='materials-edit')" @click="activeTab='materials-edit'" class="btn px-2 btn-secondary border-left-0 border-right-0" :class="{'active': activeTab=='materials-edit'}" style="border-radius: 0;">
                                <span class="fa fa-pencil"></span>
                            </button>
                        </b-tooltip>
                        <button :disabled="edit" @click="activeTab='manual'" class="btn btn-secondary border-right-0" :class="{'active': activeTab=='manual'}">
                            Методичка
                        </button>
                        <b-tooltip style="display: inline;" content="Изменить методичку" v-if="!lesson.is_started">
                            <button :disabled="(edit && activeTab!='manual-edit')" @click="activeTab='manual-edit'" class="btn px-2 btn-secondary border-left-0 border-right-0" :class="{'active': activeTab=='manual-edit'}" style="border-radius: 0;">
                                <span class="fa fa-pencil"></span>
                            </button>
                        </b-tooltip>
                        <button :disabled="edit" @click="activeTab='tests'" class="btn btn-secondary border-right-0" :class="{'active': activeTab=='tests' || activeTab=='tests-new' || activeTab=='tests-item' || activeTab=='tests-edit'}">
                            Тесты <span class="badge badge-pill badge-default ml-1">{{ lesson.tests.length }}</span>
                        </button>
                        <button :disabled="edit || (lesson.is_started==1 && lesson.homework.length==0)" @click="activeTab='homework'" class="btn btn-secondary" :class="{'active': activeTab=='homework'}">
                            Задание
                        </button>
                        <button @click="activeTab='about'" class="btn btn-secondary" :class="{'active': activeTab=='about'}">
                            О занятии
                        </button>
                    </div>
                </div>
                <div v-if="edit==true" class="pull-right">
                    <span @click="cancel" class="btn btn-outline-secondary">
                        Отменить изменения
                    </span>
                    <span @click="save" class="btn btn-primary">
                        Сохранить
                    </span>
                </div>
                <div v-if="lesson.is_started && lesson.passed != 1" class="pull-right">
                    <a href="javascript:void(0)" class="btn btn-success" style="font-weight:500" @click="activeTab='mark'">
                        <span class="fa fa-check mr-1"></span>
                        Завершить занятие
                    </a>
                </div>
                <div v-if="!lesson.is_started && !edit" class="pull-right">
                    <b-button :disabled="lesson.without_date==1 || markPossible(lesson)" :variant="'primary'" @click="startLesson" style="font-weight:500">
                        <span class="fa fa-play mr-1"></span>
                        Начать занятие
                    </b-button>
                </div>
            </div>
            <materials v-if="activeTab=='materials'" :lesson="lesson" @task="task" @student="taskStudent" :students="lesson.group.students" v-on:update="getItem()"></materials>
            <materials-edit :errors="errors" v-if="activeTab=='materials-edit'" :lesson="lesson" v-on:cancel="cancel" v-on:save="save"></materials-edit>
            <manual v-if="activeTab=='manual'" :manual="lesson.manual"></manual>
            <manual-edit v-if="activeTab=='manual-edit'" :lesson="lesson" v-on:cancel="cancel" v-on:save="save"></manual-edit>
            <mark-lesson v-if="activeTab=='mark'" :lesson="lesson" @toGroup="toGroup"></mark-lesson>
            <tests v-if="activeTab=='tests'" :lesson="lesson" @testEdit="testEdit" @newTest="newTest()" @testItem="testItem" @update="getItem()"></tests>
            <tests-new :errors="errors" :index="lesson.tests.length-1" v-if="activeTab=='tests-new'" :test="lesson.tests[lesson.tests.length-1]" @cancel="cancel" @save="save"></tests-new>
            <tests-item v-if="activeTab=='tests-item'" :students="lesson.group.students" :test="lesson.tests[selectedTestIndex]" @testEdit="testEdit" @update="getItem()"></tests-item>
            <tests-edit :errors="errors" v-if="activeTab=='tests-edit'" :index="selectedTestIndex" :test="lesson.tests[selectedTestIndex]" @cancel="cancel" @save="save"></tests-edit>
            <homework :is_started="lesson.is_started" v-if="activeTab=='homework'" :test="lesson.homework" @cancel="cancel" @save="save" @homeworkEdit="activeTab='homework-edit'" :students="lesson.group.students"></homework>
            <homework-edit :errors="errors" v-if="activeTab=='homework-edit'" :test="lesson.homework" v-on:cancel="cancel" v-on:save="save"></homework-edit>
            <b-modal ref="modalDeleteLesson" title="Подтвердите удаление">
                Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
                <div slot="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$refs.modalDeleteLesson.hide()">Отменить</button>
                    <button type="button" class="btn btn-danger" @click="removeLesson()">Удалить</button>
                </div>
            </b-modal>
            <unmarked-tasks v-if="lesson.unmarked_tasks && lesson.unmarked_tasks.length>0" ref="unmarked" :tasks="lesson.unmarked_tasks" :group_id="lesson.group_id" @reload="getItem()"></unmarked-tasks>
            <about v-if="activeTab=='about'" :lesson="lesson"></about>
            <task_group ref="taskGroup" v-if="activeTab=='task_group'" :task_group="selectedTaskGroup" :students="lesson.group.students"></task_group>
        </div>
    </div>
</template>
<script>

    import { get, post, del } from '../../helpers/api';
    import wysiwyg from "vue-wysiwyg";
    import moment from 'moment';
    export default {

        props: ['id'],

        data () {
            return {
                loading: true,
                lesson: '',
                activeTab: 'materials',
                edit: false,
                errors: [],
                selectedTestIndex: '',
            }
        },
        watch: {
            activeTab(value) {
                if(value=='manual-edit' || value=='materials-edit' || value=='tests-new' || value=='tests-edit' || value=='homework-edit') {
                    this.edit=true;
                } else {
                    this.edit=false;
                }

            }
        },
        components: {
            'materials': require('./Materials.vue'),
            'manual': require('../../components/lms/Manual.vue'),
            'manual-edit': require('../../components/lms/Edit/Manual.vue'),
            'materials-edit': require('../../components/lms/Edit/Materials.vue'),
            'mark-lesson': require('./Mark.vue'),
            'tests': require('./Tests/List.vue'),
            'tests-new': require('../../components/lms/Tests/New.vue'),
            'tests-item': require('./Tests/Item.vue'),
            'tests-edit': require('../../components/lms/Tests/Edit.vue'),
            'homework': require('./Homework/Item.vue'),
            'homework-edit': require('../../components/lms/Homework/Edit.vue'),
            'unmarked-tasks': require('./../groups/modals/Unmarked.vue'),
            'about': require('./About.vue'),
            'task_group': require('./TaskGroup/Item.vue'),
        },
        methods: {
            getItem() {
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson-content/' + _this.id, {}, function (response) {
                    _this.lesson = response.data;
                    if(response.data) _this.loading = false;
                    _this.createArray();
                });
            },
            cancel() {
                this.getItem();
                this.toViewTab();
            },
            save() {
                let _this = this;
                this.loading = true;
                post(_this, '/api/lesson-content-save/' + this.lesson.id, this.lesson, function (response) {
                    _this.errors = '';
                    _this.getItem();
                    _this.toViewTab();
                    _this.loading=false;
                }, function (error) {
                    _this.loading=false;
                    _this.errors = error.response.data;
                });
            },
            createArray() {
                let _this = this;
                if(_this.lesson.pages.length>0) {
                    _this.lesson.pages.forEach(function(page) {
                        if(page.materials.length>0) {
                            page.materials.forEach(function (material) {
                                _this.$set(material,'showAdditional',false);
                                _this.$set(material,'upload',0);
                                if(material.task_group && material.task_group.tasks && material.task_group.tasks.length > 0) {
                                    material.task_group.tasks.forEach(function (task) {
                                        _this.$set(task,'uploadAudio',0);
                                        _this.$set(task,'uploadImage',0);
                                    });
                                }
                            });
                        }
                    });
                }

                if(_this.lesson.homework.length>0) {
                    _this.lesson.homework.forEach(function (task_group) {
                        task_group.tasks.forEach(function (task) {
                            _this.$set(task,'uploadAudio',0);
                            _this.$set(task,'uploadImage',0);
                        });
                    });
                }
                if(_this.lesson.tests.length>0) {
                    _this.lesson.tests.forEach(function (test) {
                        test.task_groups.forEach(function (task_group) {
                            task_group.tasks.forEach(function (task) {
                                _this.$set(task,'uploadAudio',0);
                                _this.$set(task,'uploadImage',0);
                            });
                        });
                    });
                }
            },
            toViewTab() {
                switch(this.activeTab) {
                    case 'manual-edit':
                        this.activeTab = 'manual';
                        break;
                    case 'materials-edit':
                        this.activeTab = 'materials';
                        break;
                    case 'tests-new':
                        this.activeTab = 'tests';
                        break;
                    case 'tests-edit':
                        this.activeTab = 'tests';
                        break;
                    case 'homework-edit':
                        this.activeTab = 'homework';
                        break;
                }
            },
            deleteLesson() {
                this.$refs.modalDeleteLesson.show();
            },
            removeLesson() {
                let _this = this;
                del(_this, '/api/lesson-delete/'+this.lesson.id, {}, function (response) {
                    _this.$refs.modalDeleteLesson.hide();
                    _this.$router.push({name:'group',params:{id:_this.lesson.group.id}});
                });
            },
            newTest() {
                this.lesson.tests.push({name: '', duration: '', access: 0, task_groups: []});
                this.activeTab = 'tests-new';
            },
            testItem(id) {
                let _this = this;
                this.lesson.tests.forEach(function(test,index) {
                   if(id == test.id) _this.selectedTestIndex = index;
                });
                this.activeTab = 'tests-item';
            },
            testEdit(id) {
                let _this = this;
                this.lesson.tests.forEach(function(test,index) {
                    if(id == test.id) _this.selectedTestIndex = index;
                });
                this.activeTab = 'tests-edit';
            },
            markPossible(value) {
                var lesson_datetime = moment(value.datetime, "YYYY-MM-DD HH:mm:ss").set({
                    'hour': 0,
                    'minute': 0,
                    'second': 0
                });
                return lesson_datetime <= this.now;
            },
            startLesson() {
                let _this = this;
                post(_this,'/api/lesson-start/'+this.lesson.id, {}, function (response) {
                    _this.getItem();
                });
            },
            unmarked() {
                this.$refs.unmarked.showModal();
            },
            toGroup() {
                this.$router.push({name:'group',params:{id: this.lesson.group.id}});
            },
            task(task_group) {
                this.selectedTaskGroup = task_group;
                this.activeTab = 'task_group';
            },
            taskStudent(obj) {
                this.selectedTaskGroup = obj.task_group;
                this.activeTab = 'task_group';
                this.$nextTick(function() {
                    if(this.$refs.taskGroup) {
                        this.$refs.taskGroup.activeTab = 'student';
                        this.$refs.taskGroup.selectedId = obj.student_id;
                        this.$refs.taskGroup.selectedName = obj.name;
                    }
                });
            }
        },

        created() {

            this.getItem();
        }
    }
</script>
