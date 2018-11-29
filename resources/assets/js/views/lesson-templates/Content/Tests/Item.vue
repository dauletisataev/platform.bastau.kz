<template>
    <div class="w-75 m-auto">
        <div class="clearfix">
            <div class="lead mb-2" style="font-weight:400">
                {{ test.name }}
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
                <a href="javascript:void(0)" @click="$emit('testEdit',test.id)" class="btn btn-sm btn-secondary ml-3">
                    <span class="fa fa-pencil"></span>
                    изменить
                </a>
                <a href="javascript:void(0)" @click="deleteTest(test.id)" class="btn btn-sm btn-outline-danger ml-1">
                    <span class="fa fa-trash"></span>
                    удалить
                </a>
            </div>
        </div>
        <answers v-if="activeTab == 'answers'" :task_groups="test.task_groups"></answers>
        <preview v-if="activeTab == 'preview'" :tasks="tasks" :name="test.name"></preview>
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
                if(this.test && this.test.task_groups) {
                    this.test.task_groups.forEach(function(task_group) {
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
            'answers': require('../../../../components/lms/Tests/Answers.vue'),
            'preview': require('../../../../components/lms/Tests/Preview.vue')
        },
        methods: {
            deleteTest(id) {
                let _this = this;
                del(_this, '/api/test-delete/'+id, {}, function (response) {
                    _this.$emit('update');
                });


            },
        }
    }
</script>