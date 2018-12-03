<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner" style="z-index:3005; left: 15px">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2 pr-5" v-if="template && template.id">
            <div class="bg-primary text-white rounded p-4 mb-2" >
                <div class="small text-info">
                    <router-link to="/control/lesson-templates" class="text-info"><span class="fa fa-angle-left mr-2"></span>Все программы</router-link>
                </div>
                <div class="display-4" style="font-size: 2.5rem;">{{ template.name }}</div>
            </div>
            <div class="clearfix mb-4">
                <div class="pull-left">
                    <div class="btn-group">
                        <button type="button" @click="activeTab = 'lessons'" class="btn btn-secondary" :class="{'active': activeTab == 'lessons'}">
                            Содержимое
                        </button>
                        <!--<button type="button" @click="activeTab = 'statistics'" class="btn btn-secondary" :class="{'active': activeTab == 'statistics'}">-->
                            <!--Статистика-->
                        <!--</button>-->
                        <button type="button" @click="activeTab = 'edit'" class="btn btn-secondary" :class="{'active': activeTab == 'edit'}">
                            Параметры
                        </button>
                        <button type="button" @click="activeTab = 'tests'" class="btn btn-secondary" :class="{'active': activeTab == 'tests'}">
                            Тесты
                        </button>
                    </div>
                </div>
                <div class="pull-right">
                    <a v-if="activeTab == 'lessons'" href="javascript:void(0)" @click="addLesson()" class="btn btn-primary">добавить занятие</a>
                    <a v-if="activeTab == 'lessons'" href="javascript:void(0)" @click="deleteLesson()" class="btn btn-primary">удалить курс</a>
                    <button v-if="activeTab == 'edit'" class="btn btn-primary" @click="sendForm()" :disabled="loading">
                        <i v-show="loading" class="fa fa-spinner fa-spin"></i> сохранить
                    </button>
                    <label v-if="activeTab == 'tests'">
                        <input type="checkbox" :false-value="0" :true-value="1" v-model="template.has_test">Включить/Отключить тесты
                    </label>
                    <button @click="activeTab = 'test-edit'" v-if="activeTab == 'tests'"  type="button" class="btn btn-primary" :class="{'active': activeTab == 'test-edit'}">
                        Редактировать
                    </button>
                    <a v-if="activeTab == 'tests'" href="javascript:void(0)" @click="addTestTitle()" class="btn btn-primary"> Добавить Вопрос </a>
                </div>
            </div>
            <lessons v-if="activeTab == 'lessons'" :template="template" @formSending="getItem()"></lessons>
            <tests v-if="activeTab == 'tests'" :template="template" @formSending="getItem()" @save="sendForm()"></tests>
            <test-edit v-if="activeTab == 'test-edit'" :template="template" @save="saveTest()">Bauyrzhan</test-edit>
            <div v-if="activeTab == 'edit'" class="card w-75 mx-auto mb-5">
                <div class="card-block">
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Название программы</label>
                        <input
                                type="text"
                                v-model="template.name"
                                class="form-control form-control-lg"
                                placeholder="Укажите название программы"
                        >
                        <error-alert v-if="errors && errors.name">
                            {{ errors.name[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Язык</label>
                        <select class="form-control" @change="onChangeLang()" v-model="template.language">
                            <option value="">выберите</option>
                            <option v-for="language in languages" :value="language.id">{{ language.language }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Связанный курс</label>
                        <select class="form-control" @change="onChange()" v-model="template.connected_template_id">
                            <option value="">выберите</option>
                            <option v-for="language in connectedCourses" :value="language.id">{{ language.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Обложка</label>
                        <div class="fullWidthButton">
                            <lesson-template-image @uploaded="template.image = $refs.image.form.image" ref="image" :form="template" :accept="'image/jpeg,image/png,image/gif'"></lesson-template-image>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:500" class="d-block">Роль</label>
                        <select class="form-control" v-model="template.role_id">
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
                                    {{ templateType(template.type) }}
                                </span>
                                <span class="fa fa-angle-down pull-right"></span>
                            </div>
                            <b-dropdown-item class="py-2" @click="template.type = 0">
                                <div class="h6 mb-0">с преподавателем</div>
                                <div class="small">занятия в офисе или по скайпу с участием преподавателя</div>
                            </b-dropdown-item>
                            <b-dropdown-item class="py-2" @click="template.type = 1">
                                <div class="h6 mb-0">онлайн</div>
                                <div class="small">самостоятельное изучение без участие преподавателя</div>
                            </b-dropdown-item>
                        </b-dropdown>
                        <error-alert v-if="errors && errors.type">
                            {{ errors.type[0] }}
                        </error-alert>
                    </div>
                    <div class="form-group" v-if="template.type == 1">
                        <label style="font-weight:500" class="d-block">Стоимость программы</label>
                        <input v-model="template.cost" type="text" class="form-control">
                        <error-alert v-if="errors && errors.cost">
                            {{ errors.cost[0] }}
                        </error-alert>
                    </div>
                </div>
            </div>
            <b-modal ref="modalDelete" title="Подтвердите удаление">
                Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
                <div slot="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отменить</button>
                    <button type="button" class="btn btn-danger" @click="remove()">Удалить</button>
                </div>
            </b-modal>
            <form-item ref="addItem" :template_id="template.id" v-on:formSending="getItem"></form-item>
            <form-test ref="addTest" :template_id="template.id" v-on:formSending="getItem"></form-test>
        </div>
    </div>
</template>
<script>
    import { get, del, post } from '../../helpers/api'

    export default {
        props: ['id'],

        data() {

            return {
                template: {},
                selectedId: '',
                form: '',
                loading: true,
                activeTab: 'lessons',
                errors: '',
                filterData: {
                    office_ids: [],
                    dateRange: '',
                },
                languages: [
                    {id:1, language: 'Казахский'},
                    {id:2, language: 'Русский'}
                ],
                connectedCourses: {},
                dropdown: {
                    offices: false
                },
                temp: {
                    offices: [],
                },
                statistics: ''
            }

        },
        components: {
            'form-item': require('./FormItem.vue'),
            'form-test': require('./FormTest.vue'),
            "modal-form": require('./Form.vue'),
            'lessons': require('./Item/Lessons.vue'),
            'tests': require('./Item/Tests.vue'),
            'test-edit': require('./Item/TestsEdit.vue'),
            LessonTemplateImage: require('../../components/LessonTemplateImage.vue'),
            ErrorAlert: require('../../components/ErrorAlert.vue'),
            'statistics': require('./Item/Statistics.vue'),
            'date-range-picker': require('./../../components/DateRangePicker.vue'),
            'unmarked-tasks': require('./Unmarked.vue')
        },
        watch: {
            'template.cost'(value){
                if (!/(^[1-9]{1}[0-9]*$)|(^[0]{1}$)/.test(value)) this.form.cost = '';
            },
        },
        methods: {
            deleteLesson() {
                this.$refs.modalDelete.show();
            },
            remove() {
                let _this = this;
                get(_this, '/api/lesson-template-delete/'+this.template.id, {}, function (response) {
                    _this.$emit('formSending');
                    _this.$refs.modalDelete.hide();
                });
            },
            getItemTranslations(value) {
                console.log(value);
                this.value = value;
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson-templates/getConnectedCourses/' + _this.value, {}, function (response) {
                    _this.loading = false;
                    console.log(response);
                    _this.connectedCourses[_this.connectedCourses.length]=response.data;
                    _this.setItem();
                });
            },
            setItem() {
                if (this.template.translation[0] != null){
                    this.template.connected_template_id = this.template.translation[0].id;
                }
            },
            getItem(){
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson-template-item/' + _this.id, {}, function (response) {
                    _this.loading = false;
                    _this.template = response.data;
                    console.log(response.data);
                    if (response.data.language != null) {
                        console.log(response.data.language);
                        _this.getItemTranslations(response.data.language);
                    }
                });
            },
            addLesson() {
                this.$refs.addItem.showModal();
            },
            addTestTitle() {
                this.$refs.addTest.showModal();
            },
            editTemplate(template) {
                this.form = this.template;
                this.$nextTick(function() {
                    this.$refs.modalForm.showModal();
                });
            },
            onChangeLang() {
                if (this.template.language===1) {
                    this.getItemTranslations(1);
                } else if (this.template.language===2) {
                    this.getItemTranslations(2);
                }
            },
            onChange() {
                console.log(this.template);
            },
            sendForm() {
                this.loading = true;
                let _this = this;
                post(_this, '/api/lesson-template-save', this.template, function (response) {
                    _this.loading = false;
                    _this.errors = '';
                    _this.getItem();
                }, function (error) {
                    _this.loading = false;
                    _this.errors = error.response.data;
                });
            },
            saveTest() {
                this.loading = true;
                let _this = this;
                post(_this, '/api/lesson-template-save-test', this.template.test_questions, function () {
                    _this.loading = false;
                    _this.errors = '';
                    _this.getItem();
                }, function(error) {
                   _this.loading = false;
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
            },
            toggleDropdownOffices() {
                this.dropdown.offices = !this.dropdown.offices;
                this.$nextTick(function() {
                    this.$refs.myDropdown.childNodes[2].lastChild.removeAttribute("style");
                    let width = this.$refs.myDropdown.childNodes[2].firstChild.offsetWidth + 70;
                    if(this.dropdown.offices) {
                        this.$refs.myDropdown.childNodes[2].lastChild.setAttribute("style","z-index:10;width:"+width+"px");
                    } else {
                        this.$refs.myDropdown.childNodes[2].lastChild.setAttribute("style","display:none");
                    }
                });
            },
        },
        created() {
            this.getItem();
        }
    }
</script>