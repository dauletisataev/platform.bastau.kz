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
                        <label style="font-weight:500" class="d-block">Категория</label>
                        <select class="form-control" v-model="form.project_id">
                            <option value="">выберите</option>
                            <option v-for="project in $common.data.projects" :value="project.id">{{ project.name }}</option>
                        </select>
                        <error-alert v-if="errors && errors.program_id">
                            {{ errors.program_id[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Обложка</label>
                        <div class="fullWidthButton">
                            <lesson-template-image @uploaded="form.image = $refs.image.form.image" ref="image" :form="form" :accept="'image/jpeg,image/png,image/gif'"></lesson-template-image>
                        </div>
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
                            <b-dropdown-item class="py-2" v-for="type in $common.data.lesson_template_types" :key="'ltt'+type.value" @click="form.type=type.value">
                                <div class="h6 mb-0">{{type.name}}</div>
                                <div class="small">description</div>
                            </b-dropdown-item>
                        </b-dropdown>
                        <error-alert v-if="errors && errors.type">
                            {{ errors.type[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group row">
                        <label class="col" >tests_present</label>
                        <input type="checkbox" v-model="form.has_test"/>
                        <error-alert v-if="errors && errors.program_id">
                            {{ errors.program_id[0] }}
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
                    project_id: '',
                    has_test:false,
                },
                errors: '',
                formSending: false
            }
        },
        components: {
            LessonTemplateImage: require('../../components/LessonTemplateImage.vue'),
            ErrorAlert: require('../../components/ErrorAlert.vue')
        },
        methods: {
            sendForm() {
                this.form.language=this.$i18n.locale;
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
                let x = "select type";
                this.$common.data.lesson_template_types.map((type)=>{
                    if(type.value === val)  x = type.name;
                })
                return x;
            }

        }
    }
</script>