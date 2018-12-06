<template>
    <div v-if="form">
    	<b-modal ref="modal" size="lg" :title="title">
            
            <div v-bind:class="{ 'has-error': errors && errors.photo }" class="form-group row">
                <label class="col-3 col-form-label">Фото</label>
                <div class="col-9">
                    <user-photo :user="form" :accept="'image/jpeg,image/png,image/gif'"></user-photo>
                    <form-error v-if="errors && errors.photo" :errors="errors">
                        {{ errors.photo[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.last_name }" class="form-group row">
                <label class="col-3 col-form-label">Фамилия</label>
                <div class="col-9">
                    <input v-model="form.last_name" class="form-control" type="text" placeholder="Фамилия">
                    <form-error v-if="errors && errors.last_name" :errors="errors">
                        {{ errors.last_name[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.first_name }" class="form-group row">
                <label class="col-3 col-form-label">Имя</label>
                <div class="col-9">
                    <input v-model="form.first_name" class="form-control" type="text" placeholder="Имя">
                    <form-error v-if="errors && errors.first_name" :errors="errors">
                        {{ errors.first_name[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.patronymic }" class="form-group row">
                <label class="col-3 col-form-label">Отчество</label>
                <div class="col-9">
                    <input v-model="form.patronymic" class="form-control" type="text" placeholder="Отчество">
                    <form-error v-if="errors && errors.patronymic" :errors="errors">
                        {{ errors.patronymic[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.gender }" class="form-group row">
                <label class="col-3 col-form-label">Пол</label>
                <div class="col-9">
                    <select v-model="form.gender">
                        <option class="form-control" value="M">Мужчина</option>
                        <option class="form-control" value="F">Женщина</option>
                    </select>
                    <form-error v-if="errors && errors.gender" :errors="errors">
                        {{ errors.gender[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.iin }" class="form-group row">
                <label class="col-3 col-form-label">ИИН</label>
                <div class="col-9">
                    <input v-model="form.iin" class="form-control" type="text" placeholder="ИИН">
                    <form-error v-if="errors && errors.iin" :errors="errors">
                        {{ errors.iin[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.phone }" class="form-group row">
                <label class="col-3 col-form-label">Телефон</label>
                <div class="col-9">
                    <masked-input v-model="form.phone" class="form-control" mask="1 (111) 111 11 11" placeholder="8 (707) 465 48 12"/>
                    <form-error v-if="errors && errors.phone" :errors="errors">
                        {{ errors.phone[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.email }" class="form-group row">
                <label class="col-3 col-form-label">Email</label>
                <div class="col-9">
                    <input v-model="form.email" class="form-control" type="text" placeholder="email">
                    <form-error v-if="errors && errors.email" :errors="errors">
                        {{ errors.email[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.password }" class="form-group row">
                <label class="col-3 col-form-label">Пароль</label>
                <div class="col-9">
                    <input v-model="form.password" class="form-control" type="text" placeholder="Пароль">
                    <form-error v-if="errors && errors.password" :errors="errors">
                        {{ errors.password[0] }}
                    </form-error>
                </div>
            </div>

            <div v-bind:class="{ 'has-error': errors && errors.password_confirmation }" class="form-group row">
                <label class="col-3 col-form-label">Пароль еще раз</label>
                <div class="col-9">
                    <input v-model="form.password_confirmation" class="form-control" type="text" placeholder="Пароль">
                    <form-error v-if="errors && errors.password_confirmation" :errors="errors">
                        {{ errors.password_confirmation[0] }}
                    </form-error>
                </div>
            </div>
            <div class="row" v-if="!form.id"><div class="col-3"></div>
                <div class="col-9">
                    <button
                        type="button"
                        class="btn"
                        @click="randpass"
                        :disabled="sending? true : false"
                >Генерировать пароль</button>
                </div>
            </div>

            <div slot="modal-footer">
                <button
                        type="button"
                        class="btn btn-primary"
                        @click="send"
                        :disabled="(sending? true : false)"
                >
                    <i v-show="sending" class="fa fa-spinner fa-spin"></i> {{ form.id ? 'Сохранить' : 'Добавить' }}
                </button>
            </div>

    	</b-modal>
    </div>
</template>

<script>
    import { post } from './../../helpers/api.js';
    import UserPhoto from './../../components/UserPhoto.vue';
    import FormError from './../../components/FormError.vue';

    export default {
        created() {
            this.form = this._form ? this._form : this.newTrainer();
        },

        mounted() {
            this.title = this.form.id ? 'Редактировать тренера?' : "Добавить тренера"
        },

        props: ['_form'],
    	
        data() {
    		return {
    			form: '',
                sending: false,
                url: '/api/trainers/',
                errors: [],
                title: ''
    		}
    	},

        components: {
            UserPhoto,
            FormError
        },
        methods: {
            send() {
                this.sending = true;
                let self = this;
                post(self, 
                    self.url, 
                    this.form, 
                    (res) => {
                        self.sending = false,
                        self.errors = [];
                        self.hideModal();
                        self.form = self.form.id ? self.form : self.newTrainer();
                        self.hideModal();
                    }, 
                    (err) => {
                        self.sending = false;
                        self.errors = err.response.data;
                        console.log(err);
                    });

            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            newTrainer() {
                return {
                    id: '',
                    photo: '',
                    name: '',
                    phone: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_id: 2,
                }
            },
            randpass() {
                var chars = "abcdefghijklmnopqrstuvwxyz1234567890";
                var pass = "";
                for (var x = 0; x < 8; x++) {
                    var i = Math.floor(Math.random() * chars.length);
                    pass += chars.charAt(i);
                }
                this.form.password=pass;
                this.form.password_confirmation=pass;
            }
        }
    };
</script>