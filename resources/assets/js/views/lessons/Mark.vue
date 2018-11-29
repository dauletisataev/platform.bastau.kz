<template>
    <div class="w-75 m-auto">
        <div class="row mb-4">
            <div class="col">
                <div class="lead" style="font-size: 30px">
                    Отметьте учеников, которые присутствовали на занятии
                </div>
            </div>
        </div>
        <hr>
        <label class="custom-control custom-checkbox d-block lead" v-for="student in lesson.group.students" v-if="!$common.existIdInArray(lesson.id, student.visit_lessons) && !student.is_balance_freeze && student.in_archive!=1 && student.pivot.deleted!=1">
            <input v-model="selectedStudentIds" :value="student.id" type="checkbox" checked class="custom-control-input">
            <span class="custom-control-indicator mt-1"></span>
                {{ student.user.name }}
        </label>
        <hr>
        <div class="mb-4">
            <a href="javascript:void(0)" @click="submit" class="btn btn-lg btn-primary">
                сохранить и выйти
            </a>
        </div>
    </div>
</template>
<script>
    import { post } from '../../helpers/api';
    export default {

        props: ['lesson'],

        data() {
            return {
                selectedStudentIds: [],
            }
        },
        methods: {
            submit() {
                let _this = this;
                post(_this, '/api/lesson-mark/'+_this.lesson.id, {visit_student_ids: _this.selectedStudentIds}, function(){
                    _this.$emit('toGroup');
                }, function(error) {})

            },
        }
    }
</script>