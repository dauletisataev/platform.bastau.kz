<template>
    <div>
        <!--Yersultan-->
        <div class="row mt-4 mb-4">
            <button class="btn btn-primary" @click="$emit('show_import_lessons_modal')">{{$tc('groups.item.lessons_tab.import_lesson_template')}}</button>
        </div>
        <div class="h4">{{$tc('groups.item.lessons_tab.group_lessons')}}</div>
        <!-- starting of the list of lessons -->
        <div class="btn btn-group">
            <div v-if="selected==='planned'"      class="btn btn-primary" @click="changeLessonsType('planned')"  >Запланированные</div>
            <div v-else-if="selected==='past'"    class="btn btn-link"    @click="changeLessonsType('planned')"  >Запланированные</div>
            <div v-if="selected==='planned'"      class="btn btn-link"    @click="changeLessonsType('past')"  >Завершенные</div>
            <div v-else-if="selected==='past'"    class="btn btn-primary" @click="changeLessonsType('past')"  >Завершенные</div>
        </div>
        <div class="row">
            <div class="col">id</div>
            <div class="col">title</div>
            <div class="col">passed</div>
            <div class="col">date</div>
            <div class="col">duration</div>
            <div class="col">manual </div>
            <div class="col"> is started</div>
            <div class="col"></div>
        </div>
        <div v-for="lesson in lessons" v-if="(selected==='planned' && !lesson.passed)||(selected==='past' && lesson.passed)" class="row">
            <div class="col">{{lesson.id}}</div>
            <div class="col">{{lesson.title}}</div>
            <div class="col">{{lesson.passed?"+":"-"}}</div>
            <div class="col">{{!lesson.without_date ? lesson.datetime:"-"}}</div>
            <div class="col">{{lesson.duration}}</div>
            <div class="col">{{lesson.mangotouch}} </div>
            <div class="col">{{lesson.is_started?"+":"-"}}</div>
            <div class="col">
                <router-link :to="{name:'lesson', params:{id: lesson.id}}">
                    <span class="fa fa-play"></span>
                </router-link>

                <span class="fa fa-eye"></span>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props:['lessons'],
        data(){
            return{
                selected:"planned"
            }
        },
        components:{

        },
        methods:{
            changeLessonsType(props){
                this.selected=props;
            }
        },
    }
</script>