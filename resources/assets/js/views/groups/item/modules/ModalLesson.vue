<template>

    <div>

        <lesson-form ref="editLesson" :data="$common.data" :_form="lesson" :group="group" :lessonDate="lessonDate" v-on:formSending="getItem"></lesson-form>

        <lesson-load ref="loadLesson" :group="group" v-on:formSending="getItem"></lesson-load>

        <lesson-complete ref="completeLesson" :data="$common.data" :_form="lesson" :group="group" :students="group.students" :lessonDate="lessonDate" v-on:formSending="getItem"></lesson-complete>

    </div>
</template>

<script>

    export default {

        props: [ 'group' ],

        data() {

            return {

                type: 'default',
                selectedLessonId: '',
                lesson: '',
                lessonDate: {
                    date: '',
                    time: ''
                },
                lessonSaving: false


            }

        },

        components: {

            'lesson-form': require('./FormLesson.vue'),
            'lesson-load': require('./LoadLesson.vue'),
            'lesson-complete': require('./CompleteLesson.vue')

        },
        methods: {

            newLesson() {
                this.$router.push({name: 'lesson_new', params: {group_id: this.group.id}});
            },

            completeLesson() {
                this.lesson = '';
                this.$refs.completeLesson.showModal();

                let lastIndex = this.getLastIndex();

                if(lastIndex >= 0){
                    let lesson = this.group.lessons[this.getLastIndex()];
                    if (lesson){
                        this.lessonDate = this.group.calendar[lesson.dayId +1 ];
                    }else{
                        this.lessonDate = this.group.calendar[0];
                    }

                }

            },

            loadLesson() {
                this.$refs.loadLesson.showModal();
            },

            getItem() {

                this.$emit('getItem');

            },

            getLastIndex() {

                let lastIndex = 0;

                for (let index = this.group.lessons.length -1; index >= 0; index--) {
                    let lesson = this.group.lessons[index];

                    if (!lesson.without_date){
                        lastIndex = index;
                        break;
                    }

                }

                return lastIndex;

            }




        }



    }

</script>
