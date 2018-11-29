<template>
    <div class="row">
        <div class="col-4">
            <div class="card">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td style="font-weight:500">
                                Статус занятия
                            </td>
                            <td class="text-success">
                               {{ lesson.passed==1 ? 'Завершено' : lesson.is_started==1 ? 'Проводится' : 'Запланировано' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:500">
                                Дата и время
                            </td>
                            <td>
                                {{ lesson.date }} {{ lesson.time }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:500">
                                Продолжительность
                            </td>
                            <td>
                                {{ lesson.duration ? lesson.duration : "Не установлена" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:500">
                                Преподаватель
                            </td>
                            <td v-if="lesson.group.teacher_id">
                                <router-link :to="{ name: 'teacher', params: { id: lesson.group.teacher_id }}">{{getTeacherName(lesson.group.teacher_id)}}</router-link>
                            </td>
                            <td class="text-danger" v-else>
                                Не установлен
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:500">
                                Группа
                            </td>
                            <td>
                                <router-link :to="{ name: 'group', params: { id: lesson.group.id }}">{{lesson.group.id}}</router-link> <span class="text-muted">({{getOfficeName(lesson.group.office_id)}})</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <table class="table mb-0 text-center">
                    <thead class="thead-default h6">
                        <tr>
                            <th class="text-center">
                                ученик
                            </th>
                            <th class="text-center">
                                посещение
                            </th>
                            <th class="text-center">
                                тесты
                            </th>
                            <th class="text-center">
                                задание
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="student in lesson.group.students" v-if="student.in_archive!=1 && student.pivot.deleted!=1">
                            <td style="font-weight:500">
                                <router-link :to="{ name: 'student', params: { id: student.id }}" style="text-decoration: none; color: #292b2c; ">
                                    {{ student.user.name }}
                                </router-link>
                            </td>
                            <td>
                                <span v-if="lesson.passed==1 && visit(student.id)" class="fa fa-check text-success"></span>
                                <span v-else-if="lesson.passed==1 && !visit(student.id)" class="fa fa-times text-danger"></span>
                                <span v-else class="fa fa-minus" style="color:#DFDFDF"></span>
                            </td>
                            <td>
                                <span v-if="completedTest(student.id)" class="fa fa-check text-success"></span>
                                <span v-else-if="lesson.passed==1 && !completedTest(student.id)" class="fa fa-times text-danger"></span>
                                <span v-else class="fa fa-minus" style="color:#DFDFDF"></span>
                            </td>
                            <td>
                                <span v-if="completedHomework(student.id)" class="fa fa-check text-success"></span>
                                <span v-else-if="lesson.passed==1 && !completedHomework(student.id)" class="fa fa-times text-danger"></span>
                                <span v-else class="fa fa-minus" style="color:#DFDFDF"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: ['lesson'],

        data() {
            return {
            }
        },
        methods: {
            getTeacherName(id) {
                let name = '';
                if(this.$common.data && this.$common.data.teachers && this.$common.data.teachers.length>0) {
                    this.$common.data.teachers.forEach(function(teacher) {
                        if(id==teacher.id) name = teacher.name;
                    });
                }
                return name;
            },
            getOfficeName(id) {
                let name = '';
                if(this.$common.data && this.$common.data.offices && this.$common.data.offices.length>0) {
                    this.$common.data.offices.forEach(function(office) {
                        if(id==office.id) name = office.name;
                    });
                }
                return name;
            },
            completedTest(student_id) {
                let _this = this;
                var tests = this.lesson.tests;
                var found = false;
                if(tests && tests.length>0) {
                    tests.forEach(function(test) {
                        if (test.students && test.students.length > 0) {
                            test.students.forEach(function (student) {
                                if (!found) {
                                    if (student.id == student_id) found = true;
                                }
                            });
                        }
                    })
                }
                return found;
            },
            completedHomework(student_id) {
                var was = false;
                if(this.lesson.homework && this.lesson.homework.length>0) {
                    if(this.lesson.homework[0].students && this.lesson.homework[0].students.length>0) {
                        this.lesson.homework[0].students.forEach(function(student) {
                            if(student.id == student_id) {
                                if(!was) was = true;
                            }
                        });
                    }
                }
                return was;
            },
            visit(student_id) {
                let value = false;
                if(this.lesson.students && this.lesson.students.length>0) {
                    this.lesson.students.forEach(function(student) {
                        if(student_id === student.id && student.pivot.visit ===1) value = true;
                    });
                }
                return value;
            },
        }
    }
</script>