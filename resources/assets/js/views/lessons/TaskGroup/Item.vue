<template>
    <div class="w-75 m-auto">
        <div class="clearfix">
            <div class="lead mb-2" style="font-weight:400">
                Упражнение
            </div>
        </div>
        <div class="clearfix mb-4">
            <div class="pull-left">
                <div class="btn-group">
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" :class="{'active': activeTab == 'results' || activeTab == 'student'}" @click="activeTab = 'results'"><span class="fa fa-signal mr-1"></span>Результаты</a>
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" :class="{'active': activeTab == 'answers'}" @click="activeTab = 'answers'"><span class="fa fa-question mr-1"></span>Вопросы и ответы</a>
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" :class="{'active': activeTab == 'preview'}" @click="activeTab = 'preview'"><span class="fa fa-eye mr-1"></span>Предпросмотр</a>

                </div>
            </div>
        </div>
        <answers v-if="activeTab == 'answers'" :tasks="task_group.tasks"></answers>
        <preview v-if="activeTab == 'preview'" :tasks="task_group.tasks"></preview>
        <results v-if="activeTab == 'results'" :task_group="task_group" :students="students" @student="student"></results>
        <student v-if="activeTab == 'student'" :task_group="task_group" :student_id="selectedId" :name="selectedName"></student>
    </div>
</template>
<script>
    export default {

        props: ['task_group','students'],

        data() {
            return {
                activeTab: 'answers',
                selectedId: '',
                selectedName: '',
            }
        },
        components: {
            'answers': require('../../../components/lms/TaskGroup/Answers.vue'),
            'preview': require('./Preview.vue'),
            'results': require('./Results.vue'),
            'student': require('./Student.vue'),
        },
        methods: {
            student(student) {
                this.selectedId = student.id;
                this.selectedName = student.user.name;
                this.activeTab = 'student';
            }
        }
    }
</script>