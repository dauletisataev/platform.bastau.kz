<template>

    <div v-if="form">
        <b-modal v-if="data" ref="modal" size="lg" :title="title">
                <div v-bind:class="{ 'has-error': errors && errors.new_first_name }" class="form-group row">
                    <label class="col-3 col-form-label">Имя</label>
                    <div class="col-9">
                        <input v-model="form.new_first_name" class="form-control" placeholder="Ерсултан"/>
                        <form-error v-if="errors && errors.new_first_name" :errors="errors">
                            {{ errors.new_first_name[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_last_name }" class="form-group row">
                    <label class="col-3 col-form-label">Фамилие</label>
                    <div class="col-9">
                        <input v-model="form.new_last_name" class="form-control" placeholder="Нагаштай"/>
                        <form-error v-if="errors && errors.new_last_name" :errors="errors">
                            {{ errors.new_last_name[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_patronymic }" class="form-group row">
                    <label class="col-3 col-form-label">Отчество</label>
                    <div class="col-9">
                        <input v-model="form.new_patronymic" class="form-control" placeholder="Габитович"/>
                        <form-error v-if="errors && errors.new_patronymic" :errors="errors">
                            {{ errors.new_patronymic[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_iin }" class="form-group row">
                    <label class="col-3 col-form-label">ИИН</label>
                    <div class="col-9">
                        <input v-model="form.new_iin" class="form-control" placeholder="951030300555" @change="computeBirthDateAndGender()"/>
                        <form-error v-if="errors && errors.new_iin" :errors="errors">
                            {{ errors.new_iin[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_gender }" class="form-group row">
                    <label class="col-3 col-form-label">Пол</label>
                    <div class="col-9">
                        <input v-model="form.new_gender" placeholder="Введите правильный ИИН"/>
                        <form-error v-if="errors && errors.new_gender" :errors="errors">
                            {{ errors.new_gender[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_birth_date }" class="form-group row">
                    <label class="col-3 col-form-label">Дата рождения</label>
                    <div class="col-9">
                        <input v-model="form.new_birth_date"  placeholder="Введите правильный ИИН"/>
                        <form-error v-if="errors && errors.new_birth_date" :errors="errors">
                            {{ errors.new_birth_date[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_phone }" class="form-group row">
                    <label class="col-3 col-form-label">Телефон</label>
                    <div class="col-9">
                        <masked-input v-model="form.new_phone" class="form-control" mask="1 (111) 111 11 11" placeholder="8 (777) 777 77 77"/>
                        <form-error v-if="errors && errors.new_phone" :errors="errors">
                            {{ errors.new_phone[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_email }" class="form-group row">
                    <label class="col-3 col-form-label">Электронный адрес </label>
                    <div class="col-9">
                        <input type="email" v-model="form.new_email" class="form-control"  placeholder="yersultan.nagashtay@nu.edu.kz"/>
                        <form-error v-if="errors && errors.new_email" :errors="errors">
                            {{ errors.new_email[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_disability }" class="form-group row">
                    <label class="col-3 col-form-label">Инвалидность </label>
                    <div class="col-9">
                        <select v-model="form.new_disability" >
                            <option value="0">Нет инвалидности</option>
                            <option value="1">Первая степень инвалидности</option>
                            <option value="2">Вторая степень инвалиднось</option>
                        </select>
                        <form-error v-if="errors && errors.new_disability" :errors="errors">
                            {{ errors.new_disability[0] }}
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
                        <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ form.id ? 'Сохранить' : 'Добавить' }}
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
                title: ''
            }
        },
        computed: {
            roles() {
                var arr=[];
                if(this.$common.data) {
                    this.$common.data.forEach(function(role) {
                        arr.push(role);
                    });
                }
                return arr;
            }
        },
        created() {
            this.form = this._form ? this._form : this.newParticipant();
            this.step=1;
        },
        mounted() {
            this.title = this.form.id ? 'Редактировать заявку' : 'Добавить заявку';
            this.step=1;
        },
        components: {
            FormError : require('../../../../../components/FormError.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;

                let _this = this;


                post(_this, '/api/participant-save', this.form, function () {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newParticipant();
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
            newParticipant() {
                return {
                    new_first_name: '',
                    new_last_name:'',
                    new_patronymic:'',
                    new_gender:'',
                    new_disability:'',
                    new_phone:'',
                    new_iin:'',
                    new_email:'',
                }
            },
            computeBirthDateAndGender(){
                if(!this.form.new_iin) return null;
                else console.log(this.form.new_iin);
                if (this.form.new_iin.length === 12){
                    this.form.new_birth_date =  this.form.new_iin[4]+this.form.new_iin[5]+'.'
                          +this.form.new_iin[2]+this.form.new_iin[3]+'.'
                          +this.form.new_iin[0]+this.form.new_iin[1]
                    let number = this.form.new_iin[6]*1;
                    if(number%2 ===0){
                        this.form.new_gender= "F";
                    }else{
                        this.form.new_gender = "M";
                    }
                }
            },
        }
    }
</script>