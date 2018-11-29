<template>
    <div class="card w-75 m-auto">
        <div class="alert alert-danger clearfix" v-if="lesson.tests.length==0">
            <div class="pull-left" style="font-weight:500">Тесты не добавлены</div>
        </div>
        <table class="table mb-0">
            <thead class="thead-default h6">
                <tr>
                    <th>название</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="test in lesson.tests">
                    <td>
                        <div style="font-weight:500">{{ test.name }}</div>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <div class="btn-group">
                                <a href="javascript:void(0)" @click="$emit('testItem',test.id)" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-title="открыть" data-original-title="" title="">
                                    <span class="fa fa-eye"></span>
                                </a>
                                <a v-if="lesson.is_started==0" href="javascript:void(0)" @click="$emit('testEdit',test.id)" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-title="изменить" data-original-title="" title="">
                                    <span class="fa fa-pencil"></span>
                                </a>
                                <a v-if="lesson.is_started==0" href="javascript:void(0)" @click="deleteTest(test.id)" class="btn btn-sm btn-outline-danger" data-toggle="tooltip" data-title="удалить тест" data-original-title="" title="">
                                    <span class="fa fa-trash"></span>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="lesson.is_started==0" >
                    <td colspan="4">
                        <a href="javascript:void(0)" @click="$emit('newTest')" class="text-muted">
                            <span class="fa fa-plus mr-2"></span>создать тестирование
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    import { del } from '../../../helpers/api';
    export default {

        props: ['lesson'],
        data() {
            return {
            }
        },
        components: {
            'test': require('./Item.vue'),
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