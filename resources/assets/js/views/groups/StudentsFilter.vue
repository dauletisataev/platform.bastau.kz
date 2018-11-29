<template>
    <!-- Поиск -->
    <div class="col-10">
        <form class="input-group mb-3" style="z-index: 3004;">
            <dropdown animation="ani-none" :position="['left', 'top', 'right', 'top']" :visible="visible" >
                <button @click="visible = !visible" type="button" class="btn btn-primary" style="border-top-right-radius: 0;border-bottom-right-radius: 0;" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">
                    <span class="fa fa-filter mr-1"></span>
                    фильтр
                    <span class="badge badge-pill text-primary bg-faded ml-1">{{countFilters()}}</span>
                </button>
                <div slot="dropdown" class="dialog" style="z-index: 1000; min-width: 10rem;padding: .5rem 0;margin: .5rem 0 0;font-size: 1rem;color: #292b2c;text-align: left;list-style: none;background-color: #fff;-webkit-background-clip: padding-box;background-clip: padding-box;border: 1px solid rgba(0,0,0,.15);border-radius: .25rem;">
                    <div class="p-2" style="width: 200px"  >
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">дата создания</div>
                            <div class="input-group">
                                <date-range-picker v-model="filterData.dateRange"></date-range-picker>
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            </div>
                        </div>
                        <div class="form-group" v-if="archived==1">
                            <div class="small font-weight-bold text-muted">дата архивации</div>
                            <div class="input-group">
                                <date-range-picker v-model="filterData.dateArchived"></date-range-picker>
                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            </div>
                        </div>
                        <div class="form-group"  v-show="$common.data && $common.data.offices.length > 1">
                            <div class="small font-weight-bold text-muted">отделение</div>
                            <multiselect :value.sync="temp.offices" :limit="2" :limit-text="$common.limitSelect" @input="selectOffices" :options="$common.data.offices" :multiple="true" :close-on-select="false" :clear-on-select="false" :searchable="false" :show-labels="false" :placeholder="$common.selectOfficeTextShort(temp.offices)" label="name" track-by="name"></multiselect>
                        </div>
                        <div class="form-group" v-if="!$user.isTeacher()">
                            <div class="small font-weight-bold text-muted">преподаватель</div>
                            <v-select :on-change="selectTeacher" label="name" value="id" :value.sync="temp.teacher" :options="teachers" placeholder="Выберите"></v-select>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">тип занятий</div>
                            <v-select :on-change="selectLessonType" label="name" value="id" :value.sync="temp.lessonType" :options="$common.data.lesson_types" placeholder="Выберите"></v-select>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">программа</div>
                            <v-select :on-change="selectProgram" label="name" value="id" :value.sync="temp.program" :options="$common.data.programs" placeholder="Выберите"></v-select>
                        </div>
                        <div class="form-group">
                            <div class="small font-weight-bold text-muted">уровень</div>
                            <v-select :on-change="selectLevel" label="name" value="id" :value.sync="temp.level" :options="$common.data.levels" placeholder="Выберите"></v-select>
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

        props: ['load','archived','not_in_ids'],

        data() {

            return {

                filterData: {
                    dateRange: '',
                    search_text: '',
                    office_ids: [],
                    teacher_id: '',
                    lesson_type_id: '',
                    program_id: '',
                    level_id: '',
                    dateArchived: ''
                },
                temp: {
                    offices: [],
                    teacher: '',
                    lesson_type: '',
                    program: '',
                    level: '',
                    status: ''
                },
                statuses: [{label:'Текущие', value: 0}, {label:'Архивные', value: 1}],
                visible: false,

            }

        },

        watch:{
        },

        components: {
            'date-range-picker': require('../../components/DateRangePickerStudents.vue')
        },

        computed: {
            teachers() {
                let comp = this;
                return this.$common.data.teachers.filter(function (teacher) {
                    if (teacher.in_archive) return false;
                    return  (comp.filterData.office_ids && comp.filterData.office_ids.length!=0) ? _.intersection(comp.filterData.office_ids, teacher.office_ids).length>0 : true;
                });
            }
        },

        mounted() {

            if (this.not_in_ids)
                this.filterData.not_in_ids = this.not_in_ids;

            this.$nextTick(function () {
                this.setFiltered(this.$route.query);
            });
            this.$emit('routeQuery');


        },
        methods: {

            resetFilter(){
                this.filterData = {
                    search_text: '',
                    office_ids: [],
                    teacher_id: '',
                    lesson_type_id: '',
                    program_id: '',
                    level_id: '',
                };

                this.temp = {
                    offices: [],
                    teacher: '',
                    lesson_type: '',
                    program: '',
                    level: '',
                    status: ''
                };


                this.clearListLoad();
            },
            clearListLoad(){
                this.visible = false;
                if(this.archived != 1) this.filterData.dateArchived = '';
                this.$nextTick(function () {
                    this.$emit('filtered');
                });

            },
            setFiltered(query){

                for (let filterKey in this.filterData) {
                    for(let queryKey in query){
                        if(filterKey == queryKey) this.filterData[filterKey] = query[queryKey] === 'false' ? false : (query[queryKey] === 'true' ? true : query[queryKey]);
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
                if(this.filterData.program_id)
                    this.selected('program', this.$common.data.programs, this.filterData.program_id);
                if(this.filterData.level_id)
                    this.selected('level', this.$common.data.levels, this.filterData.level_id);
                if(this.filterData.teacher_id)
                    this.selected('teacher', this.$common.data.teachers, this.filterData.teacher_id);
                if(this.filterData.lesson_type_id)
                    this.selected('lessonType', this.$common.data.lesson_types, this.filterData.lesson_type_id);

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
            selectProgram(val) {
                if (val)
                    this.filterData.program_id = val.id;
                this.temp.program = val;
            },
            selectTeacher(val) {
                if (val)
                    this.filterData.teacher_id = val.id;
                this.temp.teacher = val;
            },
            selectLessonType(val) {
                if (val)
                    this.filterData.lesson_type_id = val.id;
                this.temp.lesson_type = val;
            },
            selectStatus(val) {
                if (val)
                    this.filterData.in_archive = val.value;
                this.temp.status = val;
            },
            countFilters() {
                let comp = this;
                var count = 0;
                if(this.filterData.dateRange) count++;
                if(this.filterData.search_text) count++;
                if(this.filterData.office_ids && this.filterData.office_ids.length!=0) count++;
                if(this.filterData.teacher_id) count++;
                if(this.filterData.lesson_type_id) count++;
                if(this.filterData.program_id) count++;
                if(this.filterData.level_id) count++;
                if(this.filterData.dateArchived) count++;
                return count;
            },
        }

    }

</script>