<template>
    <b-modal ref="modal" :title="title">
<!-- Yersultan Form for creating group-->
        <div v-bind:class="{ 'has-error': errors && errors.project_id }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.select_project_id')}}</label>
            <div class="col-7">
                <select v-model="form.project_id" >
                    <option v-for="project in project_ids" :value="project.id">{{project.name}}</option>
                </select>
                <form-error v-if="errors && errors.project_id" :errors="errors">
                    {{ errors.project_id[0] }}
                </form-error>
            </div>
        </div>

        <div v-bind:class="{ 'has-error': errors && errors.start_date }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.select_start_date')}}</label>
            <div class="col-7">
                <input type="date"  v-model="form.start_date" />
                <form-error v-if="errors && errors.start_date" :errors="errors">
                    {{ errors.start_date[0] }}
                </form-error>
            </div>
        </div>

        <div v-bind:class="{ 'has-error': errors && errors.language }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.select_language')}}</label>
            <div class="col-7">
                <select v-model="form.language" >
                    <option value="ru">{{$tc('groups.form.available_languages.ru')}}</option>
                    <option value="kz">{{$tc('groups.form.available_languages.kz')}}</option>
                </select>
                <form-error v-if="errors && errors.language" :errors="errors">
                    {{ errors.language[0] }}
                </form-error>
            </div>
        </div>

        <div v-bind:class="{ 'has-error': errors && errors.capacity }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.select_capacity')}}</label>
            <div class="col-7">
                <input type="number" v-model="form.capacity" />
                <form-error v-if="errors && errors.capacity" :errors="errors">
                    {{ errors.capacity[0] }}
                </form-error>
            </div>
        </div>
        <div v-bind:class="{ 'has-error': errors && errors.online }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.select_if_online')}}</label>
            <div class="col-7">
                <input type="checkbox" v-model="form.online" />
                <form-error v-if="errors && errors.online" :errors="errors">
                    {{ errors.online[0] }}
                </form-error>
            </div>
        </div>

        <div v-bind:class="{ 'has-error': errors && errors.trainers }" class="form-group row">
            <label class="col-5 col-form-label">{{$tc('groups.form.trainers')}}</label>
            <div class="col-7">
                <!-- <input type="checkbox" v-model="form.online" /> -->
                <select v-model="form.trainer">
                    <option :value="trainer.id" v-for="trainer in $common.data.trainers">
                        {{ trainer.user.first_name }} {{ trainer.user.last_name }}
                    </option>
                </select>
                <form-error v-if="errors && errors.trainers" :errors="errors">
                    {{ errors.trainer[0] }}
                </form-error>
            </div>
        </div>

        <button @click="sendForm" slot="modal-footer"class="btn btn-primary">{{$tc('groups.form.submit_group_form')}}</button>
        <!-- end form-->

    </b-modal>
</template>

<script>

    import { get,post } from '../../../../../helpers/api'

    export default {
        props: ['data', '_form'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                title: '',
                project_ids:''
            }
        },
        created() {
            this.form = this._form ? this._form : this.newGroup();
        },
        mounted() {
            this.title = this.form.id ? this.$tc('groups.form.editHeader') : this.$tc('groups.form.createHeader');
            let _this = this;
            get(_this, 'api/projects/get-all',{}, function (response) {
                _this.project_ids = response.data;
            }, function () {

            });
        },
        components: {
            FormError : require('../../../../../components/FormError.vue')
        },
        methods:{
            sendForm() {
                this.formSending = true;
                let _this = this;
                post(_this, '/api/group-save', this.form, function () {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newGroup();
                    _this.$emit('formSending');
                    _this.$emit('updated');
                }, function (error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            },
            showModal(){
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            newGroup() {
                return {
                    project_id:"",
                    start_date:"",
                    language:"",
                    capacity:"",
                    online:"",
                    trainer: "",
                }
            },
        }
    }
</script>