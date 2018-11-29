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
                    <router-link :to="{ name: 'lesson_template', params: { id: lesson.lesson_template_id }}" class="text-info mr-4"><span class="fa fa-angle-left mr-2"></span>Вернуться в шаблон</router-link>
                    <span class="mr-4" ><a href="#" @click="renameItem()" class="text-info"><span class="fa fa-pencil mr-2"></span>Изменить название</a></span>
                </div>
                <div class="display-4" style="font-size:2.5rem;">
                    {{ lesson.title }}
                </div>
            </div>
            <!--
                1. В занятии есть непроверенные задания студентов, которые вам необходимо проверить вручную
             -->
            <div class="clearfix mb-5">
                <div class="pull-left">
                    <div class="btn-group">
                        <button :disabled="edit" @click="activeTab='materials'" class="btn btn-secondary border-right-0" :class="{'active': activeTab=='materials'}" >
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
                        <button :disabled="edit" @click="activeTab='homework'" class="btn btn-secondary" :class="{'active': activeTab=='homework'}">
                            Задание
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
                <div v-if="lesson.is_started" class="pull-right">
                    <a href="javascript:void(0)" class="btn btn-success" style="font-weight:500" @click="activeTab='mark'">
                        <span class="fa fa-check mr-1"></span>
                        Завершить занятие
                    </a>
                </div>
            </div>
            <materials v-if="activeTab=='materials'" :lesson="lesson"></materials>
            <materials-edit :errors="errors" v-if="activeTab=='materials-edit'" :lesson="lesson" v-on:cancel="cancel" v-on:save="save"></materials-edit>
            <manual v-if="activeTab=='manual'" :manual="lesson.manual"></manual>
            <manual-edit v-if="activeTab=='manual-edit'" :lesson="lesson" v-on:cancel="cancel" v-on:save="save"></manual-edit>
            <homework v-if="activeTab=='homework'" :test="lesson.homework" @cancel="cancel" @save="save" @homeworkEdit="activeTab='homework-edit'" ></homework>
            <homework-edit :errors="errors" v-if="activeTab=='homework-edit'" :test="lesson.homework" v-on:cancel="cancel" v-on:save="save"></homework-edit>
            <b-modal ref="modalDeleteLesson" title="Подтвердите удаление">
                Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
                <div slot="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$refs.modalDeleteLesson.hide()">Отменить</button>
                    <button type="button" class="btn btn-danger" @click="removeLesson()">Удалить</button>
                </div>
            </b-modal>
            <b-modal ref="modalRenameLesson" title="Изменить название">
                <div class="form-group">
                    <label style="font-weight:500" class="d-block">Название</label>
                    <input type="text" v-model="newTitle" class="form-control" placeholder="укажите название занятия">
                    <error-alert v-if="errors && errors.title">
                        {{ errors.title[0] }}
                    </error-alert>
                </div>
                <div slot="modal-footer">
                    <button type="button" class="btn btn-primary" @click="rename()">Сохранить</button>
                    <button type="button" class="btn btn-secondary" @click="cancelRename()">Отменить</button>
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>

    import { get, post, del } from '../../../helpers/api';
    import wysiwyg from "vue-wysiwyg";
    export default {

        props: ['id','setTab'],

        data () {
            return {
                loading: true,
                lesson: '',
                activeTab: this.setTab ? this.setTab : 'materials',
                edit: false,
                errors: [],
                selectedTestIndex: '',
                newTitle: ''
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
            'manual': require('../../../components/lms/Manual.vue'),
            'manual-edit': require('../../../components/lms/Edit/Manual.vue'),
            'materials-edit': require('../../../components/lms/Edit/Materials.vue'),
            'homework': require('./Homework/Item.vue'),
            'homework-edit': require('../../../components/lms/Homework/Edit.vue'),
            ErrorAlert: require('../../../components/ErrorAlert.vue')
        },
        methods: {
            getItem() {
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson-template-item-content/' + _this.id, {}, function (response) {
                    _this.lesson = response.data;
                    _this.newTitle = response.data.title;
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
                post(_this, '/api/lesson-template-item-content-save/' + this.lesson.id, this.lesson, function (response) {
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
            renameItem() {
                this.$refs.modalRenameLesson.show();
            },
            rename() {
                let _this = this;
                this.loading = true;
                let form = {
                    title: this.newTitle
                };
                post(_this, 'api/lesson-template-item-rename/'+this.lesson.id, form, function(response) {
                    _this.newTitle = '';
                    _this.errors = '';
                    _this.getItem();
                    _this.loading=false;
                    _this.$refs.modalRenameLesson.hide();
                }, function (error) {
                    _this.loading=false;
                    _this.errors = error.response.data;
                });
            },
            cancelRename() {
                this.newTitle = '';
                this.$refs.modalRenameLesson.hide();
            },
        },

        created() {

            this.getItem();
            const wyscfg = {
                image: {
                    uploadURL: "/api/upload-file",
                    dropzoneOptions: {
                        headers: this.$auth.getToken() ? {'Authorization': `Bearer ${this.$auth.getToken()}`} : ''
                    }
                }
            }

            Vue.use(wysiwyg, wyscfg);

        }
    }
</script>
