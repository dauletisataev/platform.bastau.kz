<template>
    <div class="container-fluid">
        <holiday-filter ref="filter" :load="load" v-on:filtered="filtered"></holiday-filter>
        <!-- Результаты -->
        <div class="col-10 offset-2 mt-2 " v-if="$refs.filter">
            <div class="row">
                <div class="col h2"  v-if="$refs.filter.filterData.month===''">{{$tc("holidays.list.all_holidays")}}</div>
                <div class="col h2" v-else>{{$tc("holidays.list.holidays_for_"+$refs.filter.filterData.month)}}</div>
                <div class="col">
                    <button class="btn btn-primary btn-sm float-right"
                            @click="$refs.newHoliday.showModal($refs.filter && $refs.filter.filterData.month
                              ? $refs.filter.filterData.month:'all')">
                        {{$tc('holidays.list.create_holiday')}}
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <ul >
                    <li  class="list-group-item" style="cursor:default">
                        <div class="col-5">
                            {{$tc('holidays.list.name') }}
                        </div>
                        <div class="col-3">
                            {{$tc('holidays.list.start_date')}}
                        </div>
                        <div class="col-2">
                            {{$tc('holidays.list.days_number')}}
                        </div>
                    </li>
                    <li v-for="holiday in holidays"  class="list-group-item" style="cursor:default">
                        <div class="col-5">
                            {{holiday.name}}
                        </div>
                        <div class="col-3">
                            {{holiday.start_day}}
                            {{$tc('holidays.list.dates.'+months[holiday.start_month])}}
                        </div>
                        <div class="col-2">
                            {{holiday.days_number}}
                        </div>
                        <div class="col-2 text-right">
                            <button class="btn btn-sm btn-secondary" @click="$refs.newHoliday.showModal(holiday)" >
                                <span class="fa fa-pencil" ></span>
                            </button>
                            <button @click="remove(holiday.id)" class="btn btn-sm btn-danger">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <holiday-form ref="newHoliday" :data="$common.data" :_form="newHoliday" v-on:formSending="filtered"></holiday-form>

    </div>
</template>

<script>

    import { get,post } from '../../../../../helpers/api'

    export default {

        data() {
            return {
                load: false,
                scrollLoad: false,
                newHoliday: '',
                holidays: [],
                months:[
                    '','january','february','march','april',
                    'may','june','july','august','september',
                    'october','november','december'
                ],
                total: 0,
                resource_url: '/api/holidays',
                next_url: '',
                default_url: '/api/holidays'
            }
        },
        components: {
            'holiday-form': require('./Form.vue'),
            'holiday-filter': require('./Filter.vue')
        },
        methods: {
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;
                get(_this, this.resource_url, {params: this.filterData}, function (response) {

                    let json = response.data;

                    _this.next_url = json.next_page_url;

                    _this.holidays = _this.holidays.concat(json.data);

                    _this.total = json.total;

                    _this.scrollLoad = false;
                    _this.load = false;

                }, function () {

                    _this.scrollLoad = false;
                    _this.load = false;

                });

            },
            filtered() {
                this.resource_url = this.default_url;
                this.holidays = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;

                this.$nextTick(function () {
                    this.$router.push({ path: '/control/holidays', query: this.filterData });
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
            remove(id){
                let form={};
                let _this=this;
                post(_this, '/api/holiday/delete/'+id, form, function () {
                    _this.holidays=[];
                    _this.getList();
                }, function (error) {
                });
            }

        },

        created() {
            window.document.body.onscroll = this.handleScroll;
        }

    }
</script>
