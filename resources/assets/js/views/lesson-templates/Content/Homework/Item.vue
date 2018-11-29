<template>
    <div class="w-75 m-auto">
        <div class="alert alert-danger clearfix" v-if="test.length==0">
            <div class="pull-left" style="font-weight:500">Задание не добавлено</div>
        </div>
        <div class="clearfix">
            <div class="lead mb-2" style="font-weight:400">
                Домашнее задание
            </div>
        </div>
        <div class="clearfix mb-4">
            <div class="pull-left">
                <div class="btn-group">
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" :class="{'active': activeTab == 'answers'}" @click="activeTab = 'answers'"><span class="fa fa-question mr-1"></span>Вопросы и ответы</a>
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" :class="{'active': activeTab == 'preview'}" @click="activeTab = 'preview'"><span class="fa fa-eye mr-1"></span>Предпросмотр</a>
                </div>
            </div>
            <div class="pull-right">
                <a href="javascript:void(0)" @click="$emit('homeworkEdit')" class="btn btn-sm btn-secondary ml-3">
                    <span class="fa fa-pencil"></span>
                    изменить
                </a>
            </div>
        </div>
        <answers v-if="activeTab == 'answers'" :task_groups="test"></answers>
        <preview v-if="activeTab == 'preview'" :tasks="tasks"></preview>
    </div>
</template>
<script>
    import { del } from '../../../../helpers/api';
    export default {

        props: ['test'],

        data() {
            return {
                activeTab: 'answers'
            }
        },
        computed: {
            tasks() {
                var arr=[];
                if(this.test) {
                    this.test.forEach(function(task_group) {
                        if(task_group.tasks && task_group.tasks.length>0) {
                            task_group.tasks.forEach(function(task) {
                                arr.push(task);
                            });
                        }
                    });
                }
                return arr;
            }
        },
        components: {
            'answers': require('../../../../components/lms/Homework/Answers.vue'),
            'preview': require('../../../../components/lms/Tests/Preview.vue')
        },
        methods: {
        }
    }
</script>