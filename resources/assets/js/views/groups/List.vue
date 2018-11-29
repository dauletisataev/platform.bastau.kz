<template>
    <div v-if="$root.user">
        <!-- Поиск -->
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner" style="z-index:3005; left: 15px">
                <clip-loader :size="'70px'" :color="'#0275d8'" ></clip-loader>
            </div>
        </div>
        <!-- Результаты -->
        <div class="col-10 offset-2">
            <div class="row">
                <group-filter v-if="$common.data.offices" :load="load" ref="filter" v-on:filtered="filtered"></group-filter>
                <div class="col-2">
                    <button v-if="!$user.isTeacher() && !$user.isAccountant()" class="btn btn-primary btn-block py-1" @click="$refs.newGroup.showModal()"><span class="d-block font-weight-bold">создать</span> <small class="d-block text-info" style="margin-top:-7px">новую группу</small></button>
                </div>
            </div>

            <div class="clearfix">
                <div ref="filter1" style="display: inline">
                    <b-dropdown @click="toList" id="ddown-split" size="sm" split class="m-1">
                        <template slot="text">
                            {{ tempFilter.in_archive == 1 ? 'Архивные' : 'Текущие' }} <span class='badge badge-pill ml-1' :class="{'badge-default': (!tempFilter.without_students && !tempFilter.is_inactive && !tempFilter.incomplete), 'badge-danger': (tempFilter.without_students || tempFilter.is_inactive || tempFilter.incomplete) }">{{ total.current }}</span>
                        </template>
                        <b-dropdown-item href="#" @click="setArchived(0)">Текущие</b-dropdown-item>
                        <b-dropdown-item href="#" @click="setArchived(1)">Архивные</b-dropdown-item>
                    </b-dropdown>
                </div>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setWithout" :class="{'active': tempFilter.without_students }">
                    Без учеников <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.without_students, 'badge-danger': !tempFilter.without_students }">{{ total.without_students }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setInactive" :class="{'active': tempFilter.is_inactive }">
                    Неактивные <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.is_inactive, 'badge-danger': !tempFilter.is_inactive }">{{ total.inactive }}</span>
                </b-button>
                <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setIncomplete" :class="{'active': tempFilter.incomplete }">
                    Неполные <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.incomplete, 'badge-danger': !tempFilter.incomplete }">{{ total.incomplete }}</span>
                </b-button>
                <span class="btn-group btn-group-sm mt-1 mb-1 ml-2 pull-right btngrouptabs">
                    <button class="btn btn-secondary" :class="{ active: view=='grid'}" @click="toGridView()">
                        <span class="fa fa-th-large"></span>
                    </button>
                    <button class="btn btn-secondary" :class="{ active: view=='table'}" @click="toTableView()">
                        <span class="fa fa-bars"></span>
                    </button>
                </span>
                <b-dropdown @click="" size="sm" right class="m-1 pull-right">
                    <template slot="text">
                        <span class="fa fa-sort-amount-asc mr-1"></span>
                        {{ current_sort() }}
                        <span class="fa fa-angle-down ml-1"></span>
                    </template>
                    <b-dropdown-item :disabled="tempFilter.sort=='next'" @click="sort('next')">по дате след. занятия</b-dropdown-item>
                    <b-dropdown-item :disabled="tempFilter.sort=='id'" @click="sort('id')">по дате добавления</b-dropdown-item>
                    <b-dropdown-item :disabled="tempFilter.sort=='last'" @click="sort('last')">по дате послед. занятия</b-dropdown-item>
                </b-dropdown>
            </div>

            <div class="row mt-4">
                <div class="card w-100 mx-3" v-if="view=='table'">
                    <table class="table mb-0">
                        <thead class="thead-default h6">
                            <tr>
                                <th>Программа</th>
                                <th>Уровень</th>
                                <th>След. занятие</th>
                                <th>Преподаватель</th>
                                <th v-if="tempFilter.in_archive==1">Дата архивации</th>
                                <th v-else>Студенты</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="group in groups">
                                <td>
                                    {{ group.program.name }} {{ getLessonTypeString(group.lesson_type_id) }}
                                </td>
                                <td>
                                    {{ group.level.short_name }}
                                </td>
                                <td>
                                    <span v-if="group.next_lesson" :class="changeClass(group.next_lesson)">{{ group.next_lesson | datetime }}</span>
                                    <span v-else :class="changeClass(group.next_lesson)">не добавлено</span>
                                </td>
                                <td style="text-overflow:ellipsis;word-wrap:break-word;overflow:hidden;max-width:200px;white-space:nowrap">
                                    <span class="h6">
                                        {{ group.teacher ? group.teacher.user.name : 'Нет преподавателя'  }}
                                    </span>
                                </td>
                                <td v-if="tempFilter.in_archive==1">
                                    <span v-if="group.archive_date">{{ group.archive_date | datetime }}</span>
                                </td>
                                <td v-else style="text-overflow:ellipsis;word-wrap:break-word;overflow:hidden;max-width:300px;white-space:nowrap">
                                    <b-tooltip placement="left" style="display: inline; z-index: 99; position: relative;" :content="getHtmlListStudents(group.students)"><span class="badge mr-1" :class="{'badge-default': countStudents(group.students)>0, 'badge-danger': countStudents(group.students)===0}">{{ countStudents(group.students) }}</span></b-tooltip>
                                    <span v-if="trueStudents(group.students) && countTrueStudents(group.students)>2">
                                        {{ firstStudentName(group.students) }}
                                        <span class="fa fa-ellipsis-h mx-1" style="color:#DFDFDF;"></span>
                                        {{ lastStudentName(group.students) }}
                                    </span>
                                    <span v-if="trueStudents(group.students) && countTrueStudents(group.students)==2">
                                        {{ firstStudentName(group.students) }}, {{ secondStudentName(group.students) }}
                                    </span>
                                    <span v-if="trueStudents(group.students) && countTrueStudents(group.students)==1">
                                        {{ firstStudentName(group.students) }}
                                    </span>
                                </td>
                                <td>
                                    <b-tooltip content="Открыть группу" style="display:inline-block">
                                        <router-link :to="{ name: 'group', params: { id: group.id }}" class="btn btn-outline-primary btn-sm"><span class="fa fa-eye"></span></router-link>
                                    </b-tooltip>
                                    <b-dropdown size="sm" right style="display:inline-block;">
                                        <template slot="text">
                                            <span class="fa fa-ellipsis-v"></span>
                                        </template>
                                        <b-dropdown-item v-if="!$user.isTeacher()"><router-link :to="{ name: 'group-add-students', params: { group_id: group.id }}" tag="span"><span class="fa fa-fw fa-plus mr-2"></span> добавить ученика в группу</router-link></b-dropdown-item>
                                        <b-dropdown-item v-if="!$user.isAccountant()" @click="markLessons(group)"><span class="fa fa-fw fa-check mr-2" ></span> отметить занятие</b-dropdown-item>
                                    </b-dropdown>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-4" v-for="group in groups" v-if="view=='grid'">
                    <div class="card mb-4">
                        <router-link :to="{ name: 'group', params: { id: group.id }}" class="card-full-link">
                        </router-link>
                            <div class="card-block">
                                <div class="clearfix">
                                    <div class="lead pull-left">
                                        {{ $common.groupSchedule(group.schedules) }}
                                    </div>
                                    <div class="pull-right" ref="menu">
                                        <b-dropdown size="sm" right style="z-index: 99;">
                                            <template slot="text">
                                                <span class="fa fa-ellipsis-v"></span>
                                            </template>
                                            <b-dropdown-item v-if="!$user.isTeacher()"><router-link :to="{ name: 'group-add-students', params: { group_id: group.id }}" tag="span"><span class="fa fa-fw fa-plus mr-2"></span> добавить ученика в группу</router-link></b-dropdown-item>
                                            <b-dropdown-item v-if="!$user.isAccountant()" @click="markLessons(group)"><span class="fa fa-fw fa-check mr-2" ></span> отметить занятие</b-dropdown-item>
                                        </b-dropdown>
                                    </div>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar" v-bind:class="$common.getProgressClass(group.progress)" role="progressbar" :style="{width: group.progress+'%', height: '3px'}" :aria-valuenow="group.progress" aria-valuemin="0" aria-valuemax="100">{{group.progress}}%</div>
                                </div>
                                <div class="mt-3">
                                    След. занятие: <span v-if="group.next_lesson" :class="changeClass(group.next_lesson)">{{ group.next_lesson | datetime }}</span>
                                    <span v-else :class="changeClass(group.next_lesson)">не добавлено</span>
                                </div>
                                <div>
                                    Послед. занятие: <span v-if="group.last_lesson">{{ group.last_lesson | datetime }}</span>
                                    <span v-else :class="changeClass(group.last_lesson)">не проведено</span>
                                </div>
                                <div class="text-muted mb-2">
                                    {{ group.program.name }}<br>
                                    {{ group.level.name }}<br>
                                    {{ group.lesson_type.name }} <b-tooltip v-if="tempFilter.in_archive!=1" placement="left" style="display: inline; z-index: 99; position: relative;" :content="getHtmlListStudents(group.students)"><span class="badge ml-1"  :class="{'badge-default': countStudents(group.students)>0, 'badge-danger': countStudents(group.students)===0}">{{ countStudents(group.students) }}</span></b-tooltip>
                                </div>
                                <div v-if="tempFilter.in_archive==1 && group.archive_date">
                                    Дата архивации: {{ group.archive_date | datetime }}
                                </div>
                                <h6 v-if="!$user.isTeacher()" :class="{'text-danger': !group.teacher}">{{ group.teacher ? group.teacher.user.name : 'Нет преподавателя'  }}</h6>
                            </div>

                    </div>
                </div>
            </div>
            <group-form ref="newGroup" :data="$common.data" :_form="newGroup" v-on:formSending="filtered"></group-form>
            <show-schedules ref="showSchedules" :schedules="schedules"></show-schedules>
            <group-mark-lesson ref="markLesson" v-if="group" :lessons="group.lessons" :students="group.students" v-on:formSending="filtered"></group-mark-lesson>
        </div>
    </div>
</template>


<script>

    import { get,post } from '../../helpers/api';
    import moment from 'moment';

    export default {


        props: ['teacher_id'],

        data() {
            return {
                schedules: [],
                scrollLoad: false,
                load: false,
                resource_url: '/api/groups',
                next_url: '',
                default_url: '/api/groups',
                newGroup: '',
                groups: [],
                group: '',
                loading: false,
                total: {
                    current: 0,
                    archived: 0,
                    without_students: 0,
                    incomplete: 0,
                    inactive: 0
                },
                tempFilter: {
                    in_archive: '0',
                    without_students: false,
                    incomplete: false,
                    is_inactive: false,
                    sort: 'next',
                },
                filterTemp: '',
                firstLoad: false,
                tempClass: '',
                view: '',
            }
        },
        components: {
            'group-form': require('./Form.vue'),
            'group-filter': require('./Filter.vue'),
            'group-mark-lesson': require('./MarkLessonInList.vue'),
            'show-schedules': require('./../../components/ShowSchedules.vue')
        },
        filters: {
            datetime: function(value) {
                moment.locale('ru');
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD MMM YYYY");
            }
        },
        updated() {
            if(!this.firstLoad) {
                this.tempClass = this.$refs.filter1.firstChild.firstChild.className;
                this.firstLoad = true;
            }
            if(!this.tempFilter.without_students && !this.tempFilter.incomplete && !this.tempFilter.is_inactive) {
                this.$refs.filter1.firstChild.firstChild.className = this.tempClass + " active";
            } else {
                this.$refs.filter1.firstChild.firstChild.className = this.tempClass;
            }
            if(!this.view) {
                this.view = this.$root.user ? this.$root.user.getGroupsView() : 'grid';
            }
        },
        methods: {
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                if(this.groups && this.groups.length == 0) this.loading = true;
                if(this.groups && this.next_url)this.loading = true;
                this.load = true;

                let _this = this;
                get(_this, '/api/count-groups', {params: this.filterTemp}, function (response) {
                    _this.total = response.data;
                });
                get(_this, this.resource_url, {params: this.filterTemp}, function (response) {
                    let json = response.data;
                    _this.next_url = json.next_page_url;
                    _this.groups = _this.groups.concat(json.data);
                    _this.scrollLoad = false;
                    _this.load = false;
                    if (response.data) _this.loading = false;
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;
                });

            },
            filtered() {
                this.resource_url = this.default_url;
                this.groups = [];
                this.filterTemp = this.$refs.filter.filterData;
                this.filterTemp.in_archive = this.tempFilter.in_archive;
                this.filterTemp.is_inactive = this.tempFilter.is_inactive;
                this.filterTemp.without_students = this.tempFilter.without_students;
                this.filterTemp.incomplete = this.tempFilter.incomplete;
                this.filterTemp.sort = this.tempFilter.sort;

                this.$nextTick(function () {
                    this.$router.push({ path: '/groups', query: this.filterTemp });
                    this.getList();
                });

            },
            markLessons(group) {
//                this.group = group;
                let _this = this;
                get(_this, '/api/group-mark-info/' + group.id, {}, function (response) {
                    _this.group = response.data.data;
                    _this.$nextTick(function () {
                        _this.$refs.markLesson.showModal();
                    });
                });
            },
            showSchedules(schedules) {
                this.schedules = schedules;
                this.$nextTick(function () {
                    this.$refs.showSchedules.showModal();
                });
            },
            getHtmlListStudents(students)
            {
                let html = "<ul class='list-group'>";

                students.forEach(function (student) {
                    if((student.in_archive!=1 || student.in_archive==null) && student.pivot.deleted==0)
                        html += '<li>'+student.user.name+'</li>';
                });

                return html+"</ul>";
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
                    'font-weight-normal text-danger' : moment(value, "YYYY-MM-DD HH:mm:ss") < moment().startOf('day'),
                    'font-weight-normal text-warning' : moment(value, "YYYY-MM-DD HH:mm:ss") >= moment().startOf('day') && moment(value, "YYYY-MM-DD HH:mm:ss") <= moment().endOf('day'),
                    'font-weight-normal' : moment(value, "YYYY-MM-DD HH:mm:ss") > moment().endOf('day'),
                    'text-danger' : (value == null || value == "")
                }
            },

            countStudents(students) {
                var count = 0;
                students.forEach(function(student) {
                    if((student.in_archive!=1 || student.in_archive == null) && student.pivot.deleted==0) {
                        count++;
                    }
                });
                return count;
            },


            setArchived(value) {
                if(this.tempFilter.in_archive != value) {
                    this.tempFilter.in_archive = value;
                    if(this.tempFilter.incomplete || this.tempFilter.without_students || this.tempFilter.is_inactive) {
                        this.tempFilter.incomplete = false;
                        this.tempFilter.is_inactive = false;
                        this.tempFilter.without_students = false;
                    }
                    this.filtered();
                }
            },
            setWithout() {
                this.tempFilter.incomplete = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.without_students = true;
                this.filtered();
            },
            setInactive() {
                this.tempFilter.incomplete = false;
                this.tempFilter.is_inactive = true;
                this.tempFilter.without_students = false;
                this.filtered();
            },
            setIncomplete() {
                this.tempFilter.incomplete = true;
                this.tempFilter.is_inactive = false;
                this.tempFilter.without_students = false;
                this.filtered();
            },
            toList() {
                this.tempFilter.incomplete = false;
                this.tempFilter.is_inactive = false;
                this.tempFilter.without_students = false;
                this.filtered();
            },
            current_sort() {
                if(this.tempFilter.sort=='id') return 'по дате добавления';
                if(this.tempFilter.sort=='next') return 'по дате след. занятия';
                if(this.tempFilter.sort=='last') return 'по дате послед. занятия';
            },
            sort(value) {
                this.tempFilter.sort = value;
                this.filtered()
            },
            getLessonTypeString(value) {
                var str = '';
                switch(value) {
                    case 1: str = 'в группе';
                    break;
                    case 2: str = 'индивидуально';
                    break;
                    case 3: str = 'в паре';
                    break;
                }
                return str;
            },
            toTableView() {
                this.view = 'table';
                let _this = this;
                this.$root.user.data.view_groups = 1;
                post(_this, 'api/change-view-groups/'+this.$root.user.getId(), {}, function (response) {});
            },
            toGridView() {
                this.view = 'grid';
                let _this = this;
                this.$root.user.data.view_groups = 0;
                post(_this, 'api/change-view-groups/'+this.$root.user.getId(), {}, function (response) {});
            },
            trueStudents(students) {
                var arr = [];
                students.forEach(function (student) {
                    if((student.in_archive!=1 || student.in_archive==null) && student.pivot.deleted==0)
                        arr.push(student);
                });
                return arr;
            },
            countTrueStudents(students) {
                var arr = this.trueStudents(students);
                var count = arr ? arr.length : 0;
                return count;
            },
            firstStudentName(students) {
                var arr = this.trueStudents(students);
                return (arr && arr[0] && arr[0].user) ? arr[0].user.name : '';
            },
            lastStudentName(students) {
                var arr = this.trueStudents(students);
                return (arr && arr[arr.length-1] && arr[arr.length-1].user) ? arr[arr.length-1].user.name : '';
            },
            secondStudentName(students) {
                var arr = this.trueStudents(students);
                return (arr && arr[1] && arr[1].user) ? arr[1].user.name : '';
            }
        },

        created() {
            window.document.body.onscroll = this.handleScroll;
        }

    }

</script>