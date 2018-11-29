<template>

    <b-modal v-if="data" ref="modal" size="lg" :title="title">

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
                    <v-select :on-change="selectOfficeCabinet" label="name" :value="form.office_cabinet" :options="cabinets" placeholder="Выберите аудиторию"></v-select>
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

            <div class="form-group" v-if="form.id && !form.is_clone">
                <div class="col-9 offset-3">
                    <label class="custom-control custom-checkbox">
                        <input v-model="moveAll" type="checkbox" class="custom-control-input">
                        <span class="custom-control-indicator"></span>
                        также перенести последующие занятия
                    </label>
                </div>
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


</template>



<script>

    import { post, get } from '../../../../helpers/api';

    export default {

        props: ['data', '_form', 'group', 'lessonDate'],

        data() {
            return {
                errors: [],
                formSending: false,
                cabinets: this.group.office.cabinets,
                form: '',
                title: '',
                moveAll: false,

            }
        },
        created() {
            this.form = this._form ? this._form : this.newLesson();
            this.title = 'Запланировать занятие';
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

                post(_this, '/api/lesson-save', this.form, function (response) {

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
                    date: this.lessonDate ? this.lessonDate.date : '',
                    time: this.lessonDate ? this.lessonDate.time : '',
                    duration: this.group.true_duration,

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

            }

        }



    }
</script>