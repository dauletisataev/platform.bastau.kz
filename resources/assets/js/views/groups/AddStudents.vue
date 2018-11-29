<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner" style="z-index:3005; left: 15px">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2" v-if="group">
            <div class="row">
                <students-filter :not_in_ids="not_in_ids" v-if="$common.data.offices" ref="filter" :load="load" v-on:routeQuery="routeQuery($event)" v-on:filtered="filtered" :archived="tempFilter.in_archive"></students-filter>
            </div>
            <div class="clearfix">
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="toList" :class="{'active': !tempFilter.active && !tempFilter.without_group && !tempFilter.is_missing && !tempFilter.is_inactive && !tempFilter.is_debtor }">
                    Все студенты <span class='badge badge-pill ml-1' :class="{'badge-default': !tempFilter.active && !tempFilter.without_group && !tempFilter.is_missing && !tempFilter.is_inactive && !tempFilter.is_debtor, 'badge-danger': tempFilter.active || tempFilter.without_group || tempFilter.is_missing || tempFilter.is_inactive || tempFilter.is_debtor }">{{ total.current }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setActive" :class="{'active': tempFilter.active }">
                    Активные <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.active, 'badge-success': !tempFilter.active }">{{ this.total.active }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setWithout" :class="{'active': tempFilter.without_group }">
                    Без группы <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.without_group, 'badge-danger': !tempFilter.without_group }">{{ this.total.without_group }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setMissing" :class="{'active': tempFilter.is_missing }">
                    Пропускающие <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.is_missing, 'badge-danger': !tempFilter.is_missing }">{{ this.total.missing }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setInactive" :class="{'active': tempFilter.is_inactive }">
                    Неактивные <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.is_inactive, 'badge-danger': !tempFilter.is_inactive }">{{ this.total.inactive }}</span>
                </b-button>
                <b-button size="sm" @click="setDebtors" :class="{'active': tempFilter.is_debtor }">
                    Должники <span v-if="tempFilter.in_archive==0" class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.is_debtor, 'badge-danger': !tempFilter.is_debtor }">{{ this.total.debtors }}</span>
                    <span v-else class='badge badge-pill badge-danger ml-1' :class="{'badge-default': tempFilter.is_debtor}">{{ this.total.debtors_archived }}</span>
                </b-button>
                <b-dropdown v-if="tempFilter.in_archive!=1" @click="" size="sm" right class="m-1 pull-right">
                    <template slot="text">
                        <span class="fa fa-sort-amount-asc mr-1"></span>
                        {{ current_sort() }}
                        <span class="fa fa-angle-down ml-1"></span>
                    </template>
                    <b-dropdown-item :disabled="tempFilter.sort=='next'" @click="sort('next')">по дате след. занятия</b-dropdown-item>
                    <b-dropdown-item :disabled="tempFilter.sort=='id'" @click="sort('id')">по дате добавления</b-dropdown-item>
                    <b-dropdown-item :disabled="tempFilter.sort=='last'" @click="sort('last')">по дате послед. посещения</b-dropdown-item>
                </b-dropdown>
                <b-dropdown v-if="tempFilter.in_archive==1 && $common.data" @click="" size="sm" right class="m-1 pull-right">
                    <template slot="text">
                        {{ currentReason() }}
                        <span class="fa fa-angle-down ml-1"></span>
                    </template>
                    <b-dropdown-item @click="filterByReason()">Все причины</b-dropdown-item>
                    <b-dropdown-item v-for="reason in $common.data.archive_reasons" v-if="reason.forStudent==1" :key="reason.id" @click="filterByReason(reason.id)">{{ reason.name }}</b-dropdown-item>
                </b-dropdown>
            </div>
            <table class="table mt-4" v-if="$refs.filter && $refs.filter.filterData">
                <thead class="bg-faded h6">
                <tr>
                    <th></th>
                    <th v-if="!$user.isTeacher() && tempFilter.in_archive!=1">Тариф</th>
                    <th v-if="!$user.isTeacher()">Баланс</th>
                    <th v-if="!$user.isTeacher() && tempFilter.in_archive==1">Сумма оплат</th>
                    <th v-if="tempFilter.in_archive!=1">Посещаемость</th>
                    <th v-if="tempFilter.in_archive!=1">Группа</th>
                    <th v-if="tempFilter.in_archive!=1">След. занятие</th>
                    <th v-if="tempFilter.in_archive!=1">Посл. посещение</th>
                    <th v-if="!$user.isTeacher() && tempFilter.in_archive!=1"></th>
                    <th v-if="tempFilter.in_archive==1">Причина архивации</th>
                    <th v-if="tempFilter.in_archive==1">Дата архивации</th>
                    <th v-if="tempFilter.in_archive==1">Дата добавления</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="student in students">
                    <td>
                        <label class="custom-control custom-checkbox d-block"><input type="checkbox" class="custom-control-input studentsTimeDay" :value="student.id" v-model="selectStudentIds"><span class="custom-control-indicator"></span>
                        <div class="h6 mb-0 p-0" style="margin-top:-4px">{{ student.user.name }}</div>
                        <div class="small text-muted" style="margin-top:-4px">{{ student.user.phone }}</div>
                        </label>
                    </td>
                    <td v-if="!$user.isTeacher() && tempFilter.in_archive!=1">
                        <div v-if="student.groups.length>0" style="display: inline">
                            <div v-for="(group, index) in student.groups" style="display: inline">
                                <b-popover triggers="hover" :content="rateInfo(group,student)">
                                    <span class="small text-muted" style="border-bottom:1px dotted #636C72; cursor: help">
                                    <span class="fa fa-bars"></span>
                                    {{ rate(group,student) }}
                                    </span>
                                </b-popover>
                            </div>
                        </div>
                        <span class="small text-muted" v-if="student.groups.length==0">нет</span>
                    </td>
                    <!-- Баланс: начало -->

                    <!-- Баланс - для текущих: начало -->
                    <td v-if="!$user.isTeacher() && tempFilter.in_archive!=1">
                        <div v-if="student.groups.length>0">
                            <div v-for="group in student.groups" style="display: inline">
                                <b-popover v-if="tempFilter.in_archive!=1" triggers="hover" :content="balanceInfo(group,student)">
                                    <span :class="{'text-success' : groupBalance(group,student) > 0 && (isBalanceFreeze(group,student)!=1 || !isBalanceFreeze(group,student)) , 'text-danger': groupBalance(group,student) < 0 && (isBalanceFreeze(group,student)!=1 || !isBalanceFreeze(group,student)), 'text-muted': groupBalance(group,student) == 0 && (isBalanceFreeze(group,student)!=1 || !isBalanceFreeze(group,student)), 'text-info': isBalanceFreeze(group,student) == 1}" style="border-bottom:1px dotted #5CB85C; cursor: help">
                                        {{ formatBalance(groupBalance(group,student)) }}
                                    </span>
                                </b-popover>
                            </div>
                        </div>
                        <span class="small text-muted" v-if="student.groups.length==0">нет тарифа</span>
                    </td>
                    <!-- Баланс - для текущих: конец -->

                    <!-- Баланс - для архивных: начало -->
                    <td v-if="!$user.isTeacher() && tempFilter.in_archive==1">
                        <div v-for="student_rate in student.student_rates" :class="{'text-success': student_rate.balance > 0 && (student_rate.is_balance_freeze != 1 || !student_rate.is_balance_freeze), 'text-danger': student_rate.balance < 0 && (student_rate.is_balance_freeze != 1 || !student_rate.is_balance_freeze), 'text-muted': student_rate.balance == 0 && (student_rate.is_balance_freeze != 1 || !student_rate.is_balance_freeze), 'text-info': student_rate.is_balance_freeze}">
                            {{ formatBalance(student_rate.balance) }}
                        </div>
                    </td>
                    <!-- Баланс - для архивных: конец -->

                    <!-- Баланс: конец -->

                    <td v-if="!$user.isTeacher() && tempFilter.in_archive==1">
                            <span >
                            {{ formatBalance(sumIncome(student)) }}
                            </span>
                    </td>
                    <td v-if="tempFilter.in_archive!=1">
                        <div v-if="student.groups.length>0">
                            <div v-for="group in student.groups">
                                <div class="row pl-3">
                                    <div class="col-xs-1 mr-1" v-for="lesson in attendancePills(group,student)" :key="lesson.id">
                                        <b-popover triggers="hover" :content="lessonInfo(lesson,student)">
                                            <span class="badge badge-pill p-1" :class="{'badge-default': !lesson.datetime, 'bg-faded': !lesson.datetime, 'badge-success': pillDanger(lesson,student.id)==0, 'badge-danger': pillDanger(lesson,student.id)==1, 'badge-warning': pillDanger(lesson,student.id)==2 }" style="font-size:1px;">&nbsp;</span>
                                        </b-popover>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row pl-3" v-html="emptyPills()">
                            </div>
                        </div>
                    </td>
                    <td v-if="tempFilter.in_archive!=1">
                        <div v-if="student.groups.length>0" style="display: inline">
                            <div v-for="(group, index) in student.groups" style="display: inline">
                                <b-popover v-if="!$user.isTeacher()" triggers="click">
                                    <span slot="content">
                                        <b>Отделение:</b> {{ group.office.name }}<br>
                                        <b>Преподаватель:</b> {{ group.teacher.user.name }}<br>
                                        <b>Тип занятий:</b> {{ group.lesson_type.name }}<br>
                                        <b>Расписание:</b> {{ $common.groupSchedule(group.schedules) }}<br>
                                        <div class='mt-2'>
                                            <a :href="groupLink(group.id)" class='btn btn-sm btn-primary'>открыть группу</a>
                                            <a href="javascript:void(0);" @click="deleteStudent(student.id, group.id)" class='btn btn-sm btn-outline-danger'>убрать</a>
                                        </div>
                                    </span>
                                    <span class="small text-muted" style="border-bottom:1px dotted #636C72; cursor: help">
                                    {{ group.id }}
                                    </span>
                                </b-popover>
                                <div v-else>
                                    <span class="small text-muted" style="border-bottom:1px dotted #636C72; cursor: help">
                                    {{ group.id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="small text-muted" v-if="student.groups.length==0">нет</span>
                    </td>
                    <td v-if="tempFilter.in_archive!=1">
                        <span :class="changeClass(student.next_lesson)" v-if="student.next_lesson && student.groups.length>0">{{ student.next_lesson | datetime }}</span>
                    </td>
                    <td v-if="tempFilter.in_archive!=1">
                        <span v-if="student.last_lesson">{{ student.last_lesson | datetime }}</span>
                    </td>
                    <td v-if="tempFilter.in_archive==1">
                        <span v-if="student.archive_reason">{{ student.archive_reason.name }}</span>
                    </td>
                    <td v-if="tempFilter.in_archive==1">
                        <span>{{ student.archive_date | datetime }}</span>
                    </td>
                    <td v-if="tempFilter.in_archive==1">
                        <span>{{ student.user.created_at | datetime }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="p-2 bg-faded" style="position: fixed; right: 20px; bottom: 20px; z-index: 99">
                <router-link :to="{name: 'group', params: {id: group_id }}" class="btn btn-lg btn-secondary mr-2">Отменить</router-link>
                <button class="btn btn-lg btn-primary" @click="send()" v-disabled="running"><span class="fa fa-check"></span> Готово</button>
            </div>
        </div>
        <b-modal ref="modalDeleteStudent" title="Подтвердите удаление">
            Вы действительно хотите удалить студента из группы?
            <div slot="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$refs.modalDeleteStudent.hide()">Отменить</button>
                <button type="button" class="btn btn-danger" @click="removeStudent()">Удалить</button>
            </div>
        </b-modal>
        <b-modal ref="errorModal" title="Ошибка">
            Cреди выбранных учеников не все имеют тариф с соответствующими группе параметрами. Пожалуйста, создайте тарифы с программой типом занятий, подходящими к группе.
            <div slot="modal-footer">
                <button class="btn btn-secondary" @click="$refs.errorModal.hide()">Отмена</button>
                <button @click="addRates()" class="btn btn-primary">Создать тарифы</button>
            </div>
        </b-modal>
        <student-rates-form @success="send()" :program_id="program_id" :lesson_type_id="lesson_type_id" ref="ratesForm" v-if="error" :students="error.callback" :data="$common.data"></student-rates-form>
    </div>
</template>


<script>

    import { get, del, post } from '../../helpers/api';
    import moment from 'moment';

    export default {
        props: ['group_id'],
        data() {
            return {
                running: false,
                load: false,
                scrollLoad: false,
                newStudent: '',
                students: [],
                student : '',
                total: {
                    current: 0,
                    archived: 0,
                    debtors: 0,
                    without_group: 0,
                    missing: 0,
                    inactive: 0
                },
                resource_url: '/api/students',
                next_url: '',
                default_url: '/api/students',
                loading: false,
                tempFilter: {
                    in_archive: '0',
                    is_debtor: false,
                    without_group: false,
                    is_missing: false,
                    is_inactive: false,
                    active: false,
                    sort: 'next',
                    reason: '',
                },
                firstLoad: false,
                tempClass: '',
                filterTemp: '',
                selectedStudentId: '',
                selectedGroupId: '',
                group: '',
                not_in_ids: [],
                selectStudentIds: [],
                error: '',
                program_id: '',
                lesson_type_id: '',
            }
        },
        components: {
            'students-filter': require('./StudentsFilter.vue'),
            'student-rates-form': require('./modals/StudentRatesForm.vue'),
        },
        watch: {
            group: function (group) {
                this.not_in_ids = group.student_ids;
            }
        },
        filters: {
            datetime: function(value) {
                moment.locale('ru');
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD MMM YYYY");
            }
        },

        mounted() {


        },

        methods: {
            send() {

                let _this = this;

                this.running = true;

                post(_this, '/api/group/' + this.group_id+'/add-students', {student_ids: this.selectStudentIds}, function (response) {
                    _this.$router.push({ name: 'group', params: { id: _this.group_id }});
                }, function (error) {
                    _this.error = error.response.data;
                    _this.$refs.errorModal.show();
                    _this.running = false;
                });

                settimeout(function() {this.running=false}, 2000)

            },
            getItem() {

                let _this = this;

                get(_this, '/api/group/' + this.group_id, {}, function (response) {
                    _this.group = response.data;
                    _this.program_id = response.data.program_id;
                    _this.lesson_type_id = response.data.lesson_type_id;
                })


            },
            studentLink(id) {
                return "/student/" + id ;
            },
            sumIncome(student) {
                var total = 0;
                if(student.balance_history.length > 0) {
                    student.balance_history.forEach(function(expense) {
                        if(expense.value > 0) total = total + expense.value;
                    });
                }
                return total;
            },
            getList() {


                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;

                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                if(this.students && this.students.length == 0) this.loading = true;
                if(this.students && this.next_url)this.loading = true;
                this.load = true;

                let _this = this;
                get(_this, '/api/count-students', {params: this.filterTemp}, function (response) {
                    _this.total = response.data;
                });

                get(_this, this.resource_url, {params: this.filterTemp}, function (response) {
                    let json = response.data;
                    _this.next_url = json.next_page_url;

                    _this.students = _this.students.concat(json.data);
                    _this.scrollLoad = false;
                    _this.load = false;
                    if(response.data) _this.loading = false;
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;
                });

            },
            routeQuery(event) {
                if(event) event.preventDefault();
                if(this.$route.query && this.$route.query.without_group && this.$route.query.without_group==true) {
                    this.tempFilter.without_group = true;
                }
                if(this.$route.query && this.$route.query.is_debtor && this.$route.query.is_debtor==true) {
                    this.tempFilter.is_debtor = true;
                }
                if(this.$route.query && this.$route.query.is_missing && this.$route.query.is_missing==true) {
                    this.tempFilter.is_missing = true;
                }
                if(this.$route.query && this.$route.query.is_inactive && this.$route.query.is_inactive==true) {
                    this.tempFilter.is_inactive = true;
                }
                if(this.$route.query && this.$route.query.in_archive && this.$route.query.in_archive==1) {
                    this.tempFilter.in_archive = 1;
                }
            },
            filtered() {
                this.resource_url = this.default_url;
                this.students = [];
                this.total = 0;
                this.filterTemp = this.$refs.filter.filterData;
                this.filterTemp.in_archive = this.tempFilter.in_archive;
                this.filterTemp.is_missing = this.tempFilter.is_missing;
                this.filterTemp.is_inactive = this.tempFilter.is_inactive;
                this.filterTemp.active = this.tempFilter.active;
                this.filterTemp.without_group = this.tempFilter.without_group;
                this.filterTemp.is_debtor = this.tempFilter.is_debtor;
                this.filterTemp.sort = this.tempFilter.sort;
                this.filterTemp.reason = this.tempFilter.reason;
                if(this.tempFilter.in_archive!=1) this.filterTemp.dateArchived = '';
                this.$nextTick(function () {
                    this.$router.push({ name: 'group-add-students', params: { group_id: this.group_id}, query: this.filterTemp });
                    this.getList();
                });

            },
            addMoney(student) {
                this.student = student;
                this.$nextTick(function () {
                    this.$refs.studentAddMoney.showModal();
                })
            },
            markLesson(student) {
                this.student = student;
                this.$nextTick(function () {
                    this.$refs.markLesson.showModal();
                })
            },
            handleScroll(e){
                let body = document.body,
                    html = document.documentElement;

                let height = Math.max( body.scrollHeight, html.scrollHeight);

                if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !this.scrollLoad) {
                    this.scrollLoad = true;
                    this.$nextTick(function () {
                        this.getList();
                    })
                }

            },

            changeClass(value) {

                return {
                    'text-danger' : moment(value, "YYYY-MM-DD hh:mm:ss") < moment().startOf('day'),
                }
            },

            setArchived(value) {
                if(this.tempFilter.in_archive != value) {
                    this.tempFilter.in_archive = value;
                    if(this.tempFilter.is_missing || this.tempFilter.without_group || this.tempFilter.is_inactive) {
                        this.tempFilter.is_missing = false;
                        this.tempFilter.without_group = false;
                        this.tempFilter.is_inactive = false;
                        this.tempFilter.active = false;
                    }
                    this.filtered();
                }
            },
            setWithout() {
                this.tempFilter.is_debtor = false;
                this.tempFilter.is_missing = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.active = false;
                this.tempFilter.without_group = true;
                this.filtered();
            },
            setMissing() {
                this.tempFilter.is_debtor = false;
                this.tempFilter.without_group = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.active = false;
                this.tempFilter.is_missing = true;
                this.filtered();
            },
            setInactive() {
                this.tempFilter.is_debtor = false;
                this.tempFilter.without_group = false;
                this.tempFilter.is_missing = false;
                this.tempFilter.active = false;
                this.tempFilter.is_inactive = true;
                this.filtered();
            },
            setDebtors() {
                this.tempFilter.is_debtor = true;
                this.tempFilter.without_group = false;
                this.tempFilter.is_missing = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.active = false;
                this.filtered();
            },
            setActive() {
                this.tempFilter.is_debtor = false;
                this.tempFilter.without_group = false;
                this.tempFilter.is_missing = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.active = true;
                this.filtered();
            },
            toList() {
                this.tempFilter.is_debtor = false;
                this.tempFilter.without_group = false;
                this.tempFilter.is_missing = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.active = false;
                this.filtered();
            },
            itemLink(id) {
                return "/student/"+id;
            },
            groupBalance(group,student) {
                let found = false;
                let selectedRate = '';
                let value = 'не найден';
                student.student_rates.forEach(function(student_rate) {
                    if(!found && (student_rate.program_id === group.program_id) && (student_rate.lesson_type_id === group.lesson_type_id)) {
                        found = true;
                        selectedRate = student_rate;
                    }
                });
                if(selectedRate) value = selectedRate.balance;

                return value;

            },
            isBalanceFreeze(group,student) {
                let found = false;
                let selectedRate = '';
                let value = null;
                student.student_rates.forEach(function(student_rate) {
                    if(!found && (student_rate.program_id === group.program_id) && (student_rate.lesson_type_id === group.lesson_type_id)) {
                        found = true;
                        selectedRate = student_rate;
                    }
                });
                if(selectedRate) value = selectedRate.is_balance_freeze;

                return value;
            },
            rate(group,student) {
                let found = false;
                let selectedRate = '';
                let value = "не найден";
                student.student_rates.forEach(function(student_rate) {
                    if(!found && (student_rate.program_id === group.program_id) && (student_rate.lesson_type_id === group.lesson_type_id)) {
                        found = true;
                        selectedRate = student_rate;
                    }
                });

                if(selectedRate) value = Math.floor(selectedRate.cost / selectedRate.count_lessons);

                return value;

            },
            rateInfo(group,student) {

                let found = false;
                let selectedRate = '';
                let html = '';
                let cost = 0;
                let countLesson = 0;
                let duration = 0;
                let total = 0;
                student.student_rates.forEach(function(student_rate) {
                    if(!found && (student_rate.program_id === group.program_id) && (student_rate.lesson_type_id === group.lesson_type_id)) {
                        found = true;
                        selectedRate = student_rate;
                    }
                });

                if(selectedRate) {
                    cost = selectedRate.cost;
                    countLesson = selectedRate.count_lessons;
                    duration = selectedRate.duration;
                    total = Math.floor(selectedRate.cost / selectedRate.count_lessons);
                    html = "<small class='h6 mb-4 text-muted'>"+selectedRate.program.name+"</small><br>";
                    html += "<b>Тип занятий:</b> "+selectedRate.lesson_type.name+"<br>";
                    html += "<b>Стоимость в месяц:</b> "+cost+"<br>";
                    html += "<b>Занятий в месяц:</b> "+countLesson+"<br>";
                    html += "<b>Минут в занятии:</b> "+duration+"<br>";
                    html += "<b class='text-primary'>Стоимость занятия: "+total+"</b>"
                } else {
                    html = "Тариф для группы №" + group.id + " не найден";
                }

                return html;
            },
            formatBalance(value) {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            },
            balanceInfo(group, student) {
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
                                    next_income = moment(next_income_temp).format("DD.MM.YY");
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
                        if(student.groups.length==0) {
                            html +=  "<b>След. поплнение:</b> ? (нет группы)<hr class='my-1'>";
                        }
                    }
                }
                html += "<b>Оплачено в этом месяце:</b> " + this.formatBalance(month_expense) + "<br>";
                html += "<b>Всего оплачено:</b> " + this.formatBalance(total_expense);
                return html;
            },
            groupLink(id) {
                return "/group/" + id;
            },
            current_sort() {
                if(this.tempFilter.sort=='id') return 'по дате добавления';
                if(this.tempFilter.sort=='next') return 'по дате след. занятия';
                if(this.tempFilter.sort=='last') return 'по дате послед. посещения';
            },
            sort(value) {
                this.tempFilter.sort = value;
                this.filtered()
            },
            declOfNum(number) {
                var titles = ['занятие', 'занятия', 'занятий'];
                var cases = [2, 0, 1, 1, 1, 2];
                return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
            },
            attendancePills(group,student) {
                var date = '';
                var lessons = [];
                var array = [];
                var tempLesson = '';
                student.history.forEach(function(event) {
                    if(!date && event.value == group.id) date = event.created_at;
                });
                group.lessons.forEach(function(lesson) {
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
            emptyPills() {
                var html='';
                for(var i = 0; i <= 11; i++) {
                    html += "<div class='col-xs-1 mr-1'><span class='badge badge-pill p-1 badge-default bg-faded' style='font-size: 1px;'>&nbsp;</span></div>"
                }
                return html;
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

            deleteStudent(student_id, group_id) {
                this.selectedStudentId = student_id;
                this.selectedGroupId = group_id;
                this.$refs.modalDeleteStudent.show();
            },
            removeStudent() {

                let _this = this;

                del(_this, '/api/group/'+this.selectedGroupId+'/delete-student/'+this.selectedStudentId, {}, function (response) {
                    _this.$refs.modalDeleteStudent.hide();
                    _this.getList();
                });
            },
            currentReason() {
                let comp = this;
                var name = "Все причины";
                this.$common.data.archive_reasons.forEach(function(reason) {
                    if(comp.tempFilter.reason==reason.id) name = reason.name;
                });
                return name;
            },
            filterByReason(value) {
                this.tempFilter.reason = value;
                this.filtered();
            },
            addRates() {
                this.$refs.errorModal.hide();
                this.$refs.ratesForm.showModal();
            }
        },

        created() {

            this.getItem();
            window.document.body.onscroll = this.handleScroll;
        }

    }

</script>