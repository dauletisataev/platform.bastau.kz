
<template>
    <div>
        <b-modal v-if="lesson_id" ref="modal" size="lg" title="Отметить занятие">
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
                <tr v-for="student in students" v-if="student.in_archive!=1 && student.pivot.deleted!=1">

                    <td>{{ student.user.name }}</td>
                    <td v-if="$user.isTeacher()">
                        <span v-if="prognosis(student) == 0" class="badge badge-pill badge-danger">{{ prognosis(student) }}</span>
                        <span v-if="prognosis(student) > 0 && prognosis(student) <3" class="badge badge-pill badge-warning">{{ prognosis(student) }}</span>
                        <span v-if="prognosis(student) > 2" class="badge badge-pill badge-success">{{ prognosis(student) }}</span>
                    </td>
                    <td class="h5" v-if="!$user.isTeacher()"><balance :value="balance(student)"></balance></td>
                    <td class="pl-0" v-if="!$user.isTeacher() && group">{{cost(student)}} тг</span>
                    </td>
                    <td v-if="!$common.existIdInArray(lesson_id, student.visit_lessons)">
                        <span v-if="isBalanceFreeze(student)==1">Баланс заморожен</span>
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
                    <td v-else>Отмечен</td>
                </tr>
                </tbody>
            </table>

            <div slot="modal-footer">
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="submit"
                        :disabled="formSending? true : false"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> Сохранить
                </button>
            </div>

        </b-modal>

    </div>

</template>

<script>

    import { post, get } from '../../../../helpers/api';

    export default {

            props: ['lesson_id', 'students', 'group','duration'],

        data() {

            return {
                formSending: false,
                selectedStudentIds: [],
                lesson: '',
                respectfulMissIds: [],
            }

        },



        watch: {
            lesson_id(lesson_id) {
                this.selectedStudentIds = [];
                this.respectfulMissIds = [];
            }
        },

        methods: {
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            submit() {

                this.formSending = true;

                let _this = this;

                post(_this, '/api/lesson-mark/'+_this.lesson_id, {visit_student_ids: _this.selectedStudentIds, respectfulMissIds: this.respectfulMissIds}, function(){

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('formSending');

                }, function(error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                })

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

        },

        created () {
        },

    }

</script>