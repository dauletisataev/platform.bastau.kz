<template>
</template>

<script>

    export default  {

        data() {

            return {
            }

        },

        methods: {

            // LMS
            quillConfig() {
                return {
                    theme: 'bubble',
                    modules: {
                        imageResize: {}
                    }
                }
            },
            quillToolbar() {
                return [
                    [{'header': 1}, {'header': 2}],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['image','link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ];
            },

            // Создание массива для предпросмотра упражнения в материале

            makeArrayFromTask(task) {
                let _this = this;
                let count = '';
                let temp = [];
                if(task.questions && task.questions.length>0) {
                    task.questions.forEach(function(question) {
                        count = _this.countWords(question.content);
                        if(count && count.length>0) {
                            count.forEach(function(item,index) {
                                _this.$set(question,'answer'+index,'?');
                            });
                        }
                    });
                }
                _this.$set(task,'essay','');
                // Shuffle if type = 5
                if(task.task_type_id === 5) {
                    temp = task.questions[0].content.split(" ");
                    temp = _this.shuffle(temp);
                    _this.$set(task.questions[0],'shuffled',temp.join(" "));
                }
            },

            makeArrayFromTasks(tasks) {
                let _this = this;
                var count = '';
                var temp = [];
                if(tasks && tasks.length>0) {
                    tasks.forEach(function(task) {
                        if(task.questions && task.questions.length>0) {
                            task.questions.forEach(function(question) {
                                count = _this.countWords(question.content);
                                if(count && count.length>0) {
                                    count.forEach(function(item,index) {
                                        _this.$set(question,'answer'+index,'?');
                                    });
                                }
                            });
                        }
                        // Shuffle if type = 5
                        if(task.task_type_id==5) {
                            temp = task.questions[0].content.split(" ");
                            temp = _this.shuffle(temp);
                            _this.$set(task.questions[0],'shuffled',temp.join(" "));
                        }
                    });
                }
            },

            makeArrayFromTasksStudents(tasks) {
                let _this = this;
                var count = '';
                var temp = [];
                if(tasks && tasks.length>0) {
                    tasks.forEach(function(task) {
                        if(task.questions && task.questions.length>0) {
                            task.questions.forEach(function(question,qIndex) {
                                _this.$set(question,'answer_bool'+qIndex,0);
                                count = _this.countWords(question.content);
                                if(count && count.length>0) {
                                    count.forEach(function(item,index) {
                                        if(task.task_type_id !== 2) {
                                            _this.$set(question,'answer'+index,'?');
                                        } else {
                                            _this.$set(question,'answer'+index,'');
                                        }

                                    });
                                }
                            });
                        }
                        _this.$set(task,'essay','');
                        // Shuffle if type = 5
                        if(task.task_type_id==5) {
                            temp = task.questions[0].content.split(" ");
                            temp = _this.shuffle(temp);
                            _this.$set(task.questions[0],'shuffled',temp.join(" "));
                        }
                    });
                }
            },

            // Создание массива из слов

            countWords(str) {
                let words = [];
                let buf = "";
                let state = false;
                for(var i = 0; i < str.length; i++) {
                    if(str[i] == '[') {
                        state = true
                    }
                    if(str[i] == ']') {
                        state = false;
                    }
                    buf += str[i];
                    if(str[i] == " " || (str[i+1] && str[i+1] == '[') || (str[i] == ']' && str[i+1] && str[i+1] != " ")) {
                        if(!state) {
                            words.push(buf);
                            buf = "";
                        }
                    }

                }
                if(buf.length>0) {
                    words.push(buf);
                }
                return words;
            },

            // Перемешать слова для задания типа "Собрать"

            shuffle(arr) {
                var j,x,i;
                for (i = arr.length - 1; i > 0; i--) {
                    j = Math.floor(Math.random() * (i + 1));
                    x = arr[i];
                    arr[i] = arr[j];
                    arr[j] = x;
                }
                return arr;
            },

            // Массив из текущих ответов
            taskAnswers(questions) {
                let _this = this;
                let arr = [];
                let words = [];
                questions.forEach(function(question) {
                    words = _this.countWords(question.content);
                    if (words && words.length > 0) {
                        words.forEach(function (word, index) {
                            if (question['answer' + index] !== "?") {
                                arr.push(question['answer' + index]);
                            }
                        })
                    }
                });
                return arr;
            },

            // Варианты ответа для заданий типа "Перетащить"

            taskVariantsType1(questions) {
                let arr=[];
                let vars=[];
                let answers = this.taskAnswers(questions);
                let index = null;
                if(questions && questions.length>0) {
                    questions.forEach(function(question) {
                        vars = question.content.match(/\[(.*?)\]/g);
                        if(vars && vars.length>0) {
                            vars.forEach(function(item) {
                                arr.push(item.slice(1,-1));
                            });
                        }
                    });
                }
                answers.forEach(function(answer) {
                    index = arr.indexOf(answer);
                    if(index > -1) {
                        arr.splice(index,1);
                    }
                });
                return arr.sort();
            },

            // Создание массива
            countWords(str) {
                let words = [];
                let buf = "";
                let state = false;
                for(var i = 0; i < str.length; i++) {
                    if(str[i]=='[') {
                        state = true
                    }
                    if(str[i]==']') {
                        state = false;
                    }
                    buf += str[i];
                    if(str[i]==" " || (str[i+1] && str[i+1] == '[') || (str[i] == ']' && str[i+1] && str[i+1] != " ")) {
                        if(!state) {
                            words.push(buf);
                            buf = "";
                        }
                    }

                }
                if(buf.length>0) {
                    words.push(buf);
                }
                return words;
            },

            // Варианты ответа для заданий типа "Выбрать"

            taskVariantsType4(string) {
                let str = string;
                let arr = [];
                str = str.replace("[","");
                str = str.replace("]","");
                str = str.replace("*","");
                str = str.replace(".","");
                str = str.replace("?","");
                arr = str.split("/");
                return arr;
            },

            // Создание массива из слов для вопросов
            taskQuestions (content, questions) {
                let arr = this.countWords(content);
                // Задание типа "собрать" использует эту функцию для генерации вариантов.
                // Поэтому передает questions для исключения вхождений
                if(questions) {
                    let answers = this.taskAnswers(questions);
                    let index = null;
                    answers.forEach(function(answer) {
                        index = arr.indexOf(answer);
                        if(index > -1) {
                            arr.splice(index,1);
                        }
                    });
                }
                return arr;
            },


        },

        created() {
        }





    }

</script>