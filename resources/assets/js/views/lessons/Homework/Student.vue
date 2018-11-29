<template>
    <div>
        <div class="card text-primary text-center p-3 bg-faded mb-4">
            <div class="lead" style="font-weight:400">
                <span class="fa fa-user mr-2"></span>
                {{ name }}
            </div>
        </div>
        <div class="card mb-5">
            <div class="card-block">
                <table class="table table-sm mt-3">
                    <tbody><tr class="table-warning h6">
                        <td>Общий результат</td>
                        <td class="text-muted">{{ result() }} из {{ maxResult() }}</td>
                        <td class="text-right" :class="$common.getTextClass(percentage())">{{ percentage() }}%</td>
                    </tr>
                    <tr v-for="task_group in test.task_groups">
                        <td>{{ task_group.section.name }}</td>
                        <td>{{ sectionResult(task_group) }} из {{ sectionMaxResult(task_group) }}</td>
                        <td class="text-right">{{ sectionPercentage(task_group) }}%</td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
        <div class="card mb-5" v-for="task_group in test">
            <div class="card-header h6">
                Раздел: {{ task_group.section.name }}
            </div>
            <div class="card-block">
                <div v-for="task in task_group.tasks" class="mb-5">
                    <div class="h4 text-danger mb-3" v-if="!isTaskCorrect(task) && task.task_type_id<6">
                        <span class="fa fa-times-circle"></span> {{ task.description }}
                    </div>
                    <div class="h4 text-success mb-3" v-if="isTaskCorrect(task) && task.task_type_id<6">
                        <span class="fa fa-check-circle"></span> {{ task.description }}
                    </div>
                    <div class="h4 mb-3" v-if="task.task_type_id==6">
                        <span class="fa fa-circle"></span> {{ task.description }}
                        <div>({{ task.words }} words)</div>
                    </div>
                    <div class="mb-2">
                        <img v-if="task.image" :src="task.image" class="img-fluid">
                    </div>
                    <div class="mb-2">
                        <audio v-if="task.audio" style="width: 100%" controls>
                            <source :src="task.audio"/>
                        </audio>
                    </div>
                    <div class="mb-4" v-if="task.task_type_id==6">
                        <div v-for="question in task.questions">
                            • {{ question.content }}
                        </div>
                    </div>
                    <div class="bg-faded p-4 rounded">
                        <div v-for="(question,index) in task.questions" v-if="task.task_type_id!=6 && task.task_type_id!=3">
                            <span v-html="getAnswer(question)"></span>
                            <hr class="my-1" v-if="index<(task.questions.length-1)">
                        </div>
                        <div v-for="(question,index) in task.questions" v-if="task.task_type_id==3">
                            <span v-html="getAnswerBool(question)"></span>
                            <hr class="my-1" v-if="index<(task.questions.length-1)">
                        </div>
                        <div v-if="task.task_type_id==6">
                            <div v-html="getEssay(task)" style="white-space: pre-line"></div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-3">
                                    <div class="h6">полнота</div>
                                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                                        <label class="btn btn-primary" :class="{'active': getMark1(task)==1, 'disabled': getMark1(task)!=1}"><input type="radio"> 1</label>
                                        <label class="btn btn-primary" :class="{'active': getMark1(task)==2, 'disabled': getMark1(task)!=2}"><input type="radio"> 2</label>
                                        <label class="btn btn-primary" :class="{'active': getMark1(task)==3, 'disabled': getMark1(task)!=3}"><input type="radio"> 3</label>
                                        <label class="btn btn-primary" v-if="task.rating==0" :class="{'active': getMark1(task)==4, 'disabled': getMark1(task)!=4}"><input type="radio"> 4</label>
                                        <label class="btn btn-primary" v-if="task.rating==0" :class="{'active': getMark1(task)==5, 'disabled': getMark1(task)!=5}"><input type="radio"> 5</label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="h6">использ. новых слов</div>
                                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                                        <label class="btn btn-primary" :class="{'active': getMark2(task)==1, 'disabled': getMark2(task)!=1}"><input type="radio"> 1</label>
                                        <label class="btn btn-primary" :class="{'active': getMark2(task)==2, 'disabled': getMark2(task)!=2}"><input type="radio"> 2</label>
                                        <label class="btn btn-primary" :class="{'active': getMark2(task)==3, 'disabled': getMark2(task)!=3}"><input type="radio"> 3</label>
                                        <label class="btn btn-primary" :class="{'active': getMark2(task)==4, 'disabled': getMark2(task)!=4}"><input type="radio"> 4</label>
                                        <label class="btn btn-primary" v-if="task.rating==0" :class="{'active': getMark2(task)==5, 'disabled': getMark2(task)!=5}"><input type="radio"> 5</label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="h6">грамматика</div>
                                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                                        <label class="btn btn-primary" :class="{'active': getMark3(task)==1, 'disabled': getMark3(task)!=1}"><input type="radio"> 1</label>
                                        <label class="btn btn-primary" :class="{'active': getMark3(task)==2, 'disabled': getMark3(task)!=2}"><input type="radio"> 2</label>
                                        <label class="btn btn-primary" :class="{'active': getMark3(task)==3, 'disabled': getMark3(task)!=3}"><input type="radio"> 3</label>
                                        <label class="btn btn-primary" v-if="task.rating==0":class="{'active': getMark3(task)==4, 'disabled': getMark3(task)!=4}"><input type="radio"> 4</label>
                                        <label class="btn btn-primary" v-if="task.rating==0" :class="{'active': getMark3(task)==5, 'disabled': getMark3(task)!=5}"><input type="radio"> 5</label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="display-4 pull-right text-muted">{{ getMarks(task) }}/{{ task.rating==0 ? 15 : 10 }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['test','student_id','name'],
        methods: {
            maxResult() {
                var value = 0;
                this.test.forEach(function (task_group) {
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
            result() {
                var value = 0;
                var correct = true;
                let _this = this;
                this.test.forEach(function(task_group) {
                    task_group.tasks.forEach(function(task) {
                        if(task.task_type_id<6) {
                            task.questions.forEach(function(question) {
                                question.students.forEach(function(student) {
                                    if(student.id==_this.student_id) {
                                        if(student.pivot.is_correct==1) {
                                            value++;
                                        }
                                    }
                                });
                            });
                        } else {
                            task.students.forEach(function(student) {
                                if(student.id==_this.student_id) {
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
            isTaskCorrect(task) {
                var correct = true;
                let _this = this;
                task.questions.forEach(function(question) {
                    question.students.forEach(function(student) {
                        if(student.id==_this.student_id) {
                            if(student.pivot.is_correct!=1) {
                                correct = false;
                            }
                        }
                    });
                });
                return correct;
            },
            getAnswer(question) {
                let _this = this;
                var answer = "";
                question.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        answer = student.pivot.answer;
                    }
                });
                return answer;
            },
            getAnswerBool(question) {
                let _this = this;
                var answer = "";
                var html = "";
                question.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        answer = student.pivot.answer;
                    }
                });
                var correct = question.is_correct;
                if(answer==1) {
                    if(correct==1) {
                        html = "<span class='fa fa-check-circle mr-2 text-success'></span>" + question.content;
                    }
                    if(correct==0) {
                        html = "<span class='fa fa-check-circle mr-2 text-danger'></span>" + question.content;
                    }
                } else {
                    if(correct==0) {
                        html = "<span class='fa fa-circle mr-2 text-success'></span>" + question.content;
                    }
                    if(correct==1) {
                        html = "<span class='fa fa-circle mr-2 text-danger'></span>" + question.content;
                    }
                }
                return html;
            },
            getMark1(task) {
                let _this = this;
                var mark;
                task.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        mark = student.pivot.mark1;
                    }
                });
                return mark;
            },
            getMark2(task) {
                let _this = this;
                var mark;
                task.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        mark = student.pivot.mark2;
                    }
                });
                return mark;
            },
            getMark3(task) {
                let _this = this;
                var mark;
                task.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        mark = student.pivot.mark3;
                    }
                });
                return mark;
            },
            getMarks(task) {
                let _this = this;
                var mark;
                task.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        mark = student.pivot.mark1+student.pivot.mark2+student.pivot.mark3;
                    }
                });
                return mark;
            },
            getEssay(task) {
                let _this = this;
                var essay = "";
                task.students.forEach(function(student) {
                    if(student.id==_this.student_id) {
                        essay = student.pivot.essay;
                    }
                });
                return essay;
            },
            percentage() {
                var value = this.result();
                var max = this.maxResult();
                return Math.round(value / max*100);
            },
            sectionMaxResult(task_group) {
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
            sectionResult(task_group) {
                var value = 0;
                let _this = this;
                task_group.tasks.forEach(function(task) {
                    if(task.task_type_id<6) {
                        task.questions.forEach(function(question) {
                            question.students.forEach(function(student) {
                                if(student.id==_this.student_id) {
                                    if(student.pivot.is_correct==1) {
                                        value++;
                                    }
                                }
                            });
                        });
                    } else {
                        task.students.forEach(function(student) {
                            if(student.id==_this.student_id) {
                                value += student.pivot.mark1;
                                value += student.pivot.mark2;
                                value += student.pivot.mark3;
                            }
                        });
                    }
                });
                return value;
            },
            sectionPercentage(task_group) {
                var value = this.result(task_group);
                var max = this.maxResult(task_group);
                return Math.round(value / max*100);
            },
        }
    }
</script>