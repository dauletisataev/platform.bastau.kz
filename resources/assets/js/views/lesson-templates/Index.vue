<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner" style="z-index:3005; left: 15px">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2 pr-5">
            <program-filter v-if="$common.data.roles" ref="filter" @filtered="filtered()"></program-filter>
            <div class="row">
                <div class="col-4" v-for="template in templates">
                    <div class="card mt-4">
                        <router-link v-if="$route.name == 'student_lesson_templates'" :to="{ name: 'student_lesson_template', params:{id: template.id} }" class="card-full-link"></router-link>
                        <router-link v-else :to="{ name: 'lesson_template', params:{id: template.id} }" class="card-full-link"></router-link>
                        <img v-if="template.image" class="card-img-top" :src="template.image">
                        <img class="card-img-top" src="http://placehold.it/283x190" v-else>
                        <div class="card-block pt-2" >
                            <div class="mb-2" v-if="template.role_id == 4">
                                <div class="badge badge-primary">{{ template.program.name }}</div>
                                <div class="badge badge-default">{{ template.level.name }}</div>
                            </div>
                            <div class="h5 mb-0">{{ template.name }}</div>
                            <span v-if="$route.name == 'student_lesson_templates'">
                                <span class="small text-muted" style="letter-spacing:2px">
                                    {{ template.role.instrumental }} • {{ template.type ? 'онлайн' : 'с преподавателем'}}
                                </span>
                                <span v-if="template.type" class="small text-muted" style="font-weight:500;">• {{ $common.formatPrice(template.cost) }} ₸</span>
                            </span>
                        </div>
                        <ul class="list-group list-group-flush small">
                            <li class="d-block list-group-item">
                                <span class="fa fa-fw mr-2 fa-align-left" style="color:#BCBCBC"></span>
                                {{ template.items.length }}
                            </li>
                            <!--<li class="d-block list-group-item">-->
                            <!--<span class="fa fa-fw mr-2 fa-question-circle" style="color:#BCBCBC"></span>-->
                            <!--3 тестирования-->
                            <!--</li>-->
                            <!--<li class="d-block list-group-item">-->
                            <!--<span class="fa fa-fw mr-2 fa-pencil" style="color:#BCBCBC"></span>-->
                            <!--36 заданий-->
                            <!--</li>-->
                            <!--<li class="d-block list-group-item">-->
                            <!--<span class="fa fa-fw mr-2 fa-users" style="color:#BCBCBC"></span>-->
                            <!--1246 человек завершили-->
                            <!--</li>-->
                            <li class="d-block list-group-item list-group-item-info clearfix small" v-if="$route.name == 'student_lesson_templates' && !isBought(template)">
                                <div class="pull-left">
                                    <div class="h5 mb-0">{{ $common.formatPrice(template.cost) }} ₸</div>
                                </div>
                                <a href="javascript:void(0)" @click="pay(template)" class="btn btn-primary btn-md pull-right" style="font-weight:500;position:relative;z-index: 99;">
                                    Купить
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <modal-form ref="modalForm" :form="form" v-on:formSending="getList"></modal-form>

            <b-modal ref="modalDelete" title="Подтвердите удаление">
                Вы действительно хотите удалить шаблон? Данное действие нельзя отменить.
                <div slot="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отменить</button>
                    <button type="button" class="btn btn-danger" @click="remove()">Удалить</button>
                </div>
            </b-modal>

        </div>
    </div>

</template>


<script>

    import { get, del } from '../../helpers/api'

    export default {

        data() {

            return {
                form: [],
                templates: [],
                template: '',
                loading: true,
                filterData: '',
            }
        },

        methods: {

            addTemplate() {
                this.form = {
                    program_id: '',
                    level_id: '',
                    program: '',
                    level: '',
                    items: []
                };
                this.$refs.modalForm.showModal();
            },
            deleteTemplate(template) {
                this.$refs.modalDelete.show();
                this.template = template;
            },
            editTemplate(template) {

                this.form = template;

                this.$refs.modalForm.showModal();

            },
            remove() {

                let _this = this;

                del(_this, '/api/lesson-template-delete/'+this.template.id, {}, function (response) {
                    _this.getList();

                    _this.$refs.modalDelete.hide();
                });

            },
            getList(){
                let _this = this;
                this.loading = true;
                let url = '';
                if(this.$route.name === 'student_lesson_templates') {
                    url = '/api/online-programs';
                } else {
                    url = '/api/lesson-templates';
                }
                get(_this, url, {params: this.filterData}, function (response) {
                    _this.templates = response.data;
                    _this.loading = false;
                });
            },

            showTemplate(id) {
                this.$router.push({ name: 'lesson_template', params:{id: id} });
            },

            filtered() {

                this.filterData = this.$refs.filter.filterData;
                this.$nextTick(function () {
                    this.$router.push({ path: this.$route.path, query: this.filterData });
                    this.getList();
                });

            },
            isBought(template) {
                let result = false;
                let _this = this;
                if(template) {
                    if(!template.cost) {
                        result = true
                    } else {
                        template.users.forEach(function(user) {
                            if(user.id === _this.$root.user.data.id)
                                result = true;
                        });
                    }
                }
                return result;
            },
            pay(template) {
                this.$router.push({name:'lesson_template_payment', params: { id: template.id, cost: template.cost, name: template.name }})
            }

        },

        components: {
            "modal-form": require('./Form.vue'),
            "program-filter": require('./Filter.vue')
        },

        created() {

        }
    }
</script>