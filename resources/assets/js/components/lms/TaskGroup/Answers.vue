<template>
    <div class="card mb-4">
        <div class="card-block">
            <div class="mb-5" v-for="task in tasks">
                <div class="h4 mb-3">
                    {{ task.description }}
                    <div v-if="task.task_type_id==6">
                        <span v-if="task.words">({{task.words}} words)</span>
                    </div>
                </div>
                <div class="mb-2">
                    <img v-if="task.image" :src="task.image" class="img-fluid">
                </div>
                <div class="mb-2">
                    <audio v-if="task.audio" style="width: 100%" controls>
                        <source :src="task.audio"/>
                    </audio>
                </div>
                <div class="bg-faded p-4 rounded">
                    <div v-if="task.task_type_id==1 || task.task_type_id == 2">
                        <div v-for="(question,index) in task.questions">
                            <div v-html="answeredQuestion(question.content)"></div>
                            <hr class="my-2" v-if="index<(task.questions.length-1)">
                        </div>
                    </div>
                    <div v-if="task.task_type_id==3">
                        <div v-for="(question,index) in task.questions">
                            <span :class="{'fa fa-check-square mr-2 text-success': question.is_correct, 'fa fa-square text-muted mr-2': !question.is_correct}"></span> {{ question.content }}
                            <hr class="my-2" v-if="index<(task.questions.length-1)">
                        </div>
                    </div>
                    <div v-if="task.task_type_id==4">
                        <div v-for="(question,index) in task.questions">
                            <div v-html="answeredQuestion2(question.content)"></div>
                            <hr class="my-2" v-if="index<(task.questions.length-1)">
                        </div>
                    </div>
                    <div v-if="task.task_type_id==5">
                        <div v-for="(question,index) in task.questions">
                            <div v-html="answeredQuestion3(question.content)"></div>
                            <hr class="my-2" v-if="index<(task.questions.length-1)">
                        </div>
                    </div>
                    <div v-if="task.task_type_id==6">
                        <div v-for="(question,index) in task.questions">
                            • {{ question.content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: ['tasks'],

        data() {
            return {
            }
        },
        methods: {
            answeredQuestion(content) {
                var str = '';
                str = content.replace(/\[/g, "<span class='badge mx-1 badge-success'>");
                str = str.replace(/\]/g,"</span>");
                return str;
            },
            answeredQuestion2(content) {
                var str = '';
                var selects = content.match(/\[(.*?)\]/g);
                var answer = '';
                if(selects && selects.length>0) {
                    str = content;
                    selects.forEach(function(select) {
                        answer = select.match(/\*(.*?)[\/\]]/);
                        answer = answer[0].slice(1,-1);
                        str = str.replace(/\[(.*?)\]/,"<span class='badge mx-1 badge-success'>"+answer+"</span>");
                    });
                }
                return str;
            },
            answeredQuestion3(content) {
                var str = '';
                var arr = [];
                if(content) {
                    arr = content.split(" ");
                    if(arr && arr.length>0) {
                        arr.forEach(function(word) {
                            str = str + "<span class='badge mx-1 badge-success'>" + word + "</span>";
                        });
                    }
                }
                return str;
            },
        }
    }
</script>