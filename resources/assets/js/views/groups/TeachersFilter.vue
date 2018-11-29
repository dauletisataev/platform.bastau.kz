<template>
    <!-- Поиск -->
    <div class="col-12">
        <form class="input-group mb-3" style="z-index: 3004;">
            <dropdown animation="ani-none" :position="['left', 'top', 'right', 'top']" :visible="visible" >
                <button @click="visible = !visible" type="button" class="btn btn-primary" style="border-top-right-radius: 0;border-bottom-right-radius: 0;" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">
                    <span class="fa fa-filter mr-1"></span>
                    фильтр
                    <span class="badge badge-pill text-primary bg-faded ml-1">{{countFilters()}}</span>
                </button>
                <div slot="dropdown" class="dialog" style="z-index: 1000; min-width: 10rem;padding: .5rem 0;margin: .5rem 0 0;font-size: 1rem;color: #292b2c;text-align: left;list-style: none;background-color: #fff;-webkit-background-clip: padding-box;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .25rem;">
                    <div class="px-3 py-2" style="width: 302px;">
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">диапазон статистики</div>
                            <div class="input-group">
                                <date-range-picker v-model="filterData.dateRange"></date-range-picker>
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            </div>
                        </div>
                        <div class="form-group"  v-show="$common.data && $common.data.offices.length > 1">
                            <div class="small font-weight-bold text-muted">отделение</div>
                            <multiselect style="z-index:1002!important" :value.sync="temp.offices" :limit="2" :limit-text="$common.limitSelect" @input="selectOffices" :options="$common.data.offices" :multiple="true" :close-on-select="false" :clear-on-select="false" :searchable="false" :show-labels="false" :placeholder="$common.selectOfficeTextShort(temp.offices)" label="name" track-by="name"></multiselect>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">программы</div>
                            <multiselect :value.sync="temp.programs" :limit="2" :limit-text="$common.limitSelect" @input="selectPrograms" :options="$common.data.programs" :multiple="true" :close-on-select="false" :clear-on-select="false" :searchable="false" :show-labels="false" :placeholder="$common.selectOfficeTextShort(temp.programs)" label="name" track-by="name"></multiselect>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">уровень</div>
                            <v-select :on-change="selectLevel" label="name" value="id" :value.sync="temp.level" :options="$common.data.levels" placeholder="Выберите"></v-select>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">график работы</div>
                            <time-sheet-filter :id="'tsfilter1'" ref="timeSheet1" @updateSheet="selectHours"></time-sheet-filter>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">свободное время</div>
                            <time-sheet-filter-2 :id="'tsfilter2'" ref="timeSheet2" @updateSheet="selectFreeHours"></time-sheet-filter-2>
                        </div>
                        <div class="form_group">
                            <button @click="clearListLoad()" :disabled="load" class="btn btn-primary btn-block" >применить фильтр</button>
                            <button @click="resetFilter()" :disabled="load" class="btn btn-secondary btn-block btn-sm">сбросить фильтр</button>
                        </div>
                    </div>
                </div>
            </dropdown>
            <input v-model="filterData.search_text" @keyup.native.enter.prevent="clearListLoad()" type="text" class="form-control form-control-sm" placeholder="Поиск по имени, штрихкоду или номеру телефона...">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-secondary" @click.prevent="clearListLoad()"><span class="fa fa-search mr-1"></span> поиск</button>
            </div>
        </form>
    </div>
</template>

<script>

    export default {

        props: ['load', 'not_in_id'],

        data() {

            return {
                filterData: {
                    not_in_id: '',
                    search_text: '',
                    office_ids: [],
                    level_id: '',
                    in_archive: '',
                    program_ids: [],
                    without_group: false,
                    dateRange: '',
                    hours: JSON.stringify([
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                    ]),
                    freeHours: JSON.stringify([
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                    ]),
                },
                temp: {
                    offices: [],
                    level: '',
                    programs: [],
                    status: ''
                },
                statuses: [{label:'Текущие', value: 0}, {label:'Архивные', value: 1}],
                visible: false,
            }

        },

        components: {
            'date-range-picker': require('../../components/DateRangePicker.vue'),
            'time-sheet-filter': require('../../components/TimeSheetFilter.vue'),
            'time-sheet-filter-2': require('../../components/TimeSheetFilter.vue'),
        },

        mounted() {

            if (this.$user.isAdmin() || this.$user.isAccountant()){
                Vue.delete(this.filterData, 'office_ids');
            }

            if (this.not_in_id)
                this.filterData.not_in_id = this.not_in_id;

            this.$nextTick(function () {
                this.setFiltered(this.$route.query);
            });


        },

        methods: {
            resetFilter(){
                this.filterData = {
                    not_in_id: '',
                    search_text: '',
                    office_ids: [],
                    level_id: '',
                    in_archive: '',
                    program_ids: [],
                    without_group: false,
                    hours: JSON.stringify([
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                    ]),
                    freeHours: JSON.stringify([
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
                    ]),
                };

                this.temp = {
                    offices: [],
                    level: '',
                    programs: [],
                    status: ''
                };

                if (this.not_in_id)
                    this.filterData.not_in_id = this.not_in_id;
                this.$refs.timeSheet1.resetSheet();
                this.$refs.timeSheet2.resetSheet();
                this.clearListLoad();
            },
            clearListLoad(){

                this.$nextTick(function () {
                    this.$emit('filtered');
                });

            },
            setFiltered(query){

                for (let filterKey in this.filterData) {
                    for(let queryKey in query){
                        if(filterKey == queryKey && filterKey != 'hours' && filterKey != 'freeHours') {
                            if(this.filterData[filterKey].constructor === Array) {
                                this.filterData[filterKey].push(query[queryKey]);
                            } else {
                                this.filterData[filterKey] = query[queryKey];
                            }

                        }
                    }
                }

                this.$nextTick(function () {
                    this.setSelect();
                });


            },
            setSelect()
            {
                if(this.filterData.office_ids)
                    this.selectedArray('offices', this.$common.data.offices, this.filterData.office_ids);
                if(this.filterData.level_id)
                    this.selected('level', this.$common.data.levels, this.filterData.level_id);

                if(this.filterData.program_ids){
                    this.temp.programs = [];
                    let comp = this;
                    this.filterData.program_ids.forEach(function (program_id) {
                        comp.$common.data.programs.forEach(function (value) {
                            if(value.id == program_id) comp.temp.programs.push(value);
                        });
                    })
                }

                if(this.filterData.in_archive)
                    this.temp.status = this.statuses[this.filterData.in_archive];


                this.$nextTick(function(){
                    this.clearListLoad();
                })

            },
            selected(key, options, id)
            {
                let comp = this;

                options.forEach(function (value) {
                    if(value.id == id) comp.temp[key] = value;
                });
            },
            selectedArray(key, options, ids) {
                let comp = this;
                ids.forEach(function (id) {
                    options.forEach(function(value) {
                        if(value.id == id) comp.temp[key].push(value);
                    });
                });
            },
            selectOffices(val) {
                let offices = [];

                val.forEach(function(value) {
                    offices.push(value.id);
                });

                this.filterData.office_ids = offices;
                this.temp.offices = val;
            },
            selectLevel(val) {
                if (val)
                    this.filterData.level_id = val.id;
                this.temp.level = val;
            },
            selectPrograms(val) {
                let programs = [];

                val.forEach(function(value) {
                    programs.push(value.id);
                });

                this.temp.programs = val;
                this.filterData.program_ids = programs;
            },
            selectStatus(val) {
                if (val)
                    this.filterData.in_archive = val.value

                this.temp.status = val;
            },
            countFilters() {
                var count = 0;
                if(this.filterData.search_text) count++;
                if(this.filterData.office_ids && this.filterData.office_ids.length!=0) count++;
                if(this.filterData.program_ids && this.filterData.program_ids.length!=0) count++;
                if(this.filterData.level_id) count++;
                if(this.filterData.without_group) count++;
                return count;
            },
            selectHours(sheetData) {
                this.filterData.hours = JSON.stringify(sheetData);
            },
            selectFreeHours(sheetData) {
                this.filterData.freeHours = JSON.stringify(sheetData);
            },
        }

    }

</script>