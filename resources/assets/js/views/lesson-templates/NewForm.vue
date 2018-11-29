<template>
    <div class="col-10 offset-2">
        <div class="w-50 mx-auto mb-5">
            <div class="card">
                <div class="card-block">
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Название программы</label>
                        <input
                                type="text"
                                v-model="form.name"
                                class="form-control form-control-lg"
                                placeholder="Укажите название программы"
                        >
                        <error-alert v-if="errors && errors.name">
                            {{ errors.name[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Обложка</label>
                        <div class="fullWidthButton">
                            <lesson-template-image @uploaded="form.image = $refs.image.form.image" ref="image" :form="form" :accept="'image/jpeg,image/png,image/gif'"></lesson-template-image>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Роль</label>
                        <select class="form-control" v-model="form.role_id">
                            <option value="">выберите</option>
                            <option v-for="role in $common.data.roles" :value="role.id">{{ role.instrumental }}</option>
                        </select>
                        <error-alert v-if="errors && errors.role_id">
                            {{ errors.role_id[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Тип программы</label>
                        <b-dropdown class="bDropdownFullWidth">
                            <div slot="text">
                                <span class="pull-left">
                                    {{ templateType(form.type) }}
                                </span>
                                <span class="fa fa-angle-down pull-right"></span>
                            </div>
                            <b-dropdown-item class="py-2" @click="form.type = 0">
                                <div class="h6 mb-0">с преподавателем</div>
                                <div class="small">занятия в офисе или по скайпу с участием преподавателя</div>
                            </b-dropdown-item>
                            <b-dropdown-item class="py-2" @click="form.type = 1">
                                <div class="h6 mb-0">онлайн</div>
                                <div class="small">самостоятельное изучение без участие преподавателя</div>
                            </b-dropdown-item>
                        </b-dropdown>
                        <error-alert v-if="errors && errors.type">
                            {{ errors.type[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group" v-if="form.type == 1">
                        <label style="font-weight:500" class="d-block">Стоимость программы</label>
                        <input v-model="form.cost" type="text" class="form-control">
                        <error-alert v-if="errors && errors.cost">
                            {{ errors.cost[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Категория</label>
                        <select class="form-control" v-model="form.program_id">
                            <option value="">выберите</option>
                            <option v-for="program in $common.data.programs" :value="program.id">{{ program.name }}</option>
                        </select>
                        <error-alert v-if="errors && errors.program_id">
                            {{ errors.program_id[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Уровень языка</label>
                        <select class="form-control" v-model="form.level_id">
                            <option value="">выберите</option>
                            <option v-for="level in $common.data.levels" :value="level.id">{{ level.name }}</option>
                        </select>
                        <error-alert v-if="errors && errors.level_id">
                            {{ errors.level_id[0] }}
                        </error-alert>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block mt-4" @click="sendForm()" :disabled="!!formSending">
                        <i v-show="formSending" class="fa fa-spinner fa-spin"></i> Создать программу
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    import { post } from '../../helpers/api';

    export default {
        data() {
            return {
                form: {
                    name: '',
                    image: '',
                    type: '',
                    cost: '',
                    program_id: '',
                    level_id: '',
                    role_id: ''
                },
                errors: '',
                formSending: false
            }
        },
        watch: {
            'form.cost'(value){
                if (!/(^[1-9]{1}[0-9]*$)|(^[0]{1}$)/.test(value)) this.form.cost = '';
            },
        },
        components: {
            LessonTemplateImage: require('../../components/LessonTemplateImage.vue'),
            ErrorAlert: require('../../components/ErrorAlert.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;
                let _this = this;

                post(_this, '/api/lesson-template-save', this.form, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    if(response.data) _this.$router.push({ name: 'lesson_template', params:{id: response.data} });

                }, function (error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            },
            templateType(val) {
                switch(val) {
                    case 0: return "с преподавателем";
                    break;
                    case 1: return "онлайн";
                    break;
                    default: return "выберите тип программы";
                }
            }

        }
    }
</script>