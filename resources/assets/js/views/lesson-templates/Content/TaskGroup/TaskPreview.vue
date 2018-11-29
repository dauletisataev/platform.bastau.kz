<template>
        <div class="card-block">
            <div class="w-100">
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
                <!-- Task type 1 variants -->
                <div class="mb-4 h5" v-if="task.task_type_id==1">
                            <span draggable="true" @dragstart="selected=item" v-for="item in $lms.taskVariantsType1(task.questions)" class="badge badge-primary mr-1">
                                <span class="fa mr-2 ml-2 fa-ellipsis-v">
                                </span>
                                    {{ item }}
                            </span>
                </div>
                <!-- Task type 1 questions -->
                <div v-for="question in task.questions" v-if="task.task_type_id==1">
                    <hr>
                    <span v-for="(word,index) in $lms.taskQuestions(question.content)">
                                <span v-if="!word.match(/\[(.*?)\]/)">{{ word }}</span>
                                <span v-else draggable="true" @dragend="question['answer'+index]='?'" @dragover.prevent @dragstart="selected=question['answer'+index]" @drop="onDrop(question,index)" class="badge"  :class="{'badge-default pl-3 pr-3 ml-2 mr-2': question['answer'+index]=='?', 'badge-primary mr-1 ml-1': question['answer'+index]!='?'}">
                                    <span class="fa mr-2 ml-2 fa-ellipsis-v" v-if="question['answer'+index]!='?'"></span>
                                    {{ question['answer'+index] }}
                                </span>
                            </span>
                </div>
                <!-- Task type 2 questions -->
                <div v-for="question in task.questions" v-if="task.task_type_id==2">
                    <hr>
                    <span v-for="(word,index) in $lms.taskQuestions(question.content)">
                                <span v-if="!word.match(/\[(.*?)\]/)">{{ word }}</span>
                                <span v-else>
                                    <input type="text" class="form-control form-control-sm mr-2 ml-2 w-25" style="display: inline">
                                </span>
                            </span>
                </div>
                <!-- Task type 3 questions -->
                <div v-for="question in task.questions" v-if="task.task_type_id==3">
                    <hr>
                    <div>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                            {{ question.content }}
                        </label>
                    </div>
                </div>
                <!-- Task type 4 questions -->
                <div v-for="question in task.questions" v-if="task.task_type_id==4">
                    <hr>
                    <span v-for="(word,index) in $lms.taskQuestions(question.content)">
                        <span v-if="!word.match(/\[(.*?)\]/)">{{ word }}</span>
                        <span v-else class="form">
                            <select class="form-control-sm ml-2 mr-2"  style="display: inline">
                                <option>?</option>
                                <option v-for="variant in $lms.taskVariantsType4(word)">{{ variant }}</option>
                            </select>
                        </span>
                    </span>
                </div>
                <!-- Task type 5 variants questions -->
                <div v-for="question in task.questions" v-if="task.task_type_id==5">
                    <div class="mb-4 h5" v-if="task.task_type_id==5">
                                <span draggable="true" @dragstart="selected=item" v-for="item in $lms.taskQuestions(question.shuffled, task.questions)" class="badge badge-primary mr-1">
                                    <span class="fa mr-2 ml-2 fa-ellipsis-v">
                                    </span>
                                        {{ item }}
                                </span>
                    </div>
                    <hr>
                    <span v-for="(word,index) in $lms.taskQuestions(question.content)">
                                <span draggable="true" @dragend="question['answer'+index]='?'" @dragover.prevent @dragstart="selected=question['answer'+index]" @drop="onDrop(question,index)" class="badge"  :class="{'badge-default pl-3 pr-3 ml-2 mr-2': question['answer'+index]=='?', 'badge-primary mr-1 ml-1': question['answer'+index]!='?'}">
                                    <span class="fa mr-2 ml-2 fa-ellipsis-v" v-if="question['answer'+index]!='?'"></span>
                                    {{ question['answer'+index] }}
                                </span>
                    </span>
                </div>
                <!-- Task type 6 questions -->
                <div v-if="task.task_type_id==6">
                    <hr>
                    <div class="lead mb-4" v-for="(question,index) in task.questions">
                        • {{ question.content }}
                    </div>
                    <textarea class="form-control" rows="12" placeholder="Type here"></textarea>
                </div>
            </div>
        </div>
</template>

<script>


    export default {

        props: ['task'],

        data() {
            return {
            }
        },
        methods: {
            onDrop (question, index) {
                question['answer'+index] = this.selected;
            },
        },
        created() {
            this.tasks = this.$lms.makeArrayFromTask(this.task);
        }
    }
</script>
