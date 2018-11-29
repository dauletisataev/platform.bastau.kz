<template>

    <b-modal v-if="data" ref="modal" size="lg" title="Провести занятие">

        <div v-if="form">

            <div v-bind:class="{ 'has-error': errors && errors.title }" class="form-group row">
                <label class="col-3 col-form-label">Тема занятия</label>
                <div class="col-9">
                    <input v-model="form.title" class="form-control" placeholder="Укажите тему занятия">
                    <form-error v-if="errors && errors.title" :errors="errors">
                        {{ errors.title[0] }}
                    </form-error>
                </div>
            </div>

            <div v-if="cabinets" v-bind:class="{ 'has-error': errors && errors.office_cabinet_id }" class="form-group row">
                <label class="col-3 col-form-label">Аудитория</label>
                <div class="col-9">
                    <v-select :on-change="selectOfficeCabinet" label="name" value="id" :value="form.office_cabinet" :options="cabinets" placeholder="Выберите аудиторию"></v-select>
                    <form-error v-if="errors && errors.office_cabinet_id" :errors="errors">
                        {{ errors.office_cabinet_id[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.duration }" class="form-group row">
                <label class="col-3 col-form-label">Длительность занятия</label>
                <div class="col-9">
                    <input v-model="form.duration" class="form-control" type="number" placeholder="Укажите количество минут">
                    <form-error v-if="errors && errors.duration" :errors="errors">
                        {{ errors.duration[0] }}
                    </form-error>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-3 col-form-label">Дата и время</label>
                <div class="col-9 m-0">
                    <div class="row">
                        <div class="col-6" v-bind:class="{ 'has-error': errors && errors.date }" style="padding-right: 0">
                            <datepicker :onChange="selectDate" v-model="form.date" active="1" class="rounded-left border-right-0" placeholder="укажите дату занятия"></datepicker>
                            <form-error v-if="errors && errors.date" :errors="errors">
                                {{ errors.date[0] }}
                            </form-error>
                        </div>
                        <div class="col-6" v-bind:class="{ 'has-error': errors && errors.time }" style="padding-left: 0">
                            <timepicker v-model="form.time" active="1" class="rounded-right" placeholder="укажите время занятия"></timepicker>
                            <form-error v-if="errors && errors.time" :errors="errors">
                                {{ errors.time[0] }}
                            </form-error>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-sm">
                <thead class="thead-default">
                <tr>
                    <th>Имя</th>
                    <th v-if="$user.isTeacher()">Оплачено занятий</th>
                    <th v-if="!$user.isTeacher()">Баланс</th>
                    <th v-if="!$user.isTeacher()">Стоимость</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="student in students" v-if="student.in_archive!=1 && student.pivot.deleted!=1">
                    <td>{{ student.user.name }}</td>
                    <td v-if="$user.isTeacher()">
                        <span v-if="prognosis(student) == 0" class="badge badge-pill badge-danger">{{ prognosis(student) }}</span>
                        <span v-if="prognosis(student) > 0 && prognosis(student) <3" class="badge badge-pill badge-warning">{{ prognosis(student) }}</span>
                        <span v-if="prognosis(student) > 2" class="badge badge-pill badge-success">{{ prognosis(student) }}</span>
                    </td>
                    <td class="h5" v-if="!$user.isTeacher()"><balance :value="balance(student)"></balance></td>
                    <td class="pl-0" v-if="!$user.isTeacher()">{{cost(student)}} тг</td>
                    <td><span v-if="isBalanceFreeze(student)==1">Баланс заморожен</span>
                        <div v-else class="btn-group btn-group-sm btn-group-justified">
                            <label class="btn btn-outline-success" :class="{'active': form.selectedStudentIds.includes(student.id)}">
                                <input type="radio" v-on:click="checkStudent(student.id)" autocomplete="off" style="display: none">Был
                            </label>
                            <label class="btn btn-outline-danger" :class="{active: !form.selectedStudentIds.includes(student.id) && !form.respectfulMissIds.includes(student.id)}">
                                <input type="radio" v-on:click="uncheckStudent(student.id)" autocomplete="off" style="display: none">Не был
                            </label>
                            <!--<label class="btn btn-outline-warning" :class="{active: form.respectfulMissIds.includes(student.id)}">-->
                                <!--<input type="radio" v-on:click="respectfulMiss(student.id)" autocomplete="off" style="display: none">Не был по ув. пр.-->
                            <!--</label>-->
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        <div slot="modal-footer">
            <button
                    type="button"
                    class="btn btn-primary"
                    @click="sendForm"
                    :disabled="formSending? true : false"
            >
                <i v-show="formSending" class="fa fa-spinner fa-spin"></i> Провести занятие
            </button>
        </div>

    </b-modal>


</template>



<script>

    import { post, get } from '../../../../helpers/api';

    export default {

        props: ['data', '_form', 'group', 'lessonDate', 'students'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                title: '',
                moveAll: false,
                cabinets: this.group.office.cabinets,
                selectedStudentIds: [],
                respectfulMissIds: [],
            }
        },
        created() {
            this.form = this._form ? this._form : this.newLesson();
        },
        mounted() {
            this.setFormDate(this.lessonDate);
        },
        watch: {
            _form(form) {
                this.form = JSON.parse(JSON.stringify(form));

                this.$nextTick(function () {
                    this.form = this.form ? this.form : this.newLesson();
                    this.title = this.form.is_clone ? 'Дублировать занятие' : 'Редактировать занятие';
                    this.setFormDate(this.lessonDate);

                });

            },

            lessonDate(lessonDate) {

                if (!lessonDate) return false;

                this.$nextTick(function () {
                    this.setFormDate(lessonDate);
                });
            },

            lesson_id(lesson_id) {
                this.selectedStudentIds = [];
                this.respectfulMissIds = [];
            },

            group: {
                handler: function(val) {
                    this.form.group_id = val.id;
                },
                deep: true
            }

        },

        components: {
            FormError : require('./../../../../components/FormError.vue'),
            Timepicker: require('./../../../../components/Timepicker.vue'),
            Datepicker: require('./../../../../components/Datepicker.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;

                this.form.move_all = this.moveAll;

                let _this = this;

                post(_this, '/api/lesson-complete', this.form, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newLesson();

                    _this.$emit('formSending');


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
            selectOfficeCabinet(val) {
                if (val)
                    this.form.office_cabinet_id = val.id;
                this.form.office_cabinet = val;
            },
            setFormDate(d){
                if (!d) return false;
                this.form.date = d.date;
                this.form.time = d.time;
            },
            newLesson() {

                return {
                    group_id: this.group.id,
                    office_cabinet_id: this.group.default_office_cabinet_id,
                    office_cabinet: this.group.default_office_cabinet,
                    title: '',
                    date: '',
                    time: '',
                    duration: this.group.true_duration,
                    selectedStudentIds: this.group.student_ids,
                    respectfulMissIds: [],
                }

            },
            selectDate(date) {

                let component = this;

                this.group.calendar.forEach(function (day, index) {
                    if(day.date == date){
                        component.form.time = day.time;
                        return false;
                    }
                });

            },
            cost(value) {
                let money = 0;
                let durationtemp = 0;
                let duration = this.duration;
                let rate = this.rate(value);
                // Получаем дефолтное значение длительности занятия
                durationtemp = rate.duration;
                //Считаем стоимость
                duration ? money = rate.cost / rate.count_lessons * duration / durationtemp : money = rate.cost / rate.count_lessons;
                return Math.floor(money);
            },
            checkStudent(id) {
                if(!this.form.selectedStudentIds.includes(id)) {
                    this.form.selectedStudentIds.push(id);
                }
                if(this.form.respectfulMissIds.includes(id)) {
                    let index = this.form.respectfulMissIds.indexOf(id);
                    if (index !== -1) this.form.respectfulMissIds.splice(index, 1);
                }
            },
            uncheckStudent(id) {
                let index = this.form.selectedStudentIds.indexOf(id);
                if (index !== -1) this.form.selectedStudentIds.splice(index, 1);
                if(this.form.respectfulMissIds.includes(id)) {
                    let index = this.form.respectfulMissIds.indexOf(id);
                    if (index !== -1) this.form.respectfulMissIds.splice(index, 1);
                }
            },
            respectfulMiss(id) {
                if(!this.form.respectfulMissIds.includes(id)) {
                    this.form.respectfulMissIds.push(id);
                }
                if(this.form.selectedStudentIds.includes(id)) {
                    let index = this.form.selectedStudentIds.indexOf(id);
                    if (index !== -1) this.form.selectedStudentIds.splice(index, 1);
                }
            },
            balance(student) {
                let rate = this.rate(student);
                return rate.balance;
            },
            rate(student) {
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
            isBalanceFreeze(student) {
                let rate = this.rate(student);
                let value = rate.is_balance_freeze;
                return value;
            },
            prognosis(student) {
                let rate = this.rate(student);
                let value = rate.prognosis_balance;
                return value;
            }


        }



    }
</script>