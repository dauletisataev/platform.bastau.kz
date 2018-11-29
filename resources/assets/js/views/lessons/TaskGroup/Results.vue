<template>
    <div class="card mx-auto mb-5">
            <table class="table mb-0">
                <tbody>
                <tr v-for="student in students" v-if="(student.in_archive!=1 || student.in_archive==null) && student.pivot.deleted==0">
                    <td style="font-weight: 500">{{ student.user.name }}</td>
                    <td v-if="completed(student.id,task_group)" style="font-weight: 500" :class="$common.getTextClass(percentage(student.id,task_group))">{{ percentage(student.id,task_group) }}%</td>
                    <td v-if="completed(student.id,task_group)">{{ result(student.id,task_group) }} из {{ maxResult(task_group) }}</td>
                    <td v-if="completed(student.id,task_group)" class="text-right"><a href="javascript:void(0)" @click="$emit('student',student)" class="btn btn-sm btn-secondary" data-original-title="" title=""><span class="fa fa-eye"></span></a></td>
                    <td v-if="!completed(student.id,task_group)">
                        <span style="color:#D8D8DA"><span class="fa fa-minus"></span></span>
                    </td>
                    <td v-if="!completed(student.id,task_group)">
                        <span style="color:#D8D8DA">не выполнено</span>
                    </td>
                    <td v-if="!completed(student.id,task_group)">

                    </td>
                </tr>
                </tbody>
            </table>
    </div>
</template>
<script>
    export default {
        props: ['task_group','students'],
        methods: {
            maxResult(task_group) {
                var value = 0;
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
                return value;
            },
            result(student_id,task_group) {
                var value = 0;
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
                return value;
            },
            percentage(student_id,task_group) {
                var value = this.result(student_id,task_group);
                var max = this.maxResult(task_group);
                return Math.round(value / max*100);
            },
            completed(student_id,task_group) {
                var found = false;
                if(task_group.students && task_group.students.length>0) {
                    task_group.students.forEach(function(student) {
                        if(!found) {
                            if(student.id==student_id) found = true;
                        }
                    });
                }
                return found;
            }
        }
    }
</script>