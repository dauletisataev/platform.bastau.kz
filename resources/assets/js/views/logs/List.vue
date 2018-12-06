<template>
    <div>
        <log-filter ref="filter" :load="load" v-on:filtered="filtered"></log-filter>
<!-- Результаты -->
        <div class="col-8 offset-4">
            <table class="table">
                <tbody>
                <tr v-for="log in logs">
                    <td><router-link :to="{name:'user', params:{id: log.user.id}}">{{ log.user.first_name }}</router-link></td>

                    <td v-if="log.name === 'teacher_create'">
                        Создание преподавателя
                        <router-link :to="{name:'teacher', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'teacher_edit'">
                        Преподаватель
                        <router-link :to="{name:'teacher', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        изменен
                    </td>

                    <td v-if="log.name === 'teacher_archive'">
                        Преподаватель
                        <router-link :to="{name:'teacher', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        перемещен в архив
                    </td>

                    <td v-if="log.name === 'teacher_inarchive'">
                        Преподаватель
                        <router-link :to="{name:'teacher', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        востановлен из архива
                    </td>

                    <td v-if="log.name === 'teacher_delete'">
                        Преподаватель {{ log.value }} удален
                    </td>

                    <td v-if="log.name === 'student_create'">
                        Создание ученика
                        <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'student_balance_plus'">
                        Пополнение баланса ученика
                        <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        на {{ log.value }}
                    </td>

                    <td v-if="log.name === 'student_balance_minus'">
                        Возврат баланса ученика
                        <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        на {{ -log.value }}
                    </td>

                    <td v-if="log.name === 'student_mark_lesson'">
                        Отмечено посещение ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        на следующих занятиях: {{ log.value }}
                    </td>

                    <td v-if="log.name === 'student_add_to_group'">
                        Добавление ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        в группу <router-link :to="{name:'екgroup', params:{id: log.y_ids}}">{{ log.y_ids }}</router-link>
                    </td>

                    <td v-if="log.name === 'student_freeze_balance'">
                        Баланс ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        заморожен {{ log.value ? 'на '+log.value+ ' занятий'  : '' }}
                    </td>

                    <td v-if="log.name === 'student_unfreeze_balance'">
                        Баланс ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        разморожен
                    </td>

                    <td v-if="log.name === 'student_archive'">
                        Изменен статус ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        на {{ log.y_ids == 'temp' ? 'Отложивший' : log.y_ids == 'completed' ? 'Завершивший' : 'Прервавший' }}
                    </td>

                    <td v-if="log.name === 'student_inarchive'">
                        Изменен статус ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        на Текущий
                    </td>

                    <td v-if="log.name === 'student_edit'">
                        Изменены данные ученика <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'student_group_discount'">
                        Ученику <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        установлена скидка {{ log.value }} для группы <router-link :to="{name:'group', params:{id: log.y_ids}}">{{ log.y_ids }}</router-link>
                    </td>

                    <td v-if="log.name === 'student_group_ind'">
                        Ученику <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        установлена индивидуальная стоимость занятия {{ log.value }} для группы <router-link :to="{name:'group', params:{id: log.y_ids}}">{{ log.y_ids }}</router-link>
                    </td>

                    <td v-if="log.name === 'student_delete'">
                        Ученик {{ log.value }} удален
                    </td>

                    <td v-if="log.name === 'group_create'">
                        Создание группы <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                    </td>

                    <td v-if="log.name === 'group_archive'">
                        Группа <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        перемещена в архив
                    </td>

                    <td v-if="log.name === 'group_inarchive'">
                        Группа <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        востановлен из архива
                    </td>

                    <td v-if="log.name === 'group_change_teacher'">
                        В группу <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        назначен преподаватель <router-link :to="{name:'teacher', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'group_add_students'">
                        В группу <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        добавлены ученики: <span v-for="student in log.y_ids"><router-link :to="{name:'student', params:{id: student.id}}">{{ student.name }}</router-link>; </span>
                    </td>

                    <td v-if="log.name === 'group_delete_student'">
                        Из группы <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        удален ученик <router-link :to="{name:'student', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'group_fake_delete_student'">
                        Для группы <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        архивирован ученик <router-link :to="{name:'student', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'group_fake_attach_student'">
                        В группе <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        восстановлен из архива ученик <router-link :to="{name:'student', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link>
                    </td>

                    <td v-if="log.name === 'group_edit'">
                        Группа <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        изменена
                    </td>

                    <td v-if="log.name === 'group_change_schedule'">
                        В группе <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        изменено расписание
                    </td>

                    <td v-if="log.name === 'group_delete'">
                        Группа {{ log.value }} удалена
                    </td>

                    <td v-if="log.name === 'lesson_create'">
                        В группу <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        добавлено занятие "{{ log.value }}"
                    </td>

                    <td v-if="log.name === 'lesson_edit'">
                        В группе <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        изменено занятие "{{ log.value }}"
                    </td>

                    <td v-if="log.name === 'lesson_delete'">
                        В группу <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        удалено занятие "{{ log.value }}"
                    </td>

                    <td v-if="log.name === 'lesson_mark'">
                        В группе <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        отмечены ученики: <span v-for="student in log.y_ids"><router-link :to="{name:'student', params:{id: student.id}}">{{ student.name }}</router-link>; </span> занятие "{{ log.value }}".
                    </td>

                    <td v-if="log.name === 'event_transfer'">
                        В группе <router-link :to="{name:'group', params:{id: log.x_ids}}">{{ log.x_ids }}</router-link>
                        перенесено занятие
                    </td>

                    <td v-if="log.name === 'transaction'" >
                        <span v-if="log.x_ids.id">Проведена операция №{{ log.x_ids.id }} с меткой "{{ log.x_ids.tag.name }}" на {{ log.x_ids.value }}</span>
                        <span v-else="">операция удалена</span>
                    </td>
                    <td v-if="log.name === 'transaction_edit_value'">
                        <span v-if="log.x_ids.id">Изменено значении операции №{{ log.x_ids.id }} с меткой "{{ log.x_ids.tag.name }}" с {{ log.y_ids }}  на {{ log.value }}</span>
                        <span v-else="">операция удалена</span>
                    </td>

                    <td v-if="log.name === 'transaction_edit_description'">
                        <span v-if="log.x_ids.id">Изменено описание операции №{{ log.x_ids.id }} с меткой "{{ log.x_ids.tag.name }}" с {{ log.y_ids }}  на {{ log.value }}</span>
                        <span v-else="">операция удалена</span>
                    </td>

                    <td v-if="log.name === 'transaction_edit_tag'">
                        <span v-if="log.x_ids.id">Изменена метка операции №{{ log.x_ids.id }} с {{ log.y_ids }}  на {{ log.value }}</span>
                        <span v-else="">операция удалена</span>
                    </td>

                    <td v-if="log.name === 'program_create'">
                        <span>Создана новая програма {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'program_edit'">
                        <span>Програма {{ log.value }} изменена</span>
                    </td>
                    <td v-if="log.name === 'program_delete'">
                        <span>Програма {{ log.value }} удалена</span>
                    </td>

                    <td v-if="log.name === 'tag_create'">
                        <span>Создана новая метка {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'tag_edit'">
                        <span>Метка {{ log.value }} изменена</span>
                    </td>
                    <td v-if="log.name === 'tag_delete'">
                        <span>Метка {{ log.value }} удалена</span>
                    </td>

                    <td v-if="log.name === 'office_create'">
                        <span>Создано новое отделение {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'office_edit'">
                        <span>Отделение {{ log.value }} изменено</span>
                    </td>
                    <td v-if="log.name === 'office_delete'">
                        <span>Отделение {{ log.value }} удалено</span>
                    </td>

                    <td v-if="log.name === 'cabinet_create'">
                        <span>Добавлен кабинет {{ log.value }} в отделение {{ log.x_ids }}</span>
                    </td>

                    <td v-if="log.name === 'cabinet_delete'">
                        <span>Кабинет {{ log.value }} удален из отделения {{ log.x_ids }}</span>
                    </td>

                    <td v-if="log.name === 'transaction_delete'">
                        <span>Операция №{{ log.x_ids }} удалена </span>
                    </td>

                    <td v-if="log.name === 'user_create'">
                        <span v-if="log.x_ids">Создан пользователь <router-link :to="{name:'user', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link> </span>
                    </td>

                    <td v-if="log.name === 'user_delete'">
                        <span>Пользователь {{ log.value }} удален</span>
                    </td>

                    <td v-if="log.name === 'rate_create'">
                        <span>Для студента <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link> создан тариф {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'rate_update'">
                        <span>Для студента <router-link :to="{name:'student', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link> изменен тариф {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'pipeline_create'">
                        <span>Создана воронка {{ log.x_ids }}</span>
                    </td>

                    <td v-if="log.name === 'pipeline_edit'">
                        <span>Изменена воронка {{ log.x_ids }}</span>
                    </td>

                    <td v-if="log.name === 'pipeline_delete'">
                        Воронка {{ log.value }} удалена
                    </td>

                    <td v-if="log.name === 'status_create'">
                        <span>Создан этап {{ log.x_ids.status }} в воронке {{ log.x_ids.pipeline }} </span>
                    </td>

                    <td v-if="log.name === 'status_edit'">
                        <span>Изменен этап {{ log.x_ids.status }} в воронке {{ log.x_ids.pipeline }} </span>
                    </td>

                    <td v-if="log.name === 'status_delete'">
                        Этап {{ log.value }} удален
                    </td>

                    <td v-if="log.name === 'lead_create'">
                        <span>Создана заявка <router-link :to="{name:'lead', params:{id: log.x_ids.id}}">{{ log.x_ids.phone }}</router-link></span>
                    </td>

                    <td v-if="log.name === 'lead_delete'">
                        <span>Удалена заявка {{ log.value }}</span>
                    </td>

                    <td v-if="log.name === 'lead_task_create'">
                        <span>
                            {{ log.x_ids.type == 'custom' ? "Создана задача" : log.x_ids.type == 'trial' ? "Назначен пробный урок" : "Назначена консультация" }} №{{ log.x_ids.id }} в заявке <router-link :to="{name:'lead', params:{id: log.x_ids.lead_id}}">№{{ log.x_ids.lead_id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'lead_task_archive'">
                        <span>
                            {{ log.x_ids.type == 'custom' ? "Отменена задача" : log.x_ids.type == 'trial' ? "Отменен пробный урок" : "Отменена консультация" }} №{{ log.x_ids.id }} в заявке <router-link :to="{name:'lead', params:{id: log.x_ids.lead_id}}">№{{ log.x_ids.lead_id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'lead_task_move'">
                        <span>
                            {{ log.x_ids.type == 'custom' ? "Перенесена задача" : log.x_ids.type == 'trial' ? "Перенесен пробный урок" : "Перенесена консультация" }} №{{ log.x_ids.id }} в заявке <router-link :to="{name:'lead', params:{id: log.x_ids.lead_id}}">№{{ log.x_ids.lead_id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'lead_task_complete'">
                        <span>
                            {{ log.x_ids.type == 'custom' ? "Выполнена задача" : log.x_ids.type == 'trial' ? "Посещен пробный урок" : "Посещена консультация" }} №{{ log.x_ids.id }} в заявке <router-link :to="{name:'lead', params:{id: log.x_ids.lead_id}}">№{{ log.x_ids.lead_id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'lead_change_status'">
                        Заявка <router-link :to="{name:'lead', params:{id: log.x_ids}}">№{{ log.x_ids }}</router-link> перенесена в этап {{ log.y_ids.name }}
                    </td>
                    <td v-if="log.name === 'leads_office_change'">
                        Для {{ log.value }} заявок изменено отделение на {{ log.x_ids }}
                    </td>
                    <td v-if="log.name === 'leads_status_change'">
                        Для {{ log.value }} заявок изменен этап на {{ log.x_ids }}
                    </td>
                    <td v-if="log.name === 'auto_task_create'">
                        В этап {{ log.x_ids }} добавлена автозадача
                    </td>
                    <td v-if="log.name === 'auto_task_change'">
                        В этапе {{ log.x_ids }} изменена автозадача
                    </td>
                    <td v-if="log.name === 'auto_task_delete'">
                        В этапе {{ log.x_ids }} удалена автозадача
                    </td>

                    <td v-if="log.name === 'student_task_create'">
                        <span>
                            Создана задача №{{ log.x_ids.id }} для студента <router-link v-if="log.x_ids.student_id" :to="{name:'student', params:{id: log.x_ids.student_id}}">{{ log.x_ids.name }}</router-link><span v-else>{{log.x_ids.name}}</span>
                        </span>
                    </td>
                    <td v-if="log.name === 'student_task_archive'">
                        <span>
                            Отменена задача №{{ log.x_ids.id }} для студента <router-link v-if="log.x_ids.student_id" :to="{name:'student', params:{id: log.x_ids.student_id}}">{{ log.x_ids.name }}</router-link><span v-else>{{log.x_ids.name}}</span>
                        </span>
                    </td>
                    <td v-if="log.name === 'student_task_move'">
                        <span>
                            Перенесена задача №{{ log.x_ids.id }} для студента <router-link v-if="log.x_ids.student_id" :to="{name:'student', params:{id: log.x_ids.student_id}}">{{ log.x_ids.name }}</router-link><span v-else>{{log.x_ids.name}}</span>
                        </span>
                    </td>
                    <td v-if="log.name === 'student_task_complete'">
                        <span>
                            Выполнена задача №{{ log.x_ids.id }} для студента <router-link v-if="log.x_ids.student_id" :to="{name:'student', params:{id: log.x_ids.student_id}}">{{ log.x_ids.name }}</router-link><span v-else>{{log.x_ids.name}}</span>
                        </span>
                    </td>

                    <td v-if="log.name === 'vacancy_create'">
                        <span>
                            Создана вакансия <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_edit'">
                        <span>
                            Изменена вакансия <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_delete'">
                        <span>Удалена вакансия {{ log.value }}</span>
                    </td>
                    <td v-if="log.name === 'vacancy_succeed'">
                        <span>
                            Закрыта вакансия <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_close'">
                        <span>
                            Отменена вакансия <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_add_candidate'">
                        <span>
                            Добавлен кандидат <router-link :to="{name:'candidate', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link> в вакансию <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_accept_candidate'">
                        <span>
                            Принят кандидат <router-link :to="{name:'candidate', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link> на вакансию <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'vacancy_remove_candidate'">
                        <span>
                            Удален кандидат <router-link :to="{name:'candidate', params:{id: log.y_ids.id}}">{{ log.y_ids.name }}</router-link> из вакансии <router-link :to="{name:'vacancy', params:{id: log.x_ids.id}}">{{ log.x_ids.id }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'candidate_create'">
                        <span>
                            Создан кандидат <router-link :to="{name:'candidate', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'candidate_edit'">
                        <span>
                            Изменен кандидат <router-link :to="{name:'candidate', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link>
                        </span>
                    </td>
                    <td v-if="log.name === 'candidate_delete'">
                        <span>Удален кандидат {{ log.value }}</span>
                    </td>
                    <td v-if="log.name === 'candidate_status_change'">
                        <span>
                            Изменен статус кандидата <router-link :to="{name:'candidate', params:{id: log.x_ids.id}}">{{ log.x_ids.name }}</router-link> на "{{ $common.candidateStatusName(log.value) }}"
                        </span>
                    </td>

                    <td>{{ log.date }}</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>


<script>

    import { get } from '../../helpers/api'

    export default{

        data() {
            return {
                load: false,
                scrollLoad: false,
                resource_url: '/api/user-logs',
                next_url: '',
                default_url: '/api/user-logs',
                logs: []
            }
        },
        components: {
            'log-filter': require('./Filter.vue'),
        },
        methods: {
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;
                get(_this, this.resource_url, {params: this.filterData}, function (response) {

                    let json = response.data;
                    _this.next_url = json.next_page_url;
                    _this.logs = _this.logs.concat(json.data);
                    _this.scrollLoad = false;
                    _this.load = false;

                }, function () {

                    this.scrollLoad = false;
                    this.load = false;

                });

            },

            filtered() {
                this.resource_url = this.default_url;
                this.logs = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;
                this.$nextTick(function () {
                    this.$router.push({ path: '/control/logs', query: this.filterData });
                    this.getList();
                });

            },

            handleScroll(e){
                let body = document.body,
                    html = document.documentElement;

                let height = Math.max( body.scrollHeight, html.scrollHeight);

                if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !this.scrollLoad) {
                    this.scrollLoad = true;
                    this.$nextTick(function () {
                        this.getList();
                    })
                }
            }

        },

        created() {
            window.document.body.onscroll = this.handleScroll;
        }
    }
</script>