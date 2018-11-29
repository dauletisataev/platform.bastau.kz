
<!--
    TODO: Разгрузить страницу в модули
    TODO: Нужно поменять структуру раздела, перенсти все модальные окна и модули в соответствующие папки
-->

<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div v-if="group">
            <div class="col-10 offset-2 pr-5">
                <div v-if="group.unmarked_tasks && group.unmarked_tasks.length>0 && !$user.isManager()" class="alert alert-danger clearfix">
                    <div class="pull-left" style="font-weight:500">В этой группе есть занятия с непроверенными заданиями студентов, которые вам необходимо проверить вручную</div>
                    <button class="btn btn-sm btn-danger pull-right" data-toggle="modal" @click="unmarked()" data-target="#checktasks">проверить</button>
                </div>
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active': activeTab == 'info' }" @click="setActiveTab('info')"  href="#info" role="tab" data-toggle="tab" >Данные</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active': activeTab == 'stats' }" @click="setActiveTab('stats')" href="#stats" role="tab" data-toggle="tab">Статистика</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active': activeTab == 'students' }" @click="setActiveTab('students')" href="#students" role="tab" data-toggle="tab">Ученики <span class="badge badge-pill badge-default">{{ countCurrent() }}</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active': activeTab == 'lessons' }" @click="setActiveTab('lessons')" href="#lessons" role="tab" data-toggle="tab">Занятия</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active': activeTab == 'history' }" @click="setActiveTab('history')" href="#history" role="tab" data-toggle="tab">История</a>
                    </li>
                </ul>

                <div class="tab-content pt-4 mb-2">
                    <div role="tabpanel" class="tab-pane active" id="info">
                        <div class="mb-4">
                            <router-link v-if="!$user.isTeacher()" :to="{name: 'group-change-teacher', params:{ group_id: group.id }}" class="btn btn-sm btn-primary">
                                <span v-if="group.teacher" class="fa fa-fw fa-exchange"></span><span v-else class="fa fa-fw fa-plus"></span> {{ group.teacher ? 'Заменить' : 'Указать'  }} преподавателя
                            </router-link>
                            <button v-if="!$user.isTeacher()"  @click="edit()" type="button" class="btn btn-primary btn-sm">
                                <span class="fa fa-fw fa-pencil"></span>
                                Изменить данные
                            </button>
                            <button v-if="!$user.isTeacher()" @click="$refs.addSchedules.showModal()" type="button" class="btn btn-primary btn-sm">
                                <span class="fa fa-fw fa-calendar"></span>
                                Изменить расписание
                            </button>
                            <button v-if="!$user.isTeacher()"
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    @click="$refs.modalArchive.showModal();">
                                <span class="fa fa-fw fa-archive"></span>
                                {{ group.in_archive ? 'Восстановить из архива' : 'Архивировать' }}
                            </button>
                            <button v-if="$user.isAdmin()" type="button" class="btn btn-sm btn-danger" @click="$refs.modalDelete.show()">
                                <span class="fa fa-fw fa-trash"></span>
                                Удалить группу
                            </button>
                        </div>
                        <table class="table table-sm">
                            <tbody>
                            <tr>
                                <td class="pl-0"><span class="h6">ID группы</span></td>
                                <td>{{ group.id }}</td>
                            </tr>
                            <tr v-if="group.created_at">
                                <td class="pl-0"><span class="h6">Дата создания</span></td>
                                <td>{{ group.created_at | date }}</td>
                            </tr>
                            <tr v-if="$common.data && $common.data.offices">
                                <td class="pl-0"><span class="h6">Отделение</span></td>
                                <td>{{ group.office.name }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Тип занятий</span></td>
                                <td>{{ group.lesson_type.name }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Программа</span></td>
                                <td>{{ group.program.name }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Уровень</span></td>
                                <td>{{ group.level.name }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Время занятий</span></td>
                                <td>{{ $common.groupSchedule(group.schedules) }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Преподаватель</span></td>
                                <td v-if="group.teacher"><router-link  :to="{ name: 'teacher', params: { id: group.teacher.id }}">{{ group.teacher.user.name }}</router-link></td>
                                <td v-else class="text-danger">Нет</td>
                            </tr>
                            <tr>
                                <td class="pl-0"><span class="h6">Длительность занятий</span></td>
                                <td>{{ group.lesson_duration }}</td>
                            </tr>
                            <!--
                            <tr>
                                <td class="pl-0"><span class="h6">Тариф</span></td>
                                <td v-if="group.rate_id != 0 && group.rate">{{  group.rate.cost + " за " + group.rate.countLesson + " занятий в месяц по " + group.rate.duration + " минут, " + group.rate.months + " месяцев " }}</td>
                                <td v-if="group.rate_id == 0 && group.rate">{{  group.custom_rate_cost + " за " + group.custom_rate_countLesson + " занятий в месяц по " + group.custom_rate_duration + " минут, " + group.custom_rate_months + " месяцев "}}</td>
                                <td v-if="!group.rate_id && !group.rate">не установлен</td>
                            </tr>
                            </tbody>
                            <tr>
                                <td class="pl-0"><span class="h6">Стоимость занятия</span></td>
                                <td v-if="group.rate_id != 0 && group.rate">{{  Math.floor(group.rate.cost / group.rate.countLesson) }} тг</td>
                                <td v-if="group.rate_id == 0 && group.rate">{{  Math.floor(group.custom_rate_cost / group.custom_rate_countLesson) }} тг</td>
                                <td v-if="!group.rate_id && !group.rate">{{ Math.floor(group.cost) }} тг</td>
                            </tr>
                            -->

                            </tbody>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="students">
                        <div class="clearfix mb-4">
                            <div class="pull-right">
                                <router-link v-if="!$user.isTeacher()" :to="{ name: 'group-add-students', params: { group_id: group.id }}" class="btn btn-sm btn-primary"><span class="fa fa-fw fa-plus"></span> добавить учеников</router-link>
                            </div>

                            <div class="btn-group btn-group-sm pull-left" data-toggle="buttons">
                                <button @click="studentFilter=false" class="btn btn-secondary" :class="{'active': !studentFilter}">
                                    <input type="radio" name="studentslist">
                                    текущие
                                    <span class="badge badge-pill ml-1 badge-primary">{{ countCurrent() }}</span>
                                </button>
                                <button @click="studentFilter=true" class="btn btn-secondary" :class="{'active': studentFilter}">
                                    <input type="radio" name="studentslist">
                                    архивные
                                    <span class="badge badge-pill ml-1 badge-default">{{ countArchived() }}</span>
                                </button>
                            </div>
                        </div>
                        <table class="table mt-4" v-if="!studentFilter">
                            <thead class="bg-faded h6">
                            <tr>
                                <th></th>
                                <th v-if="!$user.isTeacher()">Тариф</th>
                                <th v-if="!$user.isTeacher()">Баланс</th>
                                <th>Посещаемость</th>
                                <th>Посл. посещение</th>
                                <th v-if="$root.user && !$root.user.isTeacher() && !$user.isAccountant()"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="student in group.students" v-if="student.in_archive!=1 && student.pivot.deleted!=1">
                                <td v-if="!$user.isTeacher()">
                                    <router-link :to="{ name: 'student', params: { id: student.id }}" style="text-decoration: none; color: #292b2c; ">
                                        <div class="h6 mb-0 p-0" style="margin-top:-4px">{{ student.user.name }}</div>
                                        <div class="small text-muted" style="margin-top:-4px">{{ student.user.phone }}</div>
                                    </router-link>
                                </td>
                                <td v-if="$user.isTeacher()">
                                    <div class="h6 mb-0 p-0" style="margin-top:-4px">{{ student.user.name }}</div>
                                    <div class="small text-muted" style="margin-top:-4px">{{ student.user.phone }}</div>
                                </td>
                                <td v-if="!$user.isTeacher()">
                                    <div style="display: inline-block">
                                        <span class="small text-muted">
                                            <span v-if="rate(student)" class="fa fa-bars"></span>
                                                {{ rate(student) ? rate(student) : "тариф не найден" }}
                                        </span>
                                    </div>
                                    <button type="button" v-if="rate(student)" class="btn btn-sm btn-secondary ml-2 d-inline" @click="setInd(student)"><span class="fa fa-pencil"></span></button>
                                </td>
                                <td v-if="!$user.isTeacher()">
                                    <b-popover v-if="rate(student)" triggers="hover" :content="balanceInfo(student)">
                                        <span :class="{'text-success' : balance(student) > 0 && (isBalanceFreeze(student)!=1 || !isBalanceFreeze(student)) , 'text-danger': balance(student) < 0 && (isBalanceFreeze(student)!=1 || !isBalanceFreeze(student)), 'text-muted': balance(student) == 0 && (isBalanceFreeze(student)!=1 || !isBalanceFreeze(student)), 'text-info': isBalanceFreeze(student) == 1}" style="border-bottom:1px dotted #5CB85C; cursor: help">
                                        {{ formatBalance(balance(student)) }}
                                        </span>
                                    </b-popover>
                                    <span v-else>тариф не найден</span>
                                </td>

                                <td>
                                    <div class="row pl-3">
                                        <div class="col-xs-1 mr-1" v-for="lesson in attendancePills(group,student)" :key="lesson.id">
                                            <b-popover triggers="hover" :content="lessonInfo(lesson,student)">
                                                <span class="badge badge-pill p-1" :class="{'badge-default': !lesson.datetime, 'bg-faded': !lesson.datetime, 'badge-success': pillDanger(lesson,student.id)==0, 'badge-danger': pillDanger(lesson,student.id)==1, 'badge-warning': pillDanger(lesson,student.id)==2 }" style="font-size:1px;">&nbsp;</span>
                                            </b-popover>
                                        </div>
                                    </div>
                                </td>

                                <td><span v-if="student.last_lesson">{{ student.last_lesson | datetime}}</span></td>
                                <td v-if="$root.user && !$root.user.isTeacher() && !$user.isAccountant()">
                                    <b-tooltip content="Убрать из группы" style="display:inline-block">
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteStudent(student.id)"><span class="fa fa-close"></span></button>
                                    </b-tooltip>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="table mt-4" v-else>
                            <thead class="bg-faded h6">
                            <tr>
                                <th></th>
                                <th>Статус</th>
                                <th>Удален из группы</th>
                                <th>Посл. посещение</th>
                                <th v-if="$root.user && !$root.user.isTeacher() && !$user.isAccountant()"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="student in group.students" v-if="student.in_archive==1 || student.pivot.deleted==1">
                                <td v-if="!$user.isTeacher()"><router-link :to="{ name: 'student', params: { id: student.id }}" style="text-decoration: none; color: #292b2c; ">
                                    <div class="h6 mb-0 p-0" style="margin-top:-4px">{{ student.user.name }}</div>
                                    <div class="small text-muted" style="margin-top:-4px">{{ student.user.phone }}</div>
                                </router-link>
                                <td v-else>
                                    <div class="h6 mb-0 p-0" style="margin-top:-4px">{{ student.user.name }}</div>
                                    <div class="small text-muted" style="margin-top:-4px">{{ student.user.phone }}</div>
                                </td>
                                <td>
                                    <b-tooltip v-if="student.in_archive==1" triggers="hover" content="архивирован">
                                        <span class="badge badge-default badge-pill p-1" style="font-size:1px; vertical-align: middle;margin-top: -7px;">&nbsp;</span>
                                    </b-tooltip>
                                    <b-tooltip v-else triggers="hover" content="активный">
                                        <span class="badge badge-success badge-pill p-1" style="font-size:1px; vertical-align: middle;margin-top: -7px;">&nbsp;</span>
                                    </b-tooltip>
                                </td>
                                <td v-if="student.in_archive==1 && student.pivot.deleted!=1">{{ student.archive_date | datetime }}</td>
                                <td v-if="(student.in_archive!=1 && student.pivot.deleted==1) || (student.in_archive==1 && student.pivot.deleted==1)">{{ student.pivot.date_deleted | datetime }}</td>
                                <td><span v-if="student.last_lesson">{{ student.last_lesson | datetime}}</span></td>
                                <td v-if="$root.user && !$root.user.isTeacher() && !$user.isAccountant()">
                                    <!-- восстановить -->
                                    <button type="button" class="btn btn-secondary btn-sm" @click="restore(student)">
                                        <span class="fa fa-undo"></span>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="stats">
                        <div class="card mb-4">
                            <div class="card-block">
                                <h4>Прогресс: <span class="text-warning">{{group.progress}}%</span></h4>
                                <div class="progress mt-1"><div class="progress-bar" v-bind:class="$common.getProgressClass(group.progress)" role="progressbar" :style="{width: group.progress+'%','height':'4px'}" :aria-valuenow="group.progress" aria-valuemin="0" aria-valuemax="100">{{group.progress}}%</div></div>
                                <table class="table mt-3 mb-0 table-sm">
                                    <tbody><tr>
                                        <td><span class="h6">Проведено занятий</span></td>
                                        <td>{{ group.count_passed_lessons }}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="h6" >Занятий по плану</span></td>
                                        <td v-if="group.rate_id >= 0" v-html="rateLessonsInfo(group)"></td>
                                        <td v-else>тариф не установлен</td>
                                    </tr>
                                    <tr>
                                        <td><span class="h6">Первое занятие</span></td>
                                        <td>{{ group.first_lesson ? formatDate(group.first_lesson) : "не проведено" }}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="h6">Последнее занятие</span></td>
                                        <td>{{ group.last_lesson ? formatDate(group.last_lesson) : "не проведено" }}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="h6">Следующее занятие</span></td>
                                        <td>{{ group.next_lesson ? formatDate(group.next_lesson) : "не добавлено" }}</td>
                                    </tr>
                                    </tbody></table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-block">
                                <h4>Посещаемость: <span class="text-success">{{group.attendance}}%</span></h4>
                                <div class="progress mt-1"><div class="progress-bar" v-bind:class="$common.getProgressClass(group.attendance)" role="progressbar" :style="{width: group.attendance+'%', height: '4px'}" :aria-valuenow="group.attendance" aria-valuemin="0" aria-valuemax="100">{{group.attendance}}%</div></div>
                                <table class="table mt-3 mb-0 table-sm">
                                    <tbody><tr>
                                        <td><span class="h6">Посещений</span></td>
                                        <td>{{ group.count_visits }}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="h6">Пропусков</span></td>
                                        <td>{{ group.count_misses }}</td>
                                    </tr>
                                    </tbody></table>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="lessons">
                        <div class="mb-4">
                            <button type="button" @click="$refs.lessonModal.completeLesson()" class="btn btn-primary">
                                <span class="fa fa-fw fa-check"></span>
                                Провести занятие
                            </button>
                            <button type="button" @click="$refs.lessonModal.newLesson()" class="btn btn-primary">
                                <span class="fa fa-fw fa-calendar"></span>
                                Запланировать занятие
                            </button>
                            <button type="button" @click="$refs.lessonModal.loadLesson()" class="btn btn-primary">
                                <span class="fa fa-fw fa-arrow-down"></span>
                                Загрузить занятия из шаблона
                            </button>
                            <button type="button" class="btn btn-success" @click="$refs.modalLevelUp.show()">
                                <span class="fa fa-fw fa-check"></span>
                                Закончить уровень
                            </button>
                        </div>
                        <module-lessons ref="lessons" v-on:getItem="getItem" :group="group"></module-lessons>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="history">
                        <module-history :group="group"></module-history>
                    </div>
                </div>
            </div>


            <div v-if="$root.user && !$user.isTeacher() && !$user.isAccountant()">

                <group-form ref="editGroup" :data="$common.data" :_form="group" v-on:formSending="getItem"></group-form>

                <modal-archive ref="modalArchive" :group="group" v-on:getItem="getItem"></modal-archive>

                <b-modal ref="modalDeleteStudent" title="Подтвердите удаление" v-bind:class="{ 'has-error': errors && errors.id }">
                    <div v-show="$user.isAdmin()" class="btn-group btn-group-sm">
                        <button class="btn btn-primary" :class="{'active': deleteType==0}" @click="deleteType=0">убрать из группы</button>
                        <!-- <button class="btn btn-primary" :class="{'active': deleteType==1}" @click="deleteType=1">полностью архивировать</button> -->
                        <button class="btn btn-primary" :class="{'active': deleteType==2}" @click="deleteType=2" v-if="$user.isAdmin()">удалить из группы</button>
                    </div>
                    <div class="text-muted mt-3" v-if="deleteType==0">Ученик переместится в "архивных" в группе: у него не будет списываться плата за занятия и засчитываться пропуски в данной группе, при этом он останется активным в общем разделе "студенты".</div>
                    <!-- <div class="text-muted mt-3" v-if="deleteType==1">Ученик будет полностью архивирован в системе (во всех группы): у него не будет списываться плата за занятия и засчитываться пропуски, при этом он останется доступен в списке "архивных" в разделе "студенты".</div> -->
                    <div class="text-muted mt-3" v-if="deleteType==2">Ученик будет полностью отвязан от этой группы, при этом он останется активным в общем разделе "студенты".</div>
                    <!--
                    <div v-if="deleteType==1" class="form-group row mt-4 mb-0" >
                        <label class="col-3 col-form-label">Категория</label>
                        <div class="col-9">
                            <v-select required="true" label="name" value="id" :on-change="selectReason" :value.sync="reason_select" :options="reasons" placeholder="Выберите причину архивации" ></v-select>
                            <form-error v-if="errors && errors.id" :errors="errors">
                                {{ errors.id[0] }}
                            </form-error>
                        </div>
                    </div>
                    <div v-if="deleteType==1" class="form-group row">
                        <label class="col-3 col-form-label">Описание</label>
                        <div class="col-9">
                            <div class="mt-2">
                                <textarea required="true" v-model="description" class="form-control" placeholder="Укажите описание"></textarea>
                            </div>
                        </div>
                    </div>
                    <div v-show="deleteType==1" class="form-group row mt-2" >
                        <div class="col-3"></div>
                        <div class="col-9">
                            <b-form-checkbox id="checkbox1" v-model="is_return">Возобновит обучение</b-form-checkbox>
                        </div>
                    </div> -->
                    <div slot="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$refs.modalDeleteStudent.hide()">Отменить</button>
                        <button type="button" class="btn btn-danger" @click="removeStudent()">Подтвердить</button>
                    </div>
                </b-modal>

            </div>

                <b-modal ref="modalDelete" title="Подтвердите удаление">
                    Вы действительно хотите удалить группу? Данное действие нельзя отменить.
                    <div slot="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отменить</button>
                        <button type="button" class="btn btn-danger" @click="remove()">Удалить</button>
                    </div>
                </b-modal>




                <b-modal ref="modalRestoreStudent" title="Подтвердите действие">
                    Вы действительно хотите восстановить студента?
                    <div slot="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$refs.modalRestoreStudent.hide()">Отменить</button>
                        <button type="button" class="btn btn-success" @click="restoreStudent()">Подтвердить</button>
                    </div>
                </b-modal>

            <div v-if="($root.user || $user.isTeacher()) && !$user.isAccountant()">

                <b-modal ref="modalLevelUp" title="Подтвердите действие">
                    Вы действительно хотите закончить уровень?
                    <div slot="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$refs.modalLevelUp.hide()">Отменить</button>
                        <button type="button" class="btn btn-success" @click="levelUp()">Закончить</button>
                    </div>
                </b-modal>

            </div>

            <show-schedules ref="showSchedules" :schedules="group.schedules"></show-schedules>
            <module-lesson-modal ref="lessonModal" v-on:getItem="getItem" :group="group"></module-lesson-modal>
            <student-set-ind ref="setInd" :student="student" :group="group" v-on:formSending="getItem"></student-set-ind>
            <student-rate-edit ref="formRate" :student="student" :data="$common.data" :_form="selectRate(student)" v-if="student" @success="getItem()"></student-rate-edit>
            <unmarked-tasks v-if="group.unmarked_tasks && group.unmarked_tasks.length>0" ref="unmarked" :tasks="group.unmarked_tasks" :group_id="group.id" @reload="getItem()"></unmarked-tasks>
            <add-schedule ref="addSchedules" :group_id="group.id" @formSending="getItem"></add-schedule>
        </div>
    </div>
</template>

<script>

    import { post, get, del } from '../../helpers/api';
    import moment from 'moment';

    export default{

        props: ['id'],

        data() {

            return {
                errors: [],
                group: '',
                form: '',
                lesson: '',
                students: '',
                selectedLessonId: '',
                selectedStudentId: '',
                activeTab: 'info',
                student: '',
                loading: false,
                studentFilter: false,
                deleteType: 0,
                reason_select: '',
                reason_id: '',
                is_return: false,
                description: '',
                selectedStudent: '',
            }

        },
        computed: {
            reasons() {
                let comp = this;
                var array = [];
                if(this.$common.data.archive_reasons) {
                    this.$common.data.archive_reasons.forEach(function(reason) {
                        if(reason.forStudent==1) array.push(reason);
                    });
                }
                return array;
            }
        },
        components: {
            FormError : require('./../../components/FormError.vue'),
            'group-form': require('./Form.vue'),
            'show-schedules': require('./../../components/ShowSchedules.vue'),
            'module-lessons': require('./item/modules/Lessons.vue'),
            'module-lesson-modal': require('./item/modules/ModalLesson.vue'),
            'module-history': require('./item/modules/History.vue'),
            'modal-archive': require('./modals/Archive.vue'),
            'student-set-ind': require('./modals/SetInd.vue'),
            'student-rate-edit': require('../students/modal/StudentRateForm.vue'),
            'unmarked-tasks': require('./modals/Unmarked.vue'),
            'add-schedule': require('./modals/AddSchedule.vue')
        },
        filters: {
            date: function(value) {
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD.MM.YYYY");
            },
            datetime: function(value) {
                moment.locale('ru');
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD MMM YYYY");
            }
        },
        methods: {

            remove() {
                let _this = this;

                del(_this, '/api/group-delete/'+this.id, {}, function (response) {
                    _this.$router.push('/groups');
                });

            },
            selectReason(val)
            {
                if (val)
                    this.reason_id = val.id;
                this.reason_select = val;
            },

            restoreStudent() {
                let _this = this;
                // Yuritsuki - нужен рефакторинг
                if(this.selectedStudent.pivot.deleted==1 && this.selectedStudent.in_archive!=1) {
                    get(_this, '/api/student/' + this.selectedStudentId + '/fake-attach-group/', { params: this.group.id }, function (response) {
                        _this.errors = '';
                        _this.$refs.modalRestoreStudent.hide();
                        _this.getItem();
                    });
                }
                if(this.selectedStudent.in_archive==1 && this.selectedStudent.pivot.deleted==0) {
                    get(_this, '/api/student-archive/' + this.selectedStudentId, { params: this.reason, archive_type: 'current' }, function (response) {
                        _this.errors = '';
                        _this.$refs.modalRestoreStudent.hide();
                        _this.getItem();
                    });
                }
                if(this.selectedStudent.in_archive==1 && this.selectedStudent.pivot.deleted==1) {
                    get(_this, '/api/student-archive/' + this.selectedStudentId, {params: this.reason, archive_type: 'current'}, function (response) {
                        _this.restoreNext();
                    });
                }
            },

            restoreNext() {
                console.log('third condition second step');
                let _this = this;
                get(_this, '/api/student/' + this.selectedStudentId + '/fake-attach-group/', {params: this.group.id}, function (response) {
                    _this.errors = '';
                    _this.$refs.modalRestoreStudent.hide();
                    _this.getItem();
                });
            },

            removeStudent() {

                let _this = this;
                switch(this.deleteType) {
                    case 0:
                        get(_this, '/api/student/' + this.selectedStudentId + '/fake-delete-group/', { params: this.group.id }, function (response) {
                            _this.$refs.modalDeleteStudent.hide();
                            _this.getItem();
                            _this.errors = '';
                        });
                        break;
                    case 1:
                        this.archive();
                        break;
                    case 2:
                        del(_this, '/api/group/'+this.group.id+'/delete-student/'+this.selectedStudentId, {}, function (response) {
                            _this.$refs.modalDeleteStudent.hide();
                            _this.getItem();
                        });
                        break;
                }
            },
            archive() {

                let _this = this;
                this.reason = this.reason_select;
                if(this.reason) this.reason.is_return = this.is_return;
                if(this.reason) this.reason.description = this.description;
                get(_this, '/api/student-archive/' + this.selectedStudentId, { params: this.reason }, function (response) {
                    _this.getItem();
                    _this.errors = '';
                    _this.$refs.modalDeleteStudent.hide();
                },function (error) {
                    _this.errors = error.response.data;
                });

            },
            edit() {
                this.form = JSON.parse(JSON.stringify(this.group));
                this.$refs.editGroup.showModal();
            },
            deleteStudent(student_id) {
                this.selectedStudentId = student_id;
                this.$refs.modalDeleteStudent.show();
            },

            restore(student) {
                this.selectedStudentId = student.id;
                this.selectedStudent = student;
                this.$refs.modalRestoreStudent.show();
            },

            getItem() {
                this.loading = true;
                let _this = this;
                get(_this, '/api/group/' + _this.id, {}, function (response) {
                    _this.group = response.data;
                    if(response.data) _this.loading = false;

                });

            },

            calculateCost(student) {
                var group = this.group;
                if(this.group && student.pivot) {
                    if(student.pivot.ind) return student.pivot.ind;
                    if(!student.pivot.ind) {
                        if(student.pivot.rate==0) {
                            if(!group.rate_id && !group.rate) return group.cost;
                            if(group.rate_id==0 && group.rate) return Math.floor(group.custom_rate_cost / group.custom_rate_countLesson);
                            if(group.rate_id>0 && group.rate) return Math.floor(group.rate.cost / group.rate.countLesson);
                        }
                        if(student.pivot.rate==1) return Math.floor(student.pivot.custom_rate_cost / student.pivot.custom_rate_countLesson);
                    }
                }
            },

            rateContent(student) {
                var content;
                var cost = 0;
                var total = 0;
                var count = 0;
                var duration = 0;
                var group = this.group;
                if(this.group && student.pivot) {
                    if(student.pivot.ind) {
                        content = "Используется старое значение индивидуальной стоимости занятий ученика";
                        return content;
                    }
                    if(!student.pivot.ind) {
                        if(student.pivot.rate==0) {
                            if(!group.rate_id && !group.rate) {
                                content = "Используются старые значения стоимости занятий из группы";
                                return content;
                            };
                            if(group.rate_id>=0 && group.rate) {
                                content = "Использется тариф из группы"
                                return content;
                            }
                        }
                        if(student.pivot.rate==1) {
                            total = student.pivot.custom_rate_cost;
                            count = student.pivot.custom_rate_countLesson;
                            duration = student.pivot.custom_rate_duration;
                            cost = Math.floor(student.pivot.custom_rate_cost / student.pivot.custom_rate_countLesson);
                            content = "<span class='small text-muted mb-2'>Используется индивидуальный тариф ученика</span><br>"
                            content = content + "<b>Стоимость в месяц: </b>" + total + "<br><b>Занятий в месяц: </b>" + count + "<br><b>Минут в занятии: </b>" + duration + "<br><b class='text-primary'>Стоимость занятия: " + cost + "</b>";
                            return content;
                        }
                    }
                }
            },


            setActiveTab(nameTab) {
                this.activeTab = nameTab;
            },

            levelUp() {


                let _this = this;

                post(_this, '/api/group/'+this.group.id+'/level-up', {}, function () {
                    _this.$refs.modalLevelUp.hide();
                    _this.getItem();
                });


            },
            setInd(student) {
                this.student = student;
                this.$nextTick(function () {
                    this.$refs.formRate.showModal();
                })

            },


            rateLessonsInfo (group) {
                var html = '';
                var months = 0;
                var countLessons = 0;
                var total = 0;
                if(group.rate_id==0) {
                    months = group.custom_rate_months;
                    countLessons = group.custom_rate_countLesson;
                }
                if(group.rate_id>0 && group.rate) {
                    months = group.rate.months;
                    countLessons = group.rate.countLesson;
                }
                html = months*countLessons + "<span class='text-muted'> (" + months + " месяца по " + countLessons + " занятий)</span>";
                return html;
            },
            formatDate(value) {
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD.MM.YYYY");
            },
            rate(student) {
                let _this = this;
                let found = false;
                let rate = '';
                student.student_rates.forEach(function(student_rate) {
                    if(!found && student_rate.program_id === _this.group.program_id && student_rate.lesson_type_id === _this.group.lesson_type_id) {
                        found = true;
                        rate = student_rate;
                    }
                });

                return rate ? Math.floor(rate.cost / rate.count_lessons) : null;


            },
            rateInfo(student) {
                var cost = '';
                var countLesson = '';
                var duration = '';
                var total = '';
                var html = '';
                var group = this.group;
                if(student.pivot.ind) {
                    total = Math.floor(student.pivot.ind);
                    html = "Используется старое значение индивидуальной стоимости занятий ученика";
                } else {
                    if(student.pivot.rate==0) {
                        if(!group.rate_id && !group.rate) {
                            total = Math.floor(group.cost);
                            countLesson = group.count_lessons;
                            duration = group.lesson_duration;
                            html = "Используются старые значения стоимости занятий из группы";
                        }
                        if(group.rate_id == 0 && group.rate) {
                            cost = group.custom_rate_cost;
                            countLesson = group.custom_rate_countLesson;
                            duration = group.custom_rate_duration;
                            total = Math.floor(group.custom_rate_cost / group.custom_rate_countLesson);
                            html = "<small class='h6 mb-4 text-muted'>"+group.program.name+"</small><br>";
                            html += "<b>Тип занятий:</b> "+group.lesson_type.name+"<br>";
                            if(cost) html += "<b>Стоимость в месяц:</b> "+cost+"<br>";
                            if(countLesson) html += "<b>Занятий в месяц:</b> "+countLesson+"<br>";
                            if(duration) html += "<b>Минут в занятии:</b> "+duration+"<br>";
                            html += "<b class='text-primary'>Стоимость занятия: "+total+"</b>"
                        }
                        if(group.rate_id>0 && group.rate) {
                            cost = group.rate.cost;
                            countLesson = group.rate.countLesson;
                            duration = group.rate.duration;
                            total = Math.floor(group.rate.cost / group.rate.countLesson);
                            html = "<small class='h6 mb-4 text-muted'>"+group.program.name+"</small><br>";
                            html += "<b>Тип занятий:</b> "+group.lesson_type.name+"<br>";
                            if(cost) html += "<b>Стоимость в месяц:</b> "+cost+"<br>";
                            if(countLesson) html += "<b>Занятий в месяц:</b> "+countLesson+"<br>";
                            if(duration) html += "<b>Минут в занятии:</b> "+duration+"<br>";
                            html += "<b class='text-primary'>Стоимость занятия: "+total+"</b>"
                        }
                    } else {
                        cost = student.pivot.custom_rate_cost;
                        countLesson = student.pivot.custom_rate_countLesson;
                        duration = student.pivot.custom_rate_duration;
                        total = Math.floor(student.pivot.custom_rate_cost / student.pivot.custom_rate_countLesson);
                        html = "<small class='h6 mb-4 text-muted'>"+group.program.name+"</small><br>";
                        html += "<b>Тип занятий:</b> "+group.lesson_type.name+"<br>";
                        if(cost) html += "<b>Стоимость в месяц:</b> "+cost+"<br>";
                        if(countLesson) html += "<b>Занятий в месяц:</b> "+countLesson+"<br>";
                        if(duration) html += "<b>Минут в занятии:</b> "+duration+"<br>";
                        html += "<b class='text-primary'>Стоимость занятия: "+total+"</b>"
                    }
                }
                return html;
            },

            formatBalance(value) {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            },
            declOfNum(number) {
                var titles = ['занятие', 'занятия', 'занятий'];
                var cases = [2, 0, 1, 1, 1, 2];
                return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
            },
            balanceInfo(student) {
                let html = '';
                let total = '';
                let prognosis = '';
                let remainder = '';
                let last_expense = '';
                let next_income = '';
                let next_income_temp = '';
                let temp = '';
                let loop = false;
                let daysActive = [];
                let month_expense = 0;
                let total_expense = 0;
                let addWeeks = 0;
                let addDays = 0;
                let temp2 = false;
                let pos = 0;
                let found = false;
                let selectedRate = '';
                let group = this.group;
                student.student_rates.forEach(function(student_rate) {
                    if(!found && (student_rate.program_id === group.program_id) && (student_rate.lesson_type_id === group.lesson_type_id)) {
                        found = true;
                        selectedRate = student_rate;
                    }
                });
                total = Math.floor(selectedRate.cost / selectedRate.count_lessons);
                prognosis = selectedRate.prognosis_balance;
                remainder = selectedRate.balance - prognosis*total;


                html += "<div class='font-weight-bold text-primary text-center'> Хватит на " + prognosis + " " + this.declOfNum(prognosis) + "</div>";
                if(remainder>0) html += "<div class='text-muted text-center' style='margin-top:-4px'>остаток " + remainder + " тенге</div><hr class='my-1'>";

                if(student.balance_history.length > 0) {
                    student.balance_history.forEach(function(expense){
                        if(expense.value>0 && expense.student_rate_id === selectedRate.id) {
                            if(!temp2) {
                                last_expense = expense.created_at;
                                temp2 = true;
                            }
                        }
                    })
                    student.balance_history.forEach(function(expense) {
                        if(expense.value > 0 && expense.student_rate_id === selectedRate.id){
                            total_expense = total_expense + expense.value;
                            if(moment(expense.created_at, "YYYY-MM-DD hh:mm:ss") > moment().startOf('month')) month_expense = month_expense + expense.value;
                        }
                    });
                }
                if(last_expense) {
                    temp = last_expense;
                    moment.locale('ru');
                    last_expense = moment(last_expense, "YYYY-MM-DD HH:mm:ss").format("DD MMM");
                    html += "<b>Посл. пополнение:</b> " + last_expense + "<br>";
                    if(student.balance>0) {
                        if(group) {
                            if(group.schedules.length>0) {
                                let val = JSON.parse(JSON.stringify(group.schedules));
                                let maxRank = val.sort((a,b) => b.schedule_range_id - a.schedule_range_id)[0].schedule_range_id;
                                let schedules = group.schedules.filter(item => item.schedule_range_id === maxRank);
                                schedules.sort((a,b) => a.day - b.day);
                                schedules.forEach(function(day) {
                                    if(day.active==1) daysActive.push(day.day);
                                });
                                if(moment().isoWeekday()>daysActive[daysActive.length-1]) {
                                    next_income = moment().isoWeekday(daysActive[0]).add(1,'weeks').format("DD MMM");
                                    next_income_temp = moment().isoWeekday(daysActive[0]).add(1,'weeks');
                                } else {
                                    daysActive.forEach(function(day) {
                                        if(day>=moment().isoWeekday()){
                                            if(!loop) {
                                                next_income = moment().isoWeekday(day).format("DD MMM");
                                                next_income_temp = moment().isoWeekday(day);
                                                loop = true;
                                            }
                                        }
                                    });
                                }
                                if(prognosis>0) {
                                    if(daysActive.length>0) {
                                        addWeeks = Math.floor(prognosis / daysActive.length);
                                        addDays = prognosis - addWeeks * daysActive.length;
                                    }
                                    if(addWeeks>0) {
                                        next_income_temp = moment(next_income_temp).add(addWeeks, 'weeks');
                                    }
                                    if(addDays>0) {
                                        if(moment(next_income_temp).isoWeekday()==daysActive[0]) {
                                            next_income_temp = moment(next_income_temp).isoWeekday(daysActive[addDays-1]);
                                        } else {
                                            pos = daysActive.findIndex(function (element,index,array) {
                                                if(moment(next_income_temp).isoWeekday()==element)
                                                    return true;
                                            });
                                            if(pos+addDays>daysActive.length-1) {
                                                next_income_temp = moment(next_income_temp).add(1,'weeks').isoWeekday(daysActive[daysActive.length-1-addDays]);
                                            } else {
                                                next_income_temp = moment(next_income_temp).isoWeekday(daysActive[addDays+pos]);
                                            }
                                        }
                                    }
                                    next_income = moment(next_income_temp).format("DD MMM");
                                }
                            } else {
                                next_income = "? (не установлено расписание в группе)";
                            }
                            if(next_income && next_income[0] !== '?') {
                                if(group && group.schedule_exceptions.length > 0) {
                                    group.schedule_exceptions.forEach(function(exception) {
                                        if(moment(exception.old_datetime, "YYYY-MM-DD HH:mm:ss").format("DD.MM.YY") ===
                                            moment(next_income,"DD.MM.YY").format("DD.MM.YY"))
                                            next_income = moment(exception.new_datetime, "YYYY-MM-DD HH:mm:ss").format("DD.MM.YY");
                                    });
                                }
                            }
                            html +=  "<b>След. поплнение:</b> " + next_income + "<hr class='my-1'>";
                        }
                    }
                }
                html += "<b>Оплачено в этом месяце:</b> " + this.formatBalance(month_expense) + "<br>";
                html += "<b>Всего оплачено:</b> " + this.formatBalance(total_expense);
                return html;
            },

            attendancePills(group,student) {
                var date = '';
                var lessons = [];
                var array = [];
                var group_lessons = [];
                var tempLesson = '';
                student.history.forEach(function(event) {
                    if(!date && event.value == group.id) date = event.created_at;
                });
                group.group_lessons.forEach(function(lesson) {
                    if(lesson.passed==1 && lesson.datetime >= date) {
                        tempLesson = lesson;
                        if(lesson.duration==null) {
                            if(!group.rate_id && !group.rate) tempLesson.duration = group.lesson_duration;
                            if(group.rate_id == 0 && group.rate) tempLesson.duration = group.custom_rate_duration;
                            if(group.rate_id>0 && group.rate) tempLesson.duration = group.rate.duration;
                        }
                        lessons.push(tempLesson);
                    }
                });
                for (var i = 0; i <= 11; i++) {
                    if(lessons[i]) {
                        array.unshift(lessons[i]);
                    } else {
                        array.push({ id: "default"+i });
                    }
                }
                return array;
            },
            pillDanger(lesson,id) {
                var temp = 1;
                var search = false;
                if(lesson.students) {
                    if(lesson.students.length>0) {
                        lesson.students.forEach(function(student) {
                            if(id == student.id) {
                                if(student.pivot.visit==1) {
                                    temp = 0;
                                }
                                if(!search) {
                                    search = true;
                                }
                            }
                        });
                        if(!search) temp = 2;
                    } else {
                        temp = 2;
                    }
                }
                return temp;
            },
            lessonInfo(lesson,student) {
                var html = '';
                var temp = 0;
                var id = student.id;
                var cost = 0;
                var search = false;
                if(lesson.datetime) {
                    if(lesson.students) {
                        if(lesson.students.length>0) {
                            lesson.students.forEach(function(student) {
                                if(id == student.id) {
                                    if(student.pivot.visit==1) {
                                        temp = 1;
                                    }
                                    if(!search) {
                                        search = true;
                                    }
                                }
                            });
                            if(!search) temp = 2;
                        } else {
                            temp = 2;
                        }
                    }
                    if(temp==1) {
                        html += "<b class='text-success'>Посетил</b><br>";
                    }
                    if(temp==0){
                        html += "<b class='text-danger'>Пропустил</b><br>";
                    }
                    if(temp==2) {
                        html += "<b class='text-warning'>Перенос</b><br>";
                    }
                    html += "<div class='small text-muted'><b>Дата</b>: " + moment(lesson.datetime, "YYYY-MM-DD hh:mm:ss").format("DD.MM.YYYY") + "<br>";
                    if(student.balance_history.length>0) {
                        student.balance_history.forEach(function(event) {
                            if(lesson.id==event.lesson_id) cost = -event.value;
                        });
                    }
                    if(!this.$user.isTeacher()) html += "<b>Списано</b>: " + cost + "<br>";
                    html += "<b>Минут</b>: " + lesson.duration + "<br>";
                    html += "</div>";
                } else {
                    html = "Занятие не проведено";
                }
                return html;
            },
            countCurrent() {
                var count = 0;
                if(this.group.students && this.group.students.length>0) {
                    this.group.students.forEach(function(student) {
                        if((student.in_archive!=1 || student.in_archive==null) && student.pivot.deleted==0) count++;
                    });
                }
                return count;
            },
            countArchived() {
                var count = 0;
                if(this.group.students && this.group.students.length>0) {
                    this.group.students.forEach(function(student) {
                        if(student.in_archive==1 || student.pivot.deleted==1) count++;
                    });
                }
                return count;
            },
            selectRate(student) {
                let _this = this;
                let found = false;
                let value = '';
                student.student_rates.forEach(function(student_rate) {
                    if(!found && student_rate.program_id === _this.group.program_id && student_rate.lesson_type_id === _this.group.lesson_type_id) {
                        found = true;
                        value = student_rate;
                    }
                });
                return value;
            },
            balance(student) {
                let rate = this.selectRate(student);
                return rate.balance;
            },
            isBalanceFreeze(student) {
                let rate = this.selectRate(student);
                return rate.is_balance_freeze;
            },
            unmarked() {
                this.$refs.unmarked.showModal();
            },

        },

        created() {

            this.getItem();


        }



    }
</script>