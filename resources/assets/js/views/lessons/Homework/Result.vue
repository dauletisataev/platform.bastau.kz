<template>
    <div class="card mx-auto mb-5">
        <table class="table mb-0">
            <tbody>
            <tr v-for="student in students" v-if="(student.in_archive!=1 || student.in_archive==null) && student.pivot.deleted==0">
                <td style="font-weight: 500">{{ student.user.name }}</td>
                <td v-if="completed(student.id)" style="font-weight: 500" :class="$common.getTextClass(percentage(student.id))">{{ percentage(student.id) }}%</td>
                <td v-if="completed(student.id)">{{ result(student.id) }} из {{ maxResult() }}</td>
                <td v-if="completed(student.id)" class="text-right"><a href="javascript:void(0)" @click="$emit('student',student)" class="btn btn-sm btn-secondary" data-original-title="" title=""><span class="fa fa-eye"></span></a></td>
                <td v-if="!completed(student.id)">
                    <span style="color:#D8D8DA"><span class="fa fa-minus"></span></span>
                </td>
                <td v-if="!completed(student.id)">
                    <span style="color:#D8D8DA">не выполнено</span>
                </td>
                <td v-if="!completed(student.id)">

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    export default {
        props: ['test','students'],
        methods: {
            maxResult() {
                var value = 0;
                this.test.forEach(function(task_group) {
                    task_group.tasks.forEach(function(task) {
                        if(task.task_type_id<6) {
                            task.questions.forEach(function (question) {
                                value++;
                            });
                        } else {
                            if(task.rating===1) {
                                value += 10;
                            } else {
                                value += 15;
                            }
                        }
                    });
                });
                return value;
            },
            result(student_id) {
                var value = 0;
                this.test.forEach(function(task_group) {
                    task_group.tasks.forEach(function(task) {
                        if(task.task_type_id<6) {
                            task.questions.forEach(function(question) {
                                question.students.forEach(function(student) {
                                    if(student.id==student_id) {
                                        if(student.pivot.is_correct==1) {
                                            value++;
                                        }
                                    }
                                });
                            });
                        } else {
                            task.students.forEach(function(student) {
                                if(student.id==student_id) {
                                    value += student.pivot.mark1;
                                    value += student.pivot.mark2;
                                    value += student.pivot.mark3;
                                }
                            });
                        }
                    });
                });
                return value;
            },
            percentage(student_id) {
                var value = this.result(student_id);
                var max = this.maxResult();
                return Math.round(value / max*100);
            },
            completed(student_id) {
                var was = false;
                if(this.test && this.test.length>0) {
                    if(this.test[0].students && this.test[0].students.length>0) {
                        this.test[0].students.forEach(function(student) {
                            if(student.id == student_id) {
                                if(!was) was = true;
                            }
                        });
                    }
                }
                return was;
            }
        }
    }
</script>