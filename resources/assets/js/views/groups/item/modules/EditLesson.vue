<template>

    <b-modal v-if="data" ref="modal" size="lg" :title="title">

        <div v-if="form">
            <div role="alert" class="alert alert-danger mt-4">
                <h5>Внимание!</h5>
                <p>Перерасчет стоимости занятия будет произведен по текущему тарифу.</p>
            </div>
            <div v-bind:class="{ 'has-error': errors && errors.title }" class="form-group row">
                <label class="col-3 col-form-label">Тема занятия</label>
                <div class="col-9">
                    <input v-model="form.title" class="form-control" placeholder="Укажите тему занятия">
                    <form-error v-if="errors && errors.title" :errors="errors">
                        {{ errors.title[0] }}
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

        </div>
        <table class="table table-sm">
            <thead class="thead-default">
            <tr>
                <th>Имя</th>
                <th v-if="$user.isTeacher()">Оплачено занятий</th>
                <th v-if="!$user.isTeacher()">Баланс</th>
                <th v-if="!$user.isTeacher()">Стоимость</th>
                <th>Был</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="student in form.students" v-if="!student.in_archive">

                <td>{{ student.user.name }}</td>
                <td v-if="$user.isTeacher()">
                    <span v-if="prognosis(student) == 0" class="badge badge-pill badge-danger">{{ prognosis(student) }}</span>
                    <span v-if="prognosis(student) > 0 && prognosis(student) <3" class="badge badge-pill badge-warning">{{ prognosis(student) }}</span>
                    <span v-if="prognosis(student) > 2" class="badge badge-pill badge-success">{{ prognosis(student) }}</span>
                </td>
                <td class="h5" v-if="!$user.isTeacher()"><balance :value="balance(student)"></balance></td>
                <td class="pl-0" v-if="!$user.isTeacher() && group">{{cost(student)}} тг
                </td>
                <td><span v-if="isBalanceFreeze(student)==1">Баланс заморожен</span>
                    <div v-else class="btn-group btn-group-sm btn-group-justified">
                        <label class="btn btn-outline-success" :class="{'active': selectedStudentIds.includes(student.id)}">
                            <input type="radio" v-on:click="checkStudent(student.id)" autocomplete="off" style="display: none">Был
                        </label>
                        <label class="btn btn-outline-danger" :class="{active: !selectedStudentIds.includes(student.id) && !respectfulMissIds.includes(student.id)}">
                            <input type="radio" v-on:click="uncheckStudent(student.id)" autocomplete="off" style="display: none">Не был
                        </label>
                        <!--<label class="btn btn-outline-warning" :class="{active: respectfulMissIds.includes(student.id)}">-->
                            <!--<input type="radio" v-on:click="respectfulMiss(student.id)" autocomplete="off" style="display: none">Не был по ув. пр.-->
                        <!--</label>-->
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
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


</template>



<script>

    import { post, get } from '../../../../helpers/api';

    export default {

        props: ['data', '_form', 'group'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                title: 'Редактировать занятие',
                moveAll: false,
                selectedStudentIds: [],
                respectfulMissIds: [],
            }
        },
        created() {
            this.form = JSON.parse(JSON.stringify(this._form));
        },
        mounted() {
            this.$nextTick(function () {
                this.selectedStudentIds = this.selectedStudents(this.form);
                this.respectfulMissIds = [];
            });
        },
        watch: {
            _form(form) {

                this.form = JSON.parse(JSON.stringify(form));
                this.$nextTick(function () {
                    this.selectedStudentIds = this.selectedStudents(form);
                    this.respectfulMissIds = [];
                });

            },
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
                this.form.visit_student_ids = this.selectedStudentIds;
                this.form.respectfulMissIds = this.respectfulMissIds;
                let _this = this;

                post(_this, '/api/passed-save', this.form, function (response) {

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
            newLesson() {

                return {
                    group_id: this.group.id,
                    office_cabinet_id: this.group.default_office_cabinet_id,
                    office_cabinet: this.group.default_office_cabinet,
                    title: '',
                    date: '',
                    time: '',
                    duration: this.group.lesson_duration

                }

            },
            selectedStudents(lesson) {
                var array = [];
                if(lesson.students) {
                    lesson.students.forEach(function(student) {
                       if(student.pivot.visit==1) array.push(student.id);
                    });
                }
                return array;
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
                if(!this.selectedStudentIds.includes(id)) {
                    this.selectedStudentIds.push(id);
                }
                if(this.respectfulMissIds.includes(id)) {
                    let index = this.respectfulMissIds.indexOf(id);
                    if (index !== -1) this.respectfulMissIds.splice(index, 1);
                }
            },
            uncheckStudent(id) {
                let index = this.selectedStudentIds.indexOf(id);
                if (index !== -1) this.selectedStudentIds.splice(index, 1);
                if(this.respectfulMissIds.includes(id)) {
                    let index = this.respectfulMissIds.indexOf(id);
                    if (index !== -1) this.respectfulMissIds.splice(index, 1);
                }
            },
            respectfulMiss(id) {
                if(!this.respectfulMissIds.includes(id)) {
                    this.respectfulMissIds.push(id);
                }
                if(this.selectedStudentIds.includes(id)) {
                    let index = this.selectedStudentIds.indexOf(id);
                    if (index !== -1) this.selectedStudentIds.splice(index, 1);
                }
            },
            balance(student) {
                let rate = this.rate(student);
                let value = rate.balance;
                return value;
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