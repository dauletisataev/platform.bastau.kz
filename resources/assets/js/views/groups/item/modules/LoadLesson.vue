<template>

    <b-modal v-if="group" ref="modal" size="lg" title="Загрузить занятия из шаблона">
        <div class="form-group">
            <label style="font-weight:500" class="d-block">Шаблон</label>
            <v-select :on-change="selectTemplate" label="name" value="id" :value.sync="selectedTemplate" :options="group.lesson_templates" placeholder="Выберите шаблон"></v-select>
            <div class="form-text text-muted small">В списке доступны все добавленные шаблоны с указанным уровнем и программой обучения соответствующие этим же параметрам, указанным в группе.</div>
        </div>
        <div class="form-group mt-2">
            <label style="font-weight:500" class="d-block">Проставить даты и время занятий</label>
            <div class="input-group">
                <datepicker v-model="firstLessonDate" active="1" class="form-control date" placeholder="дата первого занятия"></datepicker>
                <div class="input-group-btn">
                    <button class="btn btn-primary" @click="setDate" :disabled="(formSending ? true : false) || lessons.length === 0">проставить</button>
                </div>
            </div>
            <div class="form-text text-muted small">Укажите дату первого планируемого занятия в группе и нажмите "проставить". После этого для всех выбранных занятий (в таблице ниже) автоматически проставятся даты и время на основе расписания группы (дней недели и времени занятий) указанного менеджером в настройках.</div>
        </div>
        <div class="form-group mb-2">
            <label style="font-weight:500" class="d-block mb-0">Выберите занятия</label>
            <div class="form-text text-muted small mt-0 mb-2">Выбрано {{ countActive() }} занятий</div>
            <div class="card" style="max-height: 300px;overflow-y: scroll">
                <table class="table mb-0 table-sm">
                    <thead class="thead-default h6 mb-0">
                    <tr>
                        <th><input type="checkbox" v-model="toggleState" name="templatelessonscheck" @click="toggleAll()"></th>
                        <th>Тема</th>
                        <th>Дата</th>
                        <th>Время</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="lesson in lessons">
                        <td>
                            <input type="checkbox" v-model="lesson.active">
                        </td>
                        <td>
                                <span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;display:block;max-width:500px">
                                    <b-tooltip :content="lesson.title">
                                        {{ lesson.title }}
                                    </b-tooltip>
                                </span>
                        </td>
                        <td>
                            <datepicker v-model="lesson.date" active="1" class="form-control date" placeholder="дата"></datepicker>
                        </td>
                        <td>
                            <timepicker v-model="lesson.time" active="1" class="form-control time" placeholder="время"></timepicker>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div slot="modal-footer">
            <button
                    type="button"
                    class="btn btn-primary"
                    @click="sendForm"
                    :disabled="(formSending ? true : false) || lessons.length === 0"
            >
                <i v-show="formSending" class="fa fa-spinner fa-spin"></i>
                Добавить занятия в группу
            </button>
        </div>

    </b-modal>


</template>



<script>

    import { post } from '../../../../helpers/api';

    export default {

        props: ['group'],

        data() {
            return {
                errors: [],
                formSending: false,
                lessons: [],
                firstLessonDate: '',
                selectedTemplate: '',
                toggleState: true,
            }
        },
        watch: {
            group(group) {
                this.lessons = [];
                this.selectedTemplate = '';
                this.toggleState = true;
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

                this.beforeSave();

                let _this = this;

                post(_this, '/api/lesson-save', {lessons: this.lessons}, function (response) {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('formSending');
                }, function (errro) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            },

            beforeSave() {

                let component = this;

                this.lessons.forEach(function (lesson) {

                    lesson.group_id = component.group.id;
                    lesson.office_cabinet_id = component.group.default_office_cabinet_id;

                })


            },

            setDate() {

                let calendar  = this.group.calendar;
                let component = this,
                    dayId = 0;

                calendar.forEach(function (day, index) {
                    if (day.date == component.firstLessonDate) dayId = index;
                });

                this.lessons.forEach(function (lesson) {

                    if (!lesson.active) return false;


                    lesson.date = calendar[dayId].date;
                    lesson.time = calendar[dayId].time;


                    dayId++;

                });

            },

            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            selectTemplate(value) {
                this.selectedTemplate = value;
                if(value && value.items) {
                    this.lessons = value.items;
                } else {
                    this.lessons = [];
                }
            },
            countActive() {
                let count = 0;
                if(this.lessons && this.lessons.length>0) {
                    this.lessons.forEach(function(lesson) {
                        if(lesson.active) count++;
                    });
                }
                return count;
            },
            toggleAll() {
                if(this.toggleState) {
                    if(this.lessons && this.lessons.length>0) {
                        this.lessons.forEach(function(lesson) {
                            lesson.active = true;
                        });
                    }
                } else {
                    if(this.lessons && this.lessons.length>0) {
                        this.lessons.forEach(function(lesson) {
                            lesson.active = false;
                        });
                    }
                }
            }

        }



    }
</script>