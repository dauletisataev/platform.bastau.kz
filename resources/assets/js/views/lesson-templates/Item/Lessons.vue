<template>
    <div>
        <div class="card mb-4">
            <div class="card-header">1
                <span class="lead">Занятия</span>
                <span class="badge badge-pill badge-default ml-1">{{ template.items.length }}</span>
            </div>
            <div style="max-height: 400px; overflow-y:scroll">
                <table class="table">
                    <draggable @change="onChange" v-model="template.items" :options="{draggable:'.item', handle: '.btn-drag'}" :element="'tbody'">
                        <tr v-for="item in template.items" class="item">
                            <td>{{ item.title }}</td>
                            <td>
                              <!-- <b-tooltip
                                        content="есть материал"
                                        v-if="item.pages.length"
                                        class="badge badge-default d-inline-block"

                                    <router-link
                                            :to="{
                                                    name: 'lesson_template_content',
                                                    params:{id: item.id, template_id: template.id}
                                            }"
                                            class="fa fa-align-justify"
                                            style="color:white;text-decoration: none"
                                    ></router-link>
                                </b-tooltip>
                                <b-tooltip
                                        content="есть задание"
                                        v-if="item.homework.length"
                                        class="badge badge-default d-inline-block"
                                >
                                    <router-link
                                            :to="{
                                                name: 'lesson_template_content',
                                                params:{id: item.id, template_id: template.id, setTab: 'homework'}}"
                                            class="fa fa-pencil" style="color:white;text-decoration: none"
                                    ></router-link>
                                </b-tooltip>
                                <b-tooltip
                                        content="есть тестирование"
                                        v-if="item.tests.length"
                                        class="badge badge-default d-inline-block"
                                >
                                    <router-link
                                            :to="{
                                                name: 'lesson_template_content',
                                                params:{id: item.id, template_id: template.id, setTab: 'tests'}}"
                                            class="fa fa-question-circle" style="color:white;text-decoration: none"
                                    ></router-link>
                                </b-tooltip>-->
                            </td>
                            <td>
                                <b-popover
                                        title="Условия доступа к занятию"
                                        triggers="hover"
                                        :content="accessInfo(item)"
                                        class="badge"
                                        :class="{
                                            'badge-default': !item.access_free,
                                            'badge-success': item.access_free}
                                        "
                                        style="{'cursor:pointer'}"
                                >
                                    <span class="fa fa-lock" @click="editAccess(item)"></span>
                                </b-popover>
                            </td>
                            <td>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-link ml-1 btn-sm btn-drag" style="color:#D7D7D7;cursor: move;cursor: grab;cursor: -moz-grab;cursor: -webkit-grab;">
                                        <span class="fa fa-arrows"></span>
                                    </button>
                                    <a
                                            href="javascript:void(0)"
                                            @click="showContent(item.id)"
                                            class="btn btn-sm btn-secondary"
                                    >
                                        <span class="fa fa-fw fa-eye"></span>
                                    </a>
                                    <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger" type="button">
                                        <span class="fa fa-fw fa-trash"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </draggable>
                </table>
            </div>
        </div>
        <!--<div class="card mb-4">-->
            <!--<div class="card-header">-->
                <!--<span class="lead">Тестирования</span>-->
                <!--<span class="badge badge-pill badge-default ml-1">{{ tests.length }}</span>-->
            <!--</div>-->
            <!--<div style="max-height: 400px;overflow-y: scroll">-->
                <!--<table class="table mb-0">-->
                    <!--<tbody>-->
                        <!--<tr v-for="test in tests" class="item">-->
                            <!--<td>-->
                                <!--<span class="text-muted">-->
                                    <!--{{ test.lesson_template_item_title }}-->
                                    <!--<span class="fa fa-angle-right mx-1"></span>-->
                                <!--</span>-->
                                <!--<span style="font-weight:500">{{ test.name }}</span>-->
                            <!--</td>-->
                        <!--</tr>-->
                    <!--</tbody>-->
                <!--</table>-->
            <!--</div>-->
        <!--</div>-->
        <b-modal ref="modalDelete" title="Подтвердите удаление">
            Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
            <div slot="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отменить</button>
                <button type="button" class="btn btn-danger" @click="remove()">Удалить</button>
            </div>
        </b-modal>
        <modal-access v-if="selectedItem" :_form="selectedItem" @formSending="$emit('formSending')" ref="modalAccess"></modal-access>
    </div>
</template>
<script>

    import { get, del, post } from '../../../helpers/api'

    export default {
        props: ['template'],
        data() {
            return {
                selectedItem: ''

            }
        },
        components: {
            'modal-access': require('./ModalAccess.vue')
        },
        computed: {
            tests() {
                let arr = [];
                let temp = '';
                this.template.items.forEach(function(item) {
                    item.tests.forEach(function(test) {
                        temp = test;
                        test.lesson_template_item_title = item.title;
                        arr.push(temp);
                    })
                });
                return arr;
            }
        },
        methods: {
            deleteItem(id) {
                this.selectedId = id;
                this.$refs.modalDelete.show();
            },
            remove() {
                let _this = this;
                del(_this, '/api/lesson-template-item-delete/'+this.selectedId, {}, function (response) {
                    _this.$emit('formSending');
                    _this.$refs.modalDelete.hide();
                });

            },
            showContent(id) {
                this.$router.push({ name: 'lesson_template_content', params:{id: id, template_id: this.template.id} });
            },
            onChange(e) {
                this.moved(e.moved);
            },
            moved(current) {

                let calendar  = this.group.calendar,
                    lessons = this.group.lessons;

                if (current.newIndex == 0){
                    current.element.dayId = lessons[1].dayId;
                }else{
                    current.element.dayId = lessons[current.newIndex-1].dayId;
                }


                for (let index = current.newIndex; index <= lessons.length - 1; index++) {

                    let lesson = lessons[index],
                        prevLesson = index > 0 ? lessons[index - 1] : null;

                    if (prevLesson && !lesson.without_date){
                        lesson.dayId = prevLesson.dayId + 1;
                    }

                }
                this.updateLessonsDate();

            },
            onChange(obj) {
                let _this = this;
                if(obj && obj.moved) {
                    post(_this,'/api/lesson-template-item-move', obj.moved, function(response) {
                        _this.$emit('formSending');
                    });
                }
            },
            editAccess(obj) {
                let _this = this;
                this.selectedItem = obj;
                this.$nextTick(function() {
                    _this.$refs.modalAccess.showModal();
                });
            },
            accessInfo(obj) {
                let html = '';
                let divider = "<hr class='my-1'>";
                let buf = false;
                if(obj.access_free) {
                    html += "Доступно до оплаты";
                    buf = true;
                }
                if(obj.access_tasks) {
                    if(buf) {
                        html += divider;
                    } else {
                        buf = true;
                    }
                    html += "Завершение всех упражнений в материале предыдущего занятия";
                }
                if(obj.access_homework) {
                    if(buf) {
                        html += divider;
                    } else {
                        buf = true;
                    }
                    html += "Выполнение задания из предыдущего занятия";
                }
                if(obj.access_tests) {
                    if(buf) {
                        html += divider;
                    } else {
                        buf = true;
                    }
                    html += "Выполнение тестов из предыдущего занятия";
                }
                if(obj.access_offset) {
                    if(buf) {
                        html += divider;
                    } else {
                        buf = true;
                    }
                    html += "Через " + obj.offset_value;
                    switch(obj.offset_type) {
                        case "minutes": html += " минут";
                            break;
                        case "hours": html += " часов";
                            break;
                        case "days": html += " дней";
                            break;
                    }
                }
                if(!html) {
                    html += 'Доступно сразу после оплаты';
                }
                return html;
            }
        }
    }
</script>