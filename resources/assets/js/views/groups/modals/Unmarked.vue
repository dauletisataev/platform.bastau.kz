<template>
    <div>
        <b-modal ref="modal" v-if="transform" size="lg">
            <div slot="modal-title">
                Проверка заданий <span class="badge badge-primary ml-1">{{ currentPage+1 }} из {{ getCount() }}</span>
            </div>
            <div v-for="task in tasks">
                <div v-for="(student,index) in task.students" v-show="currentPage==student.page">
                    <div class="alert alert-info small mb-3">
                        <router-link :to="{ name: 'student', params: { id: student.id }}">
                            <span class="font-weight-bold"><span class="fa fa-user"></span> {{ student.user.name }}</span>
                        </router-link>
                        <span v-if="getType(task)==1">
                            в материялах занятия
                            <router-link :to="{ name: 'lesson_content', params: { id: task.task_group.material.page.lesson_id, group_id: group_id}}" class="font-weight-bold">{{task.task_group.material.page.lesson.title}}</router-link>
                            в
                            <router-link :to="{name: 'group', params: {id: group_id}}" class="font-weight-bold">{{ ' '+group_id+' группе' }}</router-link>
                        </span>
                        <span v-if="getType(task)==2">
                            в тестировании
                            <router-link :to="{ name: 'lesson_content', params: { id: task.task_group.test.lesson.id, group_id: group_id}}" class="font-weight-bold">{{task.task_group.test.name}}</router-link>
                            из занятия
                            <router-link :to="{ name: 'lesson_content', params: { id: task.task_group.test.lesson.id, group_id: group_id}}" class="font-weight-bold">{{task.task_group.test.lesson.title}}</router-link>
                            в
                            <router-link :to="{name: 'group', params: {id: group_id}}" class="font-weight-bold">{{ ' '+group_id+' группе' }}</router-link>
                        </span>
                        <span v-if="getType(task)==3">
                            в домашнем задании из занятия
                            <router-link :to="{ name: 'lesson_content', params: { id: task.task_group.lesson.id, group_id: group_id}}" class="font-weight-bold">{{task.task_group.lesson.title}}</router-link>
                            в
                            <router-link :to="{name: 'group', params: {id: group_id}}" class="font-weight-bold">{{ ' '+group_id+' группе' }}</router-link>
                        </span>
                    </div>
                        <div class="card mb-2">
                            <div class="card-header h6">
                                Задание
                            </div>
                            <div class="card-block small">
                                <div class="font-weight-bold mb-3">
                                    {{ task.description }}
                                    <div><span v-if="task.words">({{task.words}} words)</span></div>
                                </div>
                                <div class="mb-2">
                                    <img v-if="task.image" :src="task.image" class="img-fluid">
                                </div>
                                <div class="mb-2">
                                    <audio v-if="task.audio" style="width: 100%" controls>
                                        <source :src="task.audio"/>
                                    </audio>
                                </div>
                                <div v-for="(question,index) in task.questions">
                                    • {{ question.content }}
                                </div>
                            </div>
                        </div>
                            <div class="card mb-2">
                                <div class="card-header h6">
                                    Результат
                                </div>
                                <div class="card-block" style="max-height: 200px;overflow-y:scroll; white-space: pre-line">
                                    {{ student.pivot.essay }}
                                </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-header h6">
                                    Оценка
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="h6">полнота</div>
                                            <div class="btn-group btn-group-sm" data-toggle="buttons">
                                                <label class="btn btn-primary" @click="student.pivot.mark1=1" :class="{'active': student.pivot.mark1==1}"><input type="radio"> 1</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark1=2" :class="{'active': student.pivot.mark1==2}"><input type="radio"> 2</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark1=3" :class="{'active': student.pivot.mark1==3}"><input type="radio"> 3</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark1=4" v-if="task.rating==0" :class="{'active': student.pivot.mark1==4}"><input type="radio"> 4</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark1=5" v-if="task.rating==0" :class="{'active': student.pivot.mark1==5}"><input type="radio"> 5</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="h6">использ. новых слов</div>
                                            <div class="btn-group btn-group-sm" data-toggle="buttons">
                                                <label class="btn btn-primary" @click="student.pivot.mark2=1" :class="{'active': student.pivot.mark2==1}"><input type="radio"> 1</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark2=2" :class="{'active': student.pivot.mark2==2}"><input type="radio"> 2</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark2=3" :class="{'active': student.pivot.mark2==3}"><input type="radio"> 3</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark2=4" :class="{'active': student.pivot.mark2==4}"><input type="radio"> 4</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark2=5" v-if="task.rating==0" :class="{'active': student.pivot.mark2==5}"><input type="radio"> 5</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="h6">грамматика</div>
                                            <div class="btn-group btn-group-sm" data-toggle="buttons">
                                                <label class="btn btn-primary" @click="student.pivot.mark3=1" :class="{'active': student.pivot.mark3==1}"><input type="radio"> 1</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark3=2" :class="{'active': student.pivot.mark3==2}"><input type="radio"> 2</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark3=3" :class="{'active': student.pivot.mark3==3}"><input type="radio"> 3</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark3=4" v-if="task.rating==0" :class="{'active': student.pivot.mark3==4}"><input type="radio"> 4</label>
                                                <label class="btn btn-primary" @click="student.pivot.mark3=5" v-if="task.rating==0" :class="{'active': student.pivot.mark3==5}"><input type="radio"> 5</label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="display-4 pull-right text-muted">{{ getMark(student) }}/{{ task.rating==0 ? 15 : 10 }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            <div slot="modal-footer">
                <button @click="next()" class="btn btn-success">{{ currentPage<(getCount()-1) ? 'Оценить и продолжить' : 'Оценить' }}</button>
            </div>
        </b-modal>
    </div>
</template>
<script>
    import { post } from '../../../helpers/api';

    export default {
        props: ['tasks','group_id'],
        data() {
            return {
                currentPage: 0,
                transform: false,
            }
        },
        mounted() {
            this.transform = false;
            this.getArray();
            this.currentPage = 0;
        },
        methods: {
            getArray() {
                let count = 0;
                if(this.tasks && this.tasks.length > 0) {
                    this.tasks.forEach(function(task) {
                        if(task && task.students && task.students.length>0) {
                            task.students.forEach(function (student) {
                                Vue.set(student,'page',count);
                                count++;
                            });
                        }
                    });
                }
                this.transform = true;
            },
            getCount() {
                return this.tasks[this.tasks.length-1].students[this.tasks[this.tasks.length-1].students.length-1].page + 1;
            },
            getType(task) {
                if(task.task_group.is_homework==1) {
                    return 3;
                } else {
                    if(task.task_group.material_id) {
                        return 1;
                    } else {
                        return 2;
                    }
                }
            },
            getMark(student) {
                let mark = 0;
                if(student.pivot.mark1) mark += student.pivot.mark1;
                if(student.pivot.mark2) mark += student.pivot.mark2;
                if(student.pivot.mark3) mark += student.pivot.mark3;
                return mark;
            },
            next() {
                let _this = this;
                if(this.currentPage<(this.getCount()-1)) {
                    this.currentPage++
                } else {
                    post(_this,'/api/mark_tasks', {tasks: this.tasks}, function (response) {
                        _this.$emit('reload');
                        _this.$refs.modal.hide();
                    });
                }
            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
        }
    }

</script>