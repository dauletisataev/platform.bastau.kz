<template>
    <div v-if="form.length>0">
        <b-modal v-if="data" ref="modal">
            <div slot="modal-title">
                Создание тарифов <span class="badge badge-primary ml-1">{{ step+1 }} из {{ students.length }}</span>
            </div>
            <div v-for="(rate,index) in form">
                <div v-show="index == step">
                    <div class="alert alert-info mb-3 text-center">
                        <router-link :to="{ name: 'student', params: { id: rate.student_id }}"><span class="font-weight-bold"><span class="fa fa-user"></span> {{ rate.student_name }}</span></router-link>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 500" class="d-block" v-if="!program_id && !lesson_type_id">
                            Программа обучения
                        </label>
                        <div class="btn-group btn-group-sm" v-show="!program_id && !lesson_type_id">
                            <button class="btn btn-primary" :disabled="blockProgram(program.id, index)" @click="selectRateProgram(index2,index)" v-for="(program, index2) in data.programs" :class="{active: rate.program_id == program.id}">
                                {{ program.name }}
                            </button>
                        </div>
                        <div class="btn-group btn-group-sm" v-show="!program_id && !lesson_type_id">
                            <button class="btn btn-primary" :disabled="blockLessonType(lesson_type.id, index)" @click="selectRateLessonType(index3,index)" v-for="(lesson_type, index3) in data.lesson_types" :class="{active: rate.lesson_type_id == lesson_type.id}">
                                {{ lesson_type.additional_name }}
                            </button>
                        </div>
                        <div class="form-group alert alert-warning small mt-2" v-if="foundDefault(rate)">
                            <p class="mb-2">Найден стандартный тариф по указанной программе и типу занятий.</p>
                            <button @click="setDefault(rate)" class="btn btn-sm btn-warning">подставить параметры автоматически</button>
                        </div>
                        <div class="form-group">
                            <label style="font-weight:500" class="d-block">
                                Месячная стоимость (₸)
                            </label>
                            <input type="number" v-model="rate.cost" class="form-control form-control-sm" placeholder="Введите здесь сколько студент должен платить ежемесячно">
                            <div class="form-text text-muted small">
                                Если студенту предусмотрена скидка на оплату за несколько месяцев, то разделите общую стоимость на кол-во месяцев и укажите месячную стоимость.
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="font-weight:500" class="d-block">
                                Занятий в месяц
                            </label>
                            <input type="number" v-model="rate.count_lessons" class="form-control form-control-sm" placeholder="Введите здесь количество занятий в месяц">
                            <div class="form-text text-muted small">
                                Сколько занятий посетит студент за один месяц за указанную выше стоимость.
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="font-weight:500" class="d-block">
                                Продолжительность одного занятия (минут)
                            </label>
                            <input type="number" v-model="rate.duration" class="form-control form-control-sm" placeholder="Введите здесь сколько минут длится занятие">
                            <div class="form-text text-muted small">
                                Не влияет на общую стоимость занятия, но если продолжительность конкретного занятия будет отличаться от указанного в этом поле, то стоимость этого занятия перерасчитается автоматически.
                            </div>
                        </div>
                        <div class="form-group" v-if="$root.user.isAdmin() || $root.user.isDirector()">
                            <label style="font-weight:500" class="d-block">
                                Начальный баланс (₸)
                            </label>
                            <input type="number" v-model="rate.start_balance" class="form-control form-control-sm" placeholder="Введите здесь начальный баланс">
                        </div>
                        <div class="form-group alert alert-warning small">
                            <div class="h6">Результат</div>
                            <div v-if="rate.cost && rate.duration && rate.count_lessons">
                                На основе введённых параметров, за одно занятие по программе {{ getProgramName(index) }} {{ getLessonTypeName(index) }} продолжительностью {{ rate.duration }} минут у ученика будет списываться сумма {{ Math.floor(rate.cost / rate.count_lessons) }}₸.
                            </div>
                            <div v-else>
                                Заполните все поля
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-show="errors" class="form-group alert alert-danger small">
                <div class="h6">Ошибка</div>
                Заполните все поля корректно.
            </div>
            <div slot="modal-footer">
                <button
                        type="button"
                        class="btn btn-secondary"
                        @click="step--"
                        v-show="form.length>1 && step > 0">
                            Назад
                </button>
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="step++"
                        v-show="form.length>1 && step < (form.length-1)">
                            Далее
                </button>
                <button
                        v-show="step == (form.length - 1)"
                        type="button"
                        class="btn btn-primary"
                        @click="sendForm"
                        :disabled="(formSending ? true : false)"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ form.id ? 'Сохранить' : 'Создать тарифы' }}
                </button>
            </div>
        </b-modal>
    </div>
</template>

<script>

    import { post } from '../../../helpers/api';

    export default {
        props: ['data', 'students', 'program_id','lesson_type_id'],
        data() {
            return {
                errors: '',
                formSending: false,
                form: [],
                step: 0,
                rateProgramTab: 0,
                rateLessonTypeTab: 0,
                default_rate: {
                    cost: '',
                    count_lessons: '',
                    duration: '',
                }
            }
        },
        watch: {
        },
        created() {
            let _this = this;
            this.students.forEach(function(student) {
                _this.form.push({
                    student_name: student.user.name,
                    student_id: student.id,
                    program_id: _this.program_id ? _this.program_id : student.free_student_rates[0].program_id,
                    lesson_type_id: _this.lesson_type_id ?  _this.lesson_type_id : student.free_student_rates[0].lesson_type_id,
                    cost: '',
                    count_lessons: '',
                    duration: '',
                    start_balance: '',
                });
            });
        },
        mounted() {
        },
        methods: {
            blockProgram(id, index) {
                let value = true;
                let student = this.students[index];
                let lesson_type_id = this.form[index].lesson_type_id;
                if(student.free_student_rates.length > 0 ) {
                    student.free_student_rates.forEach(function (item) {
                        if(id === item.program_id && lesson_type_id === item.lesson_type_id) value = false;
                    });
                }
                return value;
            },
            blockLessonType(id, index) {
                let value = true;
                let student = this.students[index];
                let program_id = this.form[index].program_id;
                if(student.free_student_rates.length > 0 ) {
                    student.free_student_rates.forEach(function (item) {
                        if(id === item.lesson_type_id && program_id === item.program_id) value = false;
                    });
                }
                return value;
            },
            selectRateProgram(value, index) {
                let student = this.students[index];
                if(!this.blockProgram(this.data.programs[value].id,index)) {
                    this.rateProgramTab = value;
                    this.form[index].program_id = this.data.programs[value].id;
                }
            },
            selectRateLessonType(value, index) {
                let student = this.students[index];
                if(!this.blockLessonType(this.data.lesson_types[value].id,index)) {
                    this.rateLessonTypeTab = value;
                    this.form[index].lesson_type_id = this.data.lesson_types[value].id;
                }
            },
            getProgramName(index) {
                let name = '';
                let id = this.form[index].program_id;
                this.data.programs.forEach(function(program) {
                    if(program.id === id) {
                        name = program.name;
                    }
                });
                return name;
            },
            getLessonTypeName(index) {
                let name = '';
                let id = this.form[index].lesson_type_id;
                this.data.lesson_types.forEach(function(lesson_type) {
                    if(lesson_type.id === id) {
                        name = lesson_type.additional_name;
                    }
                });
                return name;
            },
            sendForm() {

                this.formSending = true;

                let _this = this;

                post(_this, '/api/student-rates-save', this.form, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('success');


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
            foundDefault(form) {
                let found = false;
                if(this.data.rates && this.data.rates.length>0) {
                    this.data.rates.forEach(function(rate) {
                        if(form.program_id && form.lesson_type_id) {
                            if(!found && rate.program_id === form.program_id && (rate.type+1) === form.lesson_type_id) {
                                found = true;
                            }
                        }
                    });
                }
                return found;
            },
            setDefault(form) {

                let found = false;
                let default_rate = {
                    cost: '',
                    count_lessons: '',
                    duration: '',
                };
                if(this.data.rates && this.data.rates.length>0) {
                    this.data.rates.forEach(function(rate) {
                        if(form.program_id && form.lesson_type_id) {
                            if(!found && rate.program_id === form.program_id && (rate.type+1) === form.lesson_type_id) {
                                found = true;
                                default_rate.cost = rate.cost;
                                default_rate.count_lessons = rate.countLesson;
                                default_rate.duration = rate.duration;
                            }
                        }
                    });
                }
                form.cost = default_rate.cost;
                form.count_lessons = default_rate.count_lessons;
                form.duration = default_rate.duration;
            }
        }

    }

</script>