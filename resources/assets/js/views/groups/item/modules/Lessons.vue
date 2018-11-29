<template>

    <div>

        <ul class="nav nav-pills small mb-2" role="tablist">
            <li class="nav-item">
                <a class="nav-link" v-bind:class="{'active' : type == 'default'}" @click="setType('default')" href="#lessonsCurrent" role="tab" data-toggle="tab">Запланированные</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" v-bind:class="{'active' : type == 'passed'}" @click="setType('passed')" href="#lessonsDone" role="tab" data-toggle="tab">Завершенные</a>
            </li>
        </ul>
        <div class="card">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="lessonsCurrent" aria-expanded="true">
                    <table class="table mb-0">
                        <thead class="thead-default h6">
                        <tr>
                            <th>Дата</th>
                            <th>Тема</th>
                            <th>Аудитория</th>
                            <th>Минут</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="lesson in group.lessons" class="item">
                            <td :class="changeClass(lesson.datetime)" v-if="!lesson.without_date" style="font-weight:500;">{{ lesson.date }} {{ lesson.time }}</td>
                            <td class="text-warning" style="font-weight:500;" v-else>без даты</td>
                            <td class="w-50 small">{{ lesson.title }}</td>
                            <td v-if="lesson.office_cabinet">{{ lesson.office_cabinet.name }}</td>
                            <td>{{ lesson.duration }}</td>
                            <td>
                                <div class="float-right">
                                    <b-tooltip v-if="lesson.is_full" content="Открыть занятие" style="display:inline">
                                        <router-link :to="{ name: 'lesson_content', params: { id: lesson.id, group_id: group.id}}" class="btn btn-secondary btn-sm"><span class="fa fa-eye"></span></router-link>
                                    </b-tooltip>
                                    <b-tooltip v-if="!lesson.without_date && markPossible(lesson) && !lesson.is_started && lesson.is_full" content="Начать занятие" style="display:inline-block">
                                        <button type="button" class="btn btn-outline-primary btn-sm" @click="startLesson(lesson)"><span class="fa fa-play"></span></button>
                                    </b-tooltip>
                                    <b-tooltip v-if="(!lesson.without_date && markPossible(lesson) && lesson.is_started && lesson.is_full) || (!lesson.is_full && !lesson.without_date && markPossible(lesson))" content="Завершить занятие" style="display:inline-block">
                                        <button type="button" class="btn btn-outline-primary btn-sm" @click="markLesson(lesson)"><span class="fa fa-check"></span></button>
                                    </b-tooltip>
                                    <b-dropdown style="display:inline-block" size="sm" right>
                                        <template slot="text">
                                            <span class="fa fa-fw fa-ellipsis-v"></span>
                                        </template>
                                        <b-dropdown-item><router-link :to="{name: 'lesson', params: { id: lesson.id, group_id: group.id, is_clone: false, lessonDate: ''}}" tag="span"><span class="fa fa-fw fa-pencil" ></span> изменить</router-link></b-dropdown-item>
                                        <b-dropdown-item @click="cloneLesson(lesson)"><span class="fa fa-fw fa-copy" ></span> дублировать</b-dropdown-item>
                                        <b-dropdown-item v-if="!lesson.is_full" @click="attachMaterial(lesson.id)"><span class="fa fa-fw fa-paperclip"></span> прикрепить материал</b-dropdown-item>
                                        <b-dropdown-item @click="deleteLesson(lesson.id)" class="text-danger"><span class="fa fa-fw fa-trash" ></span> удалить</b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="lessonsDone">
                    <table class="table">
                        <thead class="thead-default h6">
                        <tr>
                            <th>Дата</th>
                            <th>Тема</th>
                            <th>Аудитория</th>
                            <th>Минут</th>
                            <th>Преподаватель</th>
                            <th>Посещ.</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        <template v-for="(lessons, level_name) in group.passed_levels">
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="font-weight:500">{{ level_name }}</td>
                            </tr>
                            <tr v-for="lesson in lessons">
                                <td style="font-weight:500"><span class="fa fa-check-circle-o"></span> {{ lesson.date }} {{ lesson.time }}</td>
                                <td class="w-50 small">{{ lesson.title }}</td>
                                <td v-if="lesson.office_cabinet">{{ lesson.office_cabinet.name }}</td>
                                <td>{{ lesson.duration }}</td>
                                <td v-if="lesson.teachers.length>0">{{ lesson.teachers[0].user.name }}</td>
                                <td v-else>[Преподаватель удален]</td>
                                <td>
                                    <b-popover placement="top" triggers="hover" :content="getHtmlListStudents(lesson.students)">
                                        <span style="border-bottom:1px dotted #000;cursor:default;">{{ getStudentsCount(lesson.students) }}</span>
                                    </b-popover>
                                </td>
                                <td>
                                    <div class="float-right">
                                        <b-tooltip content="Открыть занятие" style="display:inline">
                                            <router-link :to="{ name: 'lesson_content', params: { id: lesson.id, group_id: group.id}}" class="btn btn-secondary btn-sm"><span class="fa fa-eye"></span></router-link>
                                        </b-tooltip>
                                        <b-dropdown style="display:inline-block" size="sm" right>
                                            <template slot="text">
                                                <span class="fa fa-fw fa-ellipsis-v"></span>
                                            </template>
                                            <b-dropdown-item @click="editPassedLesson(lesson)"><span class="fa fa-fw fa-pencil" ></span> изменить</b-dropdown-item>
                                            <b-dropdown-item @click="deleteLesson(lesson.id)" class="text-danger"><span class="fa fa-fw fa-trash" ></span> удалить</b-dropdown-item>
                                        </b-dropdown>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <b-modal ref="modalDeleteLesson" title="Подтвердите удаление">
            Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
            <div slot="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$refs.modalDeleteLesson.hide()">Отменить</button>
                <button type="button" class="btn btn-danger" @click="removeLesson()">Удалить</button>
            </div>
        </b-modal>

        <lesson-mark ref="markLesson" :students="group.students" :lesson_id="lesson.id" :group="group" :duration="lesson.duration" v-on:formSending="getItem"></lesson-mark>
        <lesson-edit ref="editPassedLesson"  :data="$common.data" :_form="lesson" :group="group" :lessonDate="lessonDate" v-on:formSending="getItem"></lesson-edit>

    </div>
</template>

<script>

    import { post, del } from '../../../../helpers/api';
    import moment from 'moment';

    export default {

        props: [ 'group' ],

        data() {

            return {

                type: 'default',
                selectedLessonId: '',
                lesson: '',
                lessonDate: {
                    date: '',
                    time: ''
                },
                lessonSaving: false,
                now: moment()


            }

        },

        components: {

            'lesson-mark': require('./MarkLesson.vue'),
            'lesson-edit': require('./EditLesson.vue')

        },
        methods: {

            setType(type) {

                this.type = type;

            },
            removeLesson() {

                let _this = this;

                del(_this, '/api/lesson-delete/'+this.selectedLessonId, {}, function (response) {
                    _this.$refs.modalDeleteLesson.hide();
                    _this.getItem();
                });


            },


            markLesson(lesson) {
                this.lesson = lesson;
                this.$nextTick(function () {
                    this.$refs.markLesson.showModal();
                });
            },
            startLesson(lesson) {
                let _this = this;
                post(_this,'/api/lesson-start/'+lesson.id, {}, function (response) {
                    _this.$router.push({ name: 'lesson_content', params: { id: lesson.id, group_id: _this.group.id}});
                });
            },
            editPassedLesson(lesson) {
                this.lesson = lesson;
                this.$refs.editPassedLesson.showModal();
                this.lessonDate = '';
            },
            deleteLesson(lesson_id) {
                this.selectedLessonId = lesson_id;
                this.$refs.modalDeleteLesson.show();
            },
            cloneLesson(lesson) {
                this.lessonDate = this.group.calendar[lesson.dayId +1 ];
                this.$nextTick(function () {
                    this.$router.push({ name: 'lesson', params: { id: lesson.id, group_id: this.group.id, is_clone: true, lessonDate: this.lessonDate, toContent: true} });
                });
            },
            getItem() {

                this.$emit('getItem');

            },


            onChange(e) {

                this.moved(e.moved);

            },


            moved(current) {

                let calendar  = this.group.calendar,
                    lessons = this.group.lessons;

                if (current.newIndex == 0){
                    current.element.dayId = lessons[1].dayId;
                }else{
                    current.element.dayId = lessons[current.newIndex-1].dayId;
                }


                for (let index = current.newIndex; index <= lessons.length - 1; index++) {

                    let lesson = lessons[index],
                        prevLesson = index > 0 ? lessons[index - 1] : null;

                    if (prevLesson && !lesson.without_date){
                        lesson.dayId = prevLesson.dayId + 1;
                    }


                }

                this.updateLessonsDate();


            },

            updateLessonsDate() {

                let calendar  = this.group.calendar;


                this.group.lessons.forEach(function (lesson, i) {
                    if (lesson.without_date) return false;

                    lesson.date = calendar[lesson.dayId].date;
                    lesson.time = calendar[lesson.dayId].time;

                });

                this.$nextTick(function () {

                    this.lessonSaving = true;

                    let _this = this;

                    post(_this, '/api/lesson-save-date', {lessons: this.group.lessons}, function () {
                        _this.lessonSaving = false;
                        _this.errors = '';
                    }, function () {
                        _this.lessonSaving = false;
                        _this.errors = error.response.data;
                    });



                });

            },


            createScheduler() {

                let calendar  = this.group.calendar;
                let component = this;


                this.group.lessons.forEach(function (lesson, index) {
                    let dayId = '';

                    calendar.forEach(function (day, index) {
                        if(day.date == lesson.date) dayId = index;
                    });


                    if (Number.isInteger(dayId))
                        component.group.lessons[index].dayId = dayId;

                    component.group.lessons[index].oldIndex = index;
                });

            },

            getHtmlListStudents(students) {
                let html = "<ul class='list-group'>";

                students.forEach(function (student) {
                    if (!student.user) return false;
                    html += student.pivot.visit ? "<li class='text-success'>"+student.user.name+"</li>" : "<li class='text-danger'>"+student.user.name+"</li>"
                });

                return html + "</ul>";
            },

            getStudentsCount(students) {
                var total = 0;
                var visit = 0;
                students.forEach(function (student) {
                    if (!student.user) return false;
                    if(student.pivot.visit) visit++;
                    total++;
                });
                return visit + "/" + total;
            },

            markPossible(value) {
                var lesson_datetime = moment(value.datetime, "YYYY-MM-DD HH:mm:ss").set({
                    'hour': 0,
                    'minute': 0,
                    'second': 0
                });
                return lesson_datetime <= this.now;
            },
            changeClass(value) {
                return {
                    'font-weight-normal text-danger' : moment(value, "YYYY-MM-DD HH:mm:ss") < moment().startOf('day'),
                    'font-weight-normal text-warning' : moment(value, "YYYY-MM-DD HH:mm:ss") >= moment().startOf('day') && moment(value, "YYYY-MM-DD HH:mm:ss") <= moment().endOf('day'),
                    'font-weight-normal' : moment(value, "YYYY-MM-DD HH:mm:ss") > moment().endOf('day'),
                    'text-danger' : (value == null || value == "")
                }
            },
            attachMaterial(value) {
                let _this = this;
                post(_this,'/api/lesson-attach-material/'+value, {}, function(response) {
                    _this.$router.push({ name: 'lesson_content', params: { id: value, group_id: _this.group.id} });
                }, function(error) {

                })
            }



        },

        watch: {

            group() {

                this.createScheduler();
            }

        },

        created() {

            this.createScheduler();

        }



    }

</script>

<style>
    .flip-list-move {
        transition: transform 0.5s;
    }
    .no-move {
        transition: transform 0s;
    }
    .ghost {
        opacity: .5;
        background: #C8EBFB;
    }
    .list-group {
        min-height: 20px;
    }
    .list-group-item {
        cursor: move;
    }
    .list-group-item i{
        cursor: pointer;
    }

    .list-complete-enter, .list-complete-leave-active {
        opacity: 0;
    }

</style>