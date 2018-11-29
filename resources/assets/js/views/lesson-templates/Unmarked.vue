<template>
    <div>
        <b-modal ref="modal" size="lg">
            <div slot="modal-title">
                Проверка заданий <span class="badge badge-primary ml-1">{{ currentTask+1 }} из {{ getCount() }}</span>
            </div>
            <div v-for="(task,index) in tasks" v-show="currentTask == index">
                <div class="alert alert-info small mb-3">
                    <span class="font-weight-bold"><span class="fa fa-user"></span> {{ task.user_name }}</span>
                    <span v-if="task.source == 'homework'">
                        {{ ' в домашнем задании' }}
                    </span>
                    <span v-if="task.source == 'test'">
                        {{ ' в тесте ' }} + {{ task.test_name }}
                    </span>
                    в занятии<span class="font-weight-bold"> {{ ' ' + task.lesson_template_item_name }} </span>
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
                    <div class="card-block" style="max-height: 200px;overflow-y:scroll;white-space: pre-line">
                        {{ task.essay }}
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-header h6">
                        Комментарий
                    </div>
                    <textarea
                            style="width: 100%; max-height: 200px; overflow-y: scroll; border: none;"
                            class="card-block"
                            v-model="task.comment"
                    ></textarea>
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
                                    <label class="btn btn-primary" @click="task.mark1=1" :class="{'active': task.mark1==1}"><input type="radio"> 1</label>
                                    <label class="btn btn-primary" @click="task.mark1=2" :class="{'active': task.mark1==2}"><input type="radio"> 2</label>
                                    <label class="btn btn-primary" @click="task.mark1=3" :class="{'active': task.mark1==3}"><input type="radio"> 3</label>
                                    <label class="btn btn-primary" @click="task.mark1=4" v-if="task.rating==0" :class="{'active': task.mark1==4}"><input type="radio"> 4</label>
                                    <label class="btn btn-primary" @click="task.mark1=5" v-if="task.rating==0" :class="{'active': task.mark1==5}"><input type="radio"> 5</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="h6">использ. новых слов</div>
                                <div class="btn-group btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-primary" @click="task.mark2=1" :class="{'active': task.mark2==1}"><input type="radio"> 1</label>
                                    <label class="btn btn-primary" @click="task.mark2=2" :class="{'active': task.mark2==2}"><input type="radio"> 2</label>
                                    <label class="btn btn-primary" @click="task.mark2=3" :class="{'active': task.mark2==3}"><input type="radio"> 3</label>
                                    <label class="btn btn-primary" @click="task.mark2=4" :class="{'active': task.mark2==4}"><input type="radio"> 4</label>
                                    <label class="btn btn-primary" @click="task.mark2=5" v-if="task.rating==0" :class="{'active': task.mark2==5}"><input type="radio"> 5</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="h6">грамматика</div>
                                <div class="btn-group btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-primary" @click="task.mark3=1" :class="{'active': task.mark3==1}"><input type="radio"> 1</label>
                                    <label class="btn btn-primary" @click="task.mark3=2" :class="{'active': task.mark3==2}"><input type="radio"> 2</label>
                                    <label class="btn btn-primary" @click="task.mark3=3" :class="{'active': task.mark3==3}"><input type="radio"> 3</label>
                                    <label class="btn btn-primary" @click="task.mark3=4" v-if="task.rating==0" :class="{'active': task.mark3==4}"><input type="radio"> 4</label>
                                    <label class="btn btn-primary" @click="task.mark3=5" v-if="task.rating==0" :class="{'active': task.mark3==5}"><input type="radio"> 5</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="display-4 pull-right text-muted">{{ getMark(task) }}/{{ task.rating==0 ? 15 : 10 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div slot="modal-footer">
                <button
                        @click="next()"
                        class="btn btn-success"
                        :disabled="formSending"
                >
                    <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ currentTask<(getCount()-1) ? 'Оценить и продолжить' : 'Оценить' }}
                </button>
            </div>
        </b-modal>
    </div>
</template>
<script>
    import { post } from '../../helpers/api';

    export default {
        props: ['tasks'],
        data() {
            return {
                currentTask: 0,
                formSending: false,
            }
        },
        methods: {
            getCount() {
                return this.tasks.length;
            },
            getMark(obj) {
                let mark = 0;
                if(obj.mark1) mark += obj.mark1;
                if(obj.mark2) mark += obj.mark2;
                if(obj.mark3) mark += obj.mark3;
                return mark;
            },
            next() {
                let _this = this;
                if(this.currentTask<(this.getCount()-1)) {
                    this.currentTask++
                } else {
                    this.formSending = true;
                    post(_this,'/api/mark-online-tasks', {tasks: this.tasks}, function (response) {
                        _this.$emit('reload');
                        _this.$refs.modal.hide();
                        _this.formSending = false;
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