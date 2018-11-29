<template>
    <b-modal v-if="student && group" ref="modal" :title="title">

        <div class="form-group row" v-bind:class="{ 'has-error': errors && errors.ind }" v-if="form.ind">
            <label class="col-3 col-form-label">Индивидуальная стоимость</label>
            <div class="col-9">
                <input type="number" v-model="form.ind" class="form-control" :placeholder="calculatecost()">
                <form-error v-if="errors && errors.ind" :errors="errors">
                    {{ errors.ind[0] }}
                </form-error>
            </div>
        </div>
        <div v-bind:class="{ 'has-error': errors && errors.rate_errors }"  class="form-group row">
            <label class="col-3 col-form-label">Тариф</label>
            <div class="col-9">
                <v-select :on-change="selectRate" label="label" value="id" :value.sync="form.rate_select" :options="options" placeholder="Выберите тариф"></v-select>
                <div v-if="form.rate==1" style="margin-top: 1rem">
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
</template>

<script>

    import { post } from '../../../helpers/api';

    export default {
        props: ['student', 'group'],

        data(){
            return {

                title: '',
                errors: '',
                formSending: false,
                form: {
                    ind: 0,
                    group_id: '',
                    rate: '',
                    custom_rate_cost: '',
                    custom_rate_countLesson: '',
                    custom_rate_duration: '',
                    custom_rate_months: ''
                },
                options: [
                    {label: 'тариф группы', id: 0},
                    {label: 'ввести вручную', id: 1},
                ],

            }
        },
        watch: {
            student(student) {
                this.title ='Изменить стоимость';
                this.form.ind = (student.pivot.ind || student.pivot.ind==0) ? student.pivot.ind : '';
                this.form.group_id = this.group.id;
                this.form.rate = student.pivot.rate;
                this.form.custom_rate_cost = student.pivot.custom_rate_cost;
                this.form.custom_rate_countLesson = student.pivot.custom_rate_countLesson;
                this.form.custom_rate_duration = student.pivot.custom_rate_duration;
                this.form.custom_rate_months = student.pivot.custom_rate_months;
                if (this.form.custom_rate_cost==0) this.form.custom_rate_cost='';
                if (this.form.custom_rate_duration==0) this.form.custom_rate_duration='';
                if (this.form.custom_rate_countLesson==0) this.form.custom_rate_countLesson='';
                if (this.form.custom_rate_months==0) this.form.custom_rate_months='';
                this.form.rate_select = {
                    id: this.form.rate,
                    label: (this.form.rate==0) ? 'тариф группы' : 'ввести вручную'
                };
            }

        },
        mounted(){

            this.form.value = 0;
            this.form.group_id = '';
            this.form.rate_select = this.form.rate ? {
                id: this.form.rate,
                label: (this.form.rate==0) ? 'тариф группы' : 'ввести вручную'
            } : '';
        },
        components: {
            FormError : require('./../../../components/FormError.vue')
        },
        methods:{
            calculatecost() {
                if(this.group) {
                    switch (this.group.rate_id) {
                        case null: return this.group.cost; break;
                        case 0: return Math.floor(this.group.custom_rate_cost / this.group.custom_rate_countLesson); break;
                        default : return Math.floor(this.group.rate.cost / this.group.rate.countLesson); break;
                    }
                }
            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            submit() {

                this.formSending = true;

                let _this = this;

                post(_this, '/api/student/'+this.student.id+'/set-ind', _this.form, function () {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('formSending');
                }, function (error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });

            },
            selectRate(val) {
                if (val)
                    this.form.rate = val.id;
                this.form.rate_select = val;
            },

        }
    }
</script>