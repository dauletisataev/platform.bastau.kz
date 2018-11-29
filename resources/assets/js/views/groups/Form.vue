<template>
    <div id="group-form" v-if="form">
        <b-modal v-if="data" ref="modal" size="lg" :title="title">

            <div v-if="" v-bind:class="{ 'has-error': errors && errors.office_id }" class="form-group row">
                <label class="col-3 col-form-label">Отделение</label>
                <div class="col-9">
                    <v-select :on-change="selectOffice" label="name" value="id" :value.sync="form.office" :options="data.offices" placeholder="Выберите отделение"></v-select>
                    <form-error v-if="errors && errors.office_id" :errors="errors">
                        {{ errors.office_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-if="cabinets" v-bind:class="{ 'has-error': errors && errors.default_office_cabinet_id }" class="form-group row">
                <label class="col-3 col-form-label">Кабинет по умолчанию</label>
                <div class="col-9">
                    <v-select :on-change="selectOfficeCabinet" label="name" value="id" :value.sync="form.default_office_cabinet" :options="cabinets" placeholder="Выберите кабинет"></v-select>
                    <form-error v-if="errors && errors.default_office_cabinet_id" :errors="errors">
                        {{ errors.default_office_cabinet_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.program_id }" class="form-group row">
                <label class="col-3 col-form-label">Программа</label>
                <div class="col-9">
                    <div class="btn-group btn-group-sm">
                        <label class="btn btn-primary" v-for="program in data.programs" :class="{active: form.program_id==program.id}">
                            <input type="radio" v-on:click="selectProgram(program)" autocomplete="off" v-model="form.program" style="display: none" :value="program" >{{ program.name }}
                        </label>
                    </div>
                    <form-error v-if="errors && errors.program_id" :errors="errors">
                        {{ errors.program_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.lesson_type_id }" class="form-group row">
                <label class="col-3 col-form-label">Тип занятий</label>
                <div class="col-9">
                    <div class="btn-group btn-group-sm">
                        <label class="btn btn-primary" v-for="type in data.lesson_types" :class="{active: form.lesson_type_id==type.id}">
                            <input type="radio" v-on:click="selectLessonType(type)" autocomplete="off" v-model="form.lesson_type" :value="type" style="display: none">{{type.name}}
                        </label>
                    </div>
                    <form-error v-if="errors && errors.lesson_type_id" :errors="errors">
                        {{ errors.lesson_type_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.level_id }" class="form-group row">
                <label class="col-3 col-form-label">Уровень языка</label>
                <div class="col-9">
                    <div class="btn-group btn-group-sm btn-group-justified">
                        <label class="btn btn-primary" v-for="level in data.levels" :class="{active: form.level_id==level.id}">
                            <input type="radio" v-on:click="selectLevel(level)" autocomplete="off" v-model="form.level" :value="level" style="display: none">{{ level.short_name }}
                        </label>
                    </div>
                    <form-error v-if="errors&& errors.level_id" :errors="errors">
                        {{ errors.level_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-if="$common.data && $common.data.teachers" v-bind:class="{ 'has-error': errors && errors.teacher_id }" class="form-group row">
                <label class="col-3 col-form-label">Преподаватель</label>
                <div class="col-9">
                    <v-select :on-change="selectTeacher" label="name" value="id" :value.sync="form.teacher_select" :options="teachers" placeholder="Выберите преподавателя"></v-select>
                    <form-error v-if="errors && errors.teacher_id" :errors="errors">
                        {{ errors.teacher_id[0] }}
                    </form-error>
                </div>
            </div>
            <!--
            <div v-bind:class="{ 'has-error': errors && errors.rate_errors }"  class="form-group row">
                <label class="col-3 col-form-label">Тариф</label>
                <div class="col-9">
                    <v-select :on-change="selectRate" label="name" :value.sync="form.rate_select" :options="rates" placeholder="Выберите тариф"></v-select>
                    <div v-if="temp==true" style="margin-top: 1rem">
                    <div class="form form-inline text-muted mb-2">
                        <input class="form-control form-control-sm col-2 mr-2" type="text" placeholder="19000 тг" v-model="form.custom_rate_cost">
                        за
                        <input class="form-control form-control-sm ml-2 mr-2" style="width:44px" type="text" placeholder="12" v-model="form.custom_rate_countLesson">
                        занятий в месяц по
                        <input class="form-control form-control-sm ml-2 mr-2" style="width:44px" type="text" placeholder="60" v-model="form.custom_rate_duration">
                        минут,
                        <input class="form-control form-control-sm ml-2 mr-2" style="width:44px" type="text" placeholder="4" v-model="form.custom_rate_months">
                        месяцев
                    </div>
                        <b>Стоимость занятия:</b> <span v-if="form.custom_rate_cost && form.custom_rate_countLesson && !isNaN(Math.floor(form.custom_rate_cost / form.custom_rate_countLesson)) && isFinite(Math.floor(form.custom_rate_cost / form.custom_rate_countLesson))">{{ Math.floor(form.custom_rate_cost / form.custom_rate_countLesson) }} тенге </span></span>
                            <span class="fa fa-question-circle text-muted ml-1" data-toggle="tooltip" title="" v-tooltip="'Стоимость занятия рассчитывается путём деления общей суммы на кол-во занятий'"></span>
                    </div>
                    <form-error v-if="errors.rate_error" :errors="errors">
                        Заполните все поля тарифа правильно
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.count_lessons }" class="form-group row" v-if="form.id && !form.rate_id && form.rate_id!=0">
                <label class="col-3 col-form-label">Кол-во занятий</label>
                <div class="col-9">
                    <input v-model="form.count_lessons" class="form-control" type="number" placeholder="Укажите кол-во занятий" disabled="true">
                    <form-error v-if="errors && errors.count_lessons" :errors="errors">
                        {{ errors.count_lessons[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.cost }" class="form-group row" v-if="form.id && !form.rate_id && form.rate_id!=0">
                <label class="col-3 col-form-label">Стоимость занятия</label>
                <div class="col-9">
                    <input v-model="form.cost" class="form-control" type="number" placeholder="Укажите стоимость занятия" disabled="true">
                    <form-error v-if="errors && errors.cost" :errors="errors">
                        {{ errors.cost[0] }}
                    </form-error>
                </div>
            </div>

            -->

            <div v-bind:class="{ 'has-error': errors && errors.lesson_duration }" class="form-group row">
                <label class="col-3 col-form-label">Длительность занятия</label>
                <div class="col-9">
                    <input v-model="form.lesson_duration" class="form-control" type="number" placeholder="Укажите количество минут">
                    <form-error v-if="errors && errors.lesson_duration" :errors="errors">
                        {{ errors.lesson_duration[0] }}
                    </form-error>
                </div>
            </div>

            <div class="form-group row" v-if="!form.id">
                <label class="col-3 col-form-label">Начало обучения</label>
                <div class="col-9">
                    <date-picker v-model="form.schedule_from" active="1" class="rounded-left border-right-0" placeholder="укажите дату начала обучения"></date-picker>
                </div>
            </div>


            <div v-bind:class="{ 'has-error': errors && errors.schedules }" class="form-group row" v-if="!form.id">
                <label class="col-3 col-form-label">Время занятий</label>
                <div class="col-9">
                    <schedules :schedules="form.schedules" id="student-schedules"></schedules>
                    <form-error v-if="errors && errors.schedules" :errors="errors">
                        {{ errors.schedules[0] }}
                    </form-error>
                </div>
            </div>

            <div slot="modal-footer">
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="sendForm"
                        :disabled="formSending? true : false"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ form.id ? 'Сохранить' : 'Добавить' }}
                </button>
            </div>

        </b-modal>
    </div>


</template>



<script>

    import { post } from '../../helpers/api';

    export default {

        props: ['data', '_form'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                title: '',
                cabinets: '',
                temp: false
            }
        },
        computed: {
            teachers() {
                let comp = this;
                return this.$common.data.teachers.filter(function (teacher) {
                    if (teacher.in_archive) return false;
                    return  (comp.form.office_id && Number.isInteger(comp.form.office_id)) ? teacher.office_ids.includes(comp.form.office_id) : true;
                });
            },
            rates() {
                var arr=[];
                let component = this;
                arr.push({id: 0, name: "ввести вручную" });
                if(this.form.program_id && this.form.lesson_type_id) {
                    this.$common.data.rates.forEach(function (rate) {
                        if(rate.program_id == component.form.program_id && rate.type==(component.form.lesson_type_id-1)) {
                            rate.name = rate.cost + " за " + rate.countLesson + " занятий в месяц по " + rate.duration + " минут, " + rate.months + " месяцев ";
                            arr.push(rate);
                        }
                    });
                }
                return arr;
            }
        },
        watch: {

            form(form) {

                    this.form.office_id = this.$user.getOfficeId();
                    if (this.form.custom_rate_cost==0) this.form.custom_rate_cost='';
                    if (this.form.custom_rate_duration==0) this.form.custom_rate_duration='';
                    if (this.form.custom_rate_countLesson==0) this.form.custom_rate_countLesson='';
                    if (this.form.custom_rate_months==0) this.form.custom_rate_months='';
            }

        },
        created() {

            this.form = this._form ? this._form : this.newGroup();
        },
        mounted() {

            this.form.teacher_select = this.form.teacher ? {
                id: this.form.teacher.id,
                name: this.form.teacher.user.name
        } : '';
            this.form.rate_select = this.form.rate ? {
                id: this.form.rate.id,
                name: this.form.rate.cost + " за " + this.form.rate.countLesson + " занятий в месяц по " + this.form.rate.duration + " минут, " + this.form.rate.months + " месяцев "
            } : '';
            if(this.form.rate_select.id == 0) this.form.rate_select.name = 'ввести вручную';
            this.title = this.form.id ? 'Редактировать группу' : 'Добавить группу';

        },
        components: {
            Schedules : require('./../../components/Schedules.vue'),
            FormError : require('./../../components/FormError.vue'),
            DatePicker: require('./../../components/Datepicker.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;

                let _this = this;

                post(_this, '/api/group-save', this.form, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newGroup();

                    if(!_this.form.id){
                        _this.$router.push({ name: 'group', params: {id: response.data.id} });
                    }else{
                        _this.$emit('formSending');
                    }


                }, function (error) {

                    _this.formSending = false;
                    _this.errors = error.response.data;

                });

            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            selectLevel(val) {
                if (val)
                    this.form.level_id = val.id;
                this.form.level = val;
            },
            selectOffice(val) {
                if (val){
                    this.cabinets = val.cabinets;
                    this.form.office_id = val.id;
                }
                this.form.office = val;
            },
            selectOfficeCabinet(val) {
                if (val)
                    this.form.default_office_cabinet_id = val.id;
                this.form.default_office_cabinet = val;
            },
            selectProgram(val) {
                    if (val){
                        this.form.program_id = val.id;
                        if (!this.form.id) this.form.count_lessons = val.countLessons;
                        if (!this.form.id && this.form.lesson_type_id) this.form.cost = val.costs[this.form.lesson_type_id];
                        this.form.program = val;
                    }

            },
            selectTeacher(val) {
                if (val)
                    this.form.teacher_id = val.id;
                this.form.teacher_select = val;
            },
            selectLessonType(val) {
                if (val){
                    this.form.lesson_type_id = val.id;
                    if (!this.form.id && this.form.program)
                        this.form.cost = this.form.program.costs[val.id];
                }

                this.form.lesson_type = val;

            },
            selectRate(val) {

                if (val) {
                    this.form.rate_select = val;
                    this.form.rate_id = val.id;
                    this.temp = (val.name=="ввести вручную") ? this.temp = true : this.temp = false;
                }
            },
            setCabinets(office_id) {

                let components = this;

                this.$common.data.offices.forEach(function (office) {
                   if(office.id == office_id) components.cabinets = office.cabinets;
                });

            },
            newGroup() {

                return {
                    office_id: '',
                    program_id: '',
                    level_id: '',
                    lesson_type_id: '',
                    teacher_id: '',
                    count_lessons: '',
                    start_passed_lesson_count: '',
                    lesson_duration: '',
                    default_office_cabinet_id: '',
                    default_office_cabinet: '',
                    cost: '',
                    program: '',
                    teacher: '',
                    level: '',
                    office: '',
                    lesson_type: '',
                    teacher_select: '',
                    rate: '',
                    rate_select: '',
                    rate_id: '',
                    custom_rate_cost: '',
                    custom_rate_countLesson: '',
                    custom_rate_duration: '',
                    custom_rate_months: '',
                    schedule_from: '',
                    schedules: [
                        {
                            day: 1,
                            active: false,
                            from: '',
                            to: ''

                        },
                        {
                            day: 2,
                            active: false,
                            from: '',
                            to: ''
                        },
                        {
                            day: 3,
                            active: false,
                            to: '',
                            from: ''
                        },
                        {
                            day: 4,
                            active: false,
                            to: '',
                            from: ''
                        },
                        {
                            day: 5,
                            active: false,
                            to: '',
                            from: ''
                        },
                        {
                            day: 6,
                            active: false,
                            to: '',
                            from: ''
                        }
                    ]

                }

            }

        }



    }
</script>