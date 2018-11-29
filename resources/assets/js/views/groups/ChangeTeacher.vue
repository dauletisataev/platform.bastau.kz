<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner" style="z-index:3005; left: 15px">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2">
            <div class="row">
                <teacher-filter v-if="$common.data.offices && group" ref="filter" :not_in_id="not_in_id"  :load="load" v-on:filtered="filtered"></teacher-filter>
            </div>
            <div class="clearfix">
                <!-- Доп. фильтр  -->
                <div ref="filter1" style="display: inline">
                    <b-button size="sm" @click="toList" :class="{'active': !tempFilter.without_group && !tempFilter.is_inactive }">
                        Все <span class='badge badge-pill ml-1' :class="{'badge-default': !tempFilter.without_group && !tempFilter.is_inactive, 'badge-danger': tempFilter.without_group || tempFilter.is_inactive }">{{ total.current }}</span>
                    </b-button>
                    <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setWithout" :class="{'active': tempFilter.without_group }">
                        Без групп <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.without_group, 'badge-danger': !tempFilter.without_group }">{{ total.without_group }}</span>
                    </b-button>
                    <b-button v-if="tempFilter.in_archive==0" size="sm" @click="setInactive" :class="{'active': tempFilter.is_inactive }">
                        Неактивные <span class='badge badge-pill ml-1' :class="{'badge-default': tempFilter.is_inactive, 'badge-danger': !tempFilter.is_inactive }">{{ total.inactive }}</span>
                    </b-button>
                    <b-dropdown v-if="tempFilter.in_archive==0" @click="" size="sm" right class="m-1 pull-right">
                        <template slot="text">
                            <span class="fa fa-sort-amount-asc mr-1"></span>
                            {{ current_sort() }}
                            <span class="fa fa-angle-down ml-1"></span>
                        </template>
                        <b-dropdown-item :disabled="tempFilter.sort=='id'" @click="sort('id')">по дате добавления</b-dropdown-item>
                        <b-dropdown-item :disabled="tempFilter.sort=='last'" @click="sort('last')">по дате послед. занятия</b-dropdown-item>
                    </b-dropdown>
                </div>
            </div>
            <div class="row my-4">
                <div class="card w-100 mx-3">
                    <table class="table mb-0">
                        <thead class="thead-default h6">
                        <tr>
                            <th>Имя</th>
                            <th v-if="tempFilter.in_archive != 1">Студенты</th>
                            <th v-if="tempFilter.in_archive != 1">Удержание</th>
                            <th v-if="tempFilter.in_archive != 1">Часов</th>
                            <th v-if="$user.isAdmin()">Платежи</th>
                            <th v-if="tempFilter.in_archive == 1">Причина архивации</th>
                            <th v-if="tempFilter.in_archive == 1">Дата архивации</th>
                            <th>Посл. занятие</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="teacher in teachers">
                            <td><label class="custom-control custom-radio"> <input v-model="selectTeacher" :value="teacher.id" type="radio" name="teacher" class="custom-control-input studentsTimeDay"> <span class="custom-control-indicator"></span> <span class="h6">{{ teacher.user.name }}</span></label></td>
                            <td v-if="!teacher.statistics" colspan="4">
                                <clip-loader v-show="!teacher.statistics" :size="'35px'" :color="'#0275d8'"></clip-loader>
                            </td>
                            <td v-if="tempFilter.in_archive != 1 && teacher.statistics">
                                <router-link :to="{path:'/students', query: { teacher_id: teacher.id, in_archive: 0 }}" v-if="filterTemp.dateRange && filterTemp.dateRange.substring(11) == today">{{ teacher.statistics.count_students }}</router-link>
                                <span v-else class="text-muted">{{ teacher.statistics.count_students }}</span> <span class="text-success">+{{ teacher.statistics.count_new_students }}</span> <span class="text-danger">-{{ teacher.statistics.count_departed_students }}</span>
                            </td>
                            <td v-if="tempFilter.in_archive != 1 && teacher.statistics">
                                {{ teacher.statistics.retardation }}%

                            </td>
                            <td v-if="tempFilter.in_archive != 1 && teacher.statistics">
                                {{ teacher.statistics.hours }}

                            </td>
                            <td v-if="$user.isAdmin() && teacher.statistics">
                                {{ $common.formatPrice(teacher.statistics.transactions) }} ₸
                            </td>
                            <td v-if="tempFilter.in_archive == 1">{{ teacher.archive_reason ? teacher.archive_reason.name : "не указано" }}</td>
                            <td v-if="tempFilter.in_archive == 1">
                                <span v-if="teacher.archive_date">{{ teacher.archive_date | datetime }}</span>
                                <span v-else>не указано</span>
                            </td>
                            <td><span :class="changeClass(teacher.last_lesson)" v-if="teacher.last_lesson">{{ teacher.last_lesson | datetime }}</span>
                                <span v-else :class="changeClass(teacher.last_lesson)">не проведено</span></td>
                            <td class="text-danger" style="font-weight:500">
                                <span v-if="countProblems(teacher)>0" class="fa fa-exclamation-triangle"></span>
                                <span v-if="countProblems(teacher)>0">{{ countProblems(teacher) }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="p-2 bg-faded" style="position: fixed; right: 20px; bottom: 20px;">
            <router-link :to="{name: 'group', params: {id: group_id }}" class="btn btn-lg btn-secondary mr-2">Отменить</router-link>
            <button :disabled="formSending" class="btn btn-lg btn-primary" @click="send()"><i v-show="formSending" class="fa fa-spinner fa-spin"></i><span class="fa fa-check"></span> Готово</button>
        </div>
    </div>


</template>

<script>

    import { get, post } from '../../helpers/api';
    import moment from 'moment';

    export default {
        props: ['group_id'],

        data() {
            return {
                load: false,
                scrollLoad: false,
                resource_url: '/api/teachers',
                next_url: '',
                default_url: '/api/teachers',
                not_in_id: '',
                teachers: [],
                group: '',
                selectTeacher: '',
                total: {
                    current: 0,
                    archived: 0,
                    without_group: 0,
                    inactive: 0
                },
                tempFilter: {
                    in_archive: '0',
                    without_group: false,
                    is_inactive: false,
                    sort: 'id',
                },
                filterTemp: '',
                firstLoad: false,
                tempClass: '',
                today: moment().format('DD.MM.YY'),
                loading: true,
                formSending: false,
            }
        },
        components: {
            'teacher-filter': require('./TeachersFilter.vue'),

        },
        filters: {
            datetime: function(value) {
                moment.locale('ru');
                return moment(value, "YYYY-MM-DD HH:mm:ss").format("DD MMM YYYY");
            }
        },
        watch: {
            group: function (group) {
                this.not_in_id = group.teacher_id;
            },
            teachers(arr) {
                let _this = this;
                if(this.teachers.length > 0) {
                    this.teachers.forEach(function(teacher,index) {
                        setTimeout(function () {
                            if(!teacher.statistics && !teacher.loaded) {
                                Vue.set(teacher,'loaded',true);
                                Vue.set(teacher,'statistics',_this.getStats(teacher.id));
                            }
                        },index*500);
                    });
                }
            }
        },
        methods: {
            send() {

                let _this = this;
                this.formSending = true;
                post(_this, '/api/group/' + this.group_id+'/change-teacher', {teacher_id: this.selectTeacher}, function (response) {
                    _this.$router.push({ name: 'group', params: { id: _this.group_id }});
                }, function (error) {
                    _this.formSending = false;
                });



            },
            getItem() {

                let _this = this;

                get(_this, '/api/group/' + this.group_id, {}, function (response) {
                    _this.group = response.data;
                })

            },
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if(this.teachers && this.teachers.length == 0) this.loading = true;
                if(this.teachers && this.next_url)this.loading = true;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;

                // Счетчик доп. фильтров

                get(_this, '/api/count-teachers', {params: this.filterTemp}, function (response) {
                    _this.total = response.data;
                });

                get(_this, this.resource_url, {params: this.filterTemp}, function (response) {
                    let json = response.data;
                    _this.next_url = json.next_page_url;
                    _this.teachers = _this.teachers.concat(json.data);
                    _this.scrollLoad = false;
                    _this.load = false;
                    _this.loading = false;
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;
                    _this.loading = false;
                });

            },

            getStats(id) {
                let filterStats = JSON.parse(JSON.stringify(this.filterTemp));
                filterStats.id = id;
                let _this = this;
                get(_this, '/api/teacher-statistics', {params: filterStats}, function(response) {
                    _this.teachers.forEach(function(teacher) {
                        if(teacher.id === id) {
                            teacher.statistics = response.data;
                        }
                    });
                });
            },

            filtered() {
                this.resource_url = this.default_url;
                this.teachers = [];
                this.total = 0;
                this.filterTemp = this.$refs.filter.filterData;
                this.filterTemp.in_archive = this.tempFilter.in_archive;
                this.filterTemp.is_inactive = this.tempFilter.is_inactive;
                this.filterTemp.without_group = this.tempFilter.without_group;
                this.filterTemp.sort = this.tempFilter.sort;
                this.filterTemp.not_in_id = this.not_in_id;

                this.$nextTick(function () {
                    this.$router.push({ name: 'group-change-teacher', params:{group_id: this.group_id}, query: this.filterTemp });
                    this.getList();
                });

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
            setWithout() {
                this.tempFilter.is_inactive = false;
                this.tempFilter.without_group = true;
                this.filtered();
            },
            setInactive() {
                this.tempFilter.is_inactive = true;
                this.tempFilter.without_group = false;
                this.filtered();
            },
            toList() {
                this.tempFilter.is_inactive = false;
                this.tempFilter.without_group = false;
                this.filtered();
            },
            current_sort() {
                if(this.tempFilter.sort=='id') return 'по дате добавления';
                if(this.tempFilter.sort=='last') return 'по дате послед. занятия';
            },
            sort(value) {
                this.tempFilter.sort = value;
                this.filtered()
            },
            changeClass(value) {
                return {
                    'text-danger' : (value == null || value == "" || (value && moment(value, "YYYY-MM-DD HH:mm:ss") < moment().subtract(7,'days').startOf('day')))
                }
            },
            countProblems(teacher) {
                let count = 0;
                if(teacher.problems.groups_without.length>0) count++;
                if(teacher.problems.inactive_groups.length>0) count++;
                if(teacher.problems.unmarked_lessons.length>0) count++;
                return count;
            }


        },

        created() {
            this.getItem();
            window.document.body.onscroll = this.handleScroll;
        }
    }
</script>