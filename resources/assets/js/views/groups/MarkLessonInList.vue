<template>

    <b-modal v-if="lessons && students" ref="modal" size="lg" title="Отметить занятие">


        <h6 class="js-title-step mb-4"><span class="badge badge-primary badge-pill">{{ step }}</span> {{ stepTitle }}</h6>

        <div v-show="step == 1">
            <table class="table table-sm">
                <thead class="thead-default">
                <tr>
                    <th>Тема</th>
                    <th>Дата</th>
                    <th>Время</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="lesson in lessons" v-if="!lesson.passed">
                    <td>{{ lesson.title }}</td>
                    <td>{{ lesson.date }}</td>
                    <td>{{ lesson.time }}</td>
                    <td><label class="custom-control custom-radio"> <input v-model="lesson_id" :value="lesson.id" name="lesson" type="radio" checked="" class="custom-control-input"> <span class="custom-control-indicator"></span> <span class="custom-control-description"></span> </label></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-show="step == 2">
            <table class="table table-sm" v-if="lesson_id">
                <thead class="thead-default">
                <tr>
                    <th>Имя</th>
                    <th v-if="$user.isTeacher()">Должник</th>
                    <th v-if="!$user.isTeacher()">Баланс</th>
                    <th>Был</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="student in students" v-if="student.in_archive!=1 && student.pivot.deleted==0">
                    <td>{{ student.user.name }}</td>
                    <td v-if="$user.isTeacher()"><span v-if="student.balance<0" class="badge badge-pill badge-danger">Да</span></td>
                    <td class="h5" v-if="!$user.isTeacher()"><balance :value="student.balance"></balance></td>
                    <td v-if="!$common.existIdInArray(lesson_id, student.visit_lessons)">
                        <span v-if="student.is_balance_freeze==1">Баланс заморожен</span>
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
                    <td v-else>Отмечен</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div slot="modal-footer">
            <button type="button" :disabled="step <= 1" class="btn btn-secondary" @click="prev()">Назад</button>
            <button type="button" :disabled="disabledNext" class="btn btn-primary" @click="next()"><i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ nextText }}</button>
        </div>
    </b-modal>

</template>


<script>

    import { post } from '../../helpers/api';

    export default {

        props: ['lessons', 'students'],

        data() {

            return {
                step: 1,
                formSending: false,
                lesson_id: '',
                selectedStudentIds: [],
                respectfulMissIds: [],
            }

        },
        computed: {
            stepTitle() {
                return this.step == 1 ? 'Выберите занятие' : 'Отметьте учеников';
            },
            disabledNext() {

                if(this.formSending)
                    return true;

                if (this.step != 2){
                    return !this.lesson_id;
                }
            },
            nextText() {
                return this.step == 2 ? 'Сохранить' : 'Дальше'
            }
        },
        methods: {
            clear() {
                this.step = 1;
                this.lesson_id = '';
                this.selectedStudentIds = [];
                this.respectfulMissIds = [];
            },
            showModal() {
                this.clear();
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            next() {

                if (this.step == 2){
                    this.submit();
                    return false;
                }

                this.step = this.step + 1;

            },
            prev() {

                this.step = this.step - 1;

            },
            submit() {

                this.formSending = true;

                let _this = this;

                post(_this, '/api/lesson-mark/'+this.lesson_id, {visit_student_ids: this.selectedStudentIds, respectfulMissIds: this.respectfulMissIds}, function () {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('formSending');
                }, function () {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });


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
        }

    }

</script>