<template>
    <div id="group-form" v-if="form">
        <b-modal ref="modal" size="lg" title="Изменить расписание">

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
                        class="btn btn-secondary"
                        @click="$refs.modal.hide()"
                >
                    Отменить
                </button>
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="sendForm"
                        :disabled="formSending? true : false"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> Сохранить
                </button>
            </div>
        </b-modal>
    </div>


</template>



<script>

    import { post } from '../../../helpers/api';

    export default {

        props: ['group_id', '_form'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                temp: false
            }
        },
        watch: {
            group_id() {
                this.form = this.newForm();
            }
        },
        created() {
            this.form = this.newForm();
        },
        components: {
            Schedules : require('./../../../components/Schedules.vue'),
        },
        methods: {
            sendForm() {
                this.formSending = true;

                let _this = this;

                post(_this, '/api/group-schedule-add', this.form, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.newForm();
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
            newForm() {

                return {
                    group_id: this.group_id,
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