<template>

    <div v-if="form">
        <b-modal v-if="data" ref="modal" size="lg" :title="title">
            <div class="container">
                <div v-bind:class="{ 'has-error': errors && errors.name }" class="form-group row">
                    <label class="col-4">
                        {{$tc('holidays.list.name')}}
                    </label>
                    <input v-model="form.name" class="form-control col-8" :placeholder="$tc('holidays.form.enter_name')"/>
                    <form-error v-if="errors && errors.name" :errors="errors">
                        {{ errors.name[0] }}
                    </form-error>
                </div>
                <div v-bind:class="{ 'has-error': errors && errors.start_day }" class="form-group row">
                    <label class="col-4">
                        {{$tc('holidays.list.start_day')}}
                    </label>
                    <input type="number" min="1" :max="month_days[form.start_month]" v-model="form.start_day" class="form-control col-8" :placeholder="$tc('holidays.form.enter_start_day')"/>
                    <form-error v-if="errors && errors.start_day" :errors="errors">
                        {{ errors.start_day[0] }}
                    </form-error>
                </div>
                <div v-bind:class="{ 'has-error': errors && errors.start_month }" class="form-group row">
                    <label class="col-4">
                    {{$tc('holidays.list.start_month')}}
                    </label>
                    <select v-model="form.start_month" class="form-control col-8" :placeholder="$tc('holidays.form.enter_start_month')">
                        <option value="">{{$tc('holidays.form.select_month')}}</option>
                        <option value="1">{{$tc('holidays.months.january')}}</option>
                        <option value="2">{{$tc('holidays.months.february')}}</option>
                        <option value="3">{{$tc('holidays.months.march')}}</option>
                        <option value="4">{{$tc('holidays.months.april')}}</option>
                        <option value="5">{{$tc('holidays.months.may')}}</option>
                        <option value="6">{{$tc('holidays.months.june')}}</option>
                        <option value="7">{{$tc('holidays.months.july')}}</option>
                        <option value="8">{{$tc('holidays.months.august')}}</option>
                        <option value="9">{{$tc('holidays.months.september')}}</option>
                        <option value="10">{{$tc('holidays.months.october')}}</option>
                        <option value="11">{{$tc('holidays.months.november')}}</option>
                        <option value="12">{{$tc('holidays.months.december')}}</option>
                    </select>
                    <form-error v-if="errors && errors.start_month" :errors="errors">
                        {{ errors.start_month[0] }}
                    </form-error>
                </div>
                <div v-bind:class="{ 'has-error': errors && errors.days_number }" class="form-group row">
                    <label class="col-4">
                    {{$tc('holidays.list.days_number')}}
                    </label>
                    <input type="number" min="1"  v-model="form.days_number" class="form-control col-8" :placeholder="$tc('holidays.form.enter_days_number')"/>
                    <form-error v-if="errors && errors.days_number" :errors="errors">
                        {{ errors.days_number[0] }}
                    </form-error>
                </div>
            </div>
            <div slot="modal-footer">
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="sendForm"
                        :disabled="(formSending? true : false)"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ form.id ? $tc("participants.form.update") :  $tc("participants.form.create") }}
                </button>
            </div>


        </b-modal>
    </div>


</template>



<script>

    import { post } from '../../../../../helpers/api'

    export default {

        props: ['data', '_form'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                defaultMonth:'',
                region:'',
                district:'',
                title:'',
                month_days:{
                    1:31,
                    2:28,
                    3:31,
                    4:30,
                    5:31,
                    6:30,
                    7:31,
                    8:31,
                    9:30,
                    10:31,
                    11:30,
                    12:31
                }
            }
        },
        created() {
            this.form = this._form ? this._form : this.newHoliday();
        },
        mounted() {
            this.title = this.form.id ? this.$tc("holidays.form.edit") : this.$tc("holidays.form.create");
        },
        components: {
            FormError : require('../../../../../components/FormError.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;

                let _this = this;


                post(_this, '/api/holiday-save', this.form, function () {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newHoliday();
                    _this.$emit('formSending');


                }, function (error) {

                    _this.formSending = false;
                    _this.errors = error.response.data;

                });
            },
            showModal(x) {
                console.log(x);
                if(x.id){
                    this.form=x;
                }
                else if(x!=="all"&&x!==""){
                    this.form=this.newHoliday();
                    this.form.start_month=x;
                }
                else {
                    this.form=this.newHoliday();
                }
                this.title = this.form.id ? this.$tc("holidays.form.edit") : this.$tc("holidays.form.create");
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            newHoliday() {
                return {
                    name:"",
                    start_day:"",
                    start_month:"",
                    days_number:""
                }
            },
        }
    }
</script>