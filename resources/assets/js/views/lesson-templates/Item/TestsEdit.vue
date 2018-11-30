<template>
    <div>
        <div class="row">
            <div class="col-3">
                <form class="card card-outline-primary mb-4">
                    <div class="card-block">
                        <div class="form-group">
                            <label class="small text-muted" style="font-weight:500" for="field3">ограничение времени (минут)</label>
                            <input id="field3" type="text" class="form-control" v-model="template.test_duration" placeholder="12">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-9">
                 <div class="card-block">
                     <div v-for="(question,index1) in template.test_questions">
                         <div class="form-group mb-4">
                             <label for="field5" class="small text-muted d-block" style="font-weight:500">вопрос или описание задания</label>
                             <textarea class="d-block form-control form-control-lg p-0" style="border: none; height: 58px; margin-top: 0px; margin-bottom: 0px;" placeholder="Введите вопрос или описание задание здесь" id="field5" v-model="question.value"></textarea>
                         </div>
                         <div class="form-group">
                             <div class="input-group mt-1" style="flex-wrap: wrap;" v-for="(option,index2) in question.answers">
                                 <input type="checkbox" :false-value="0" :true-value="1" v-model="option.is_correct">
                                 <input :id="'input_'+index2+'_'+index1" @keyup.enter="onEnter(question, index2, index1)" type="text" class="form-control"  v-model="option.value">
                             </div>
                             <button @click="addQuestion(question)" class="btn btn-secondary btn-sm mt-1">добавить еще</button>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
        <a href="javascript:void(0)" @click="$emit('save')"  class="rounded h2 p-3 bg-primary text-white rounded-circle" style="position:fixed;right:20px;bottom:20px;"><div class="fa fa-fw fa-save"></div></a>
    </div>
</template>
<script>

    export default {

        props: ['template'],

        data() {
            return {
            }
        },
        components: {
        },

        methods: {
            addQuestion(task) {
                console.log('task:'+task.answers.length);
                task.answers.push({id: '', lesson_question_id: task.id, option: task.answers.length, value: '', is_correct: 0});
            },
            deleteQuestion(task,question) {
                /*var index = task.questions.indexOf(question);
                if(index > -1) task.questions.splice(index,1);*/
            },
            onEnter(task,index2, index1) {
                console.log('task:'+task.answers.length);
                    task.answers.push({id: '',  lesson_question_id: task.id, option: task.answers.length, value: '', is_correct: 0});
                    this.$nextTick(function() {
                        let i = task.answers.length - 1;
                        $('#input_'+index2+'_'+i).focus();
                        $('#input_'+i+'_'+index1).focus();
                        console.log('#input_'+index2+'_'+i);
                        console.log('#input_'+i+'_'+index1);
                    });
            }
        }
    }
</script>