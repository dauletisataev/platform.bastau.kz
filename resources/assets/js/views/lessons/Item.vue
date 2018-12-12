<template>
    <div class="col-10 offset-2">
        <div class="bg-primary text-white rounded p-4 mb-4">
            <div class="small text-info mb-2">
                <span class="fa fa-list-ol mr-1"></span>{{$tc('lessons.item.lesson_order')+lesson.order}}
                <span class="fa fa-calendar-o mr-1"></span>{{$tc('lessons.item.start_time')+parseToDate(lesson.datetime)+" "+parseToTime(lesson.datetime)}}
                <span class="fa fa-calendar-o mr-1"></span>{{$tc('lessons.item.duration') +lesson.duration +$tc('lessons.item.duration')}}
            </div>
            <div class="h4 mt-4 mb-4">{{lesson.title}}</div>
            <div class=" mb-2"> {{$tc('lessons.item.status')}}
                <template v-if="lesson.passed"> {{$tc('lessons.item.status.passed')}}</template>
                <template v-else-if="!lesson.is_started"> {{$tc('lessons.item.status.planned')}} </template>
                <template v-else-if="lesson.is_started"> {{$tc('lessons.item.status.started')}} </template>
            </div>
        </div>
        <div class="row btn-group">

            <div class="btn btn-secondary" @click="changeTab('materials')">{{$tc('lessons.item.materials')}}</div>
            <div class="btn btn-secondary" @click="changeTab('methodics')">{{$tc('lessons.item.methodics')}}</div>
            <div class="btn btn-secondary" @click="changeTab('participants')">{{$tc('lessons.item.participants')}}</div>
            <div v-if="lesson.passed">Урок закончен</div>
            <div v-else-if="!lesson.is_started" @click="change_to_next_state_lesson('start') "class="btn btn-danger">начать урок</div>
            <div v-else-if="lesson.is_started" @click="change_to_next_state_lesson('end')    "class="btn btn-danger">закончить урок</div>
        </div>
        <div>
            <materials v-if="active_tab==='materials'" :lesson="lesson"  :students="lesson.group.participants" />
            <manual v-if="active_tab=='methodics'" :manual="lesson.manual"></manual>
            <div v-if="active_tab==='participants'">
                <div class="row">
                    <div class="col-2 offset-1">First name</div>
                    <div class="col-2">Last Name</div>
                    <div class="col-2 offset-2">attendance</div>
                    <div class="col-2 ">grade</div>
                </div>
                <div v-for="participant in lesson.group.participants" class="row">
                    <div class="col-2 offset-1">{{participant.user.first_name}}</div>
                    <div class="col-2">{{participant.user.last_name}}</div>
                    <div class="col-2 offset-2">
                        <div v-for="participant_attendance in attendance"
                             v-if="participant_attendance.participant_id===participant.id">
                            <div class="btn-group"   >
                                <label class="btn btn-success">
                                    <input type="radio" :value="1" @change="change_user_attendance(participant_attendance)" v-model="participant_attendance.presented"/>
                                </label>
                                <label class="btn btn-danger">
                                    <input type="radio" :value="0" @change="change_user_attendance(participant_attendance)" v-model="participant_attendance.presented"/>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">grade</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { get, post, del } from '../../helpers/api';
    export default{
        props:['id'],
        data(){
            return{
                loading:true,
                lesson:"",
                attendance:"",
                active_tab:"",
                edit:false,
                errors:[]
            }

        },
        components:{
            'materials': require('./Materials.vue'),
            'manual': require('../../components/lms/Manual.vue'),
            'manual-edit': require('../../components/lms/Edit/Manual.vue'),
            'materials-edit': require('../../components/lms/Edit/Materials.vue'),
        },
        methods:{
            getItem(){
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson/' + _this.id, {}, function (response) {
                    console.log(response);
                    _this.lesson = response.data;
                    if(response.data) _this.loading = false;
                    _this.createArray();
                    get(_this,"/api/lesson/attendance/"+_this.lesson.id,{},function (resp) {
                        console.log(">>",resp);
                        _this.attendance=resp.data;
                    })
                });
            },
            saveItem(){
                this.loading = true;
                let _this = this;
                post(_this, '/api/lesson-save' , _this.lesson, function (response) {
                    _this.lesson = response.data;
                    if(response.data) _this.loading = false;
                    _this.createArray();
                },function (err) {
                    console.log("ERRR",err);
                });
            },
            cancel(){},
            changeTab(element){
                this.active_tab = element;
                this.$router.push({ path: '/lesson/'+this.id, query: {page:element} });
            },
            change_to_next_state_lesson(value){
                this.loading=true;
                let _this=this;
                if(value==="start"){
                    _this.lesson.is_started=true;
                }
                if(value ==="end") _this.lesson.passed=true;
                post(_this, '/api/lesson-save', _this.lesson, function (response) {
                    console.log(response);
                    _this.lesson = response.data;
                    if(response.data) _this.loading = false;
                    _this.createArray();
                });
            },
            createArray() {
                let _this = this;
                if(_this.lesson.pages.length>0) {
                    _this.lesson.pages.forEach(function(page) {
                        if(page.materials.length>0) {
                            page.materials.forEach(function (material) {
                                _this.$set(material,'showAdditional',false);
                                _this.$set(material,'upload',0);
                                if(material.task_group && material.task_group.tasks && material.task_group.tasks.length > 0) {
                                    material.task_group.tasks.forEach(function (task) {
                                        _this.$set(task,'uploadAudio',0);
                                        _this.$set(task,'uploadImage',0);
                                    });
                                }
                            });
                        }
                    });
                }
                if(_this.lesson.tests){
                    if(_this.lesson.tests.length>0) {
                        _this.lesson.tests.forEach(function (test) {
                            test.task_groups.forEach(function (task_group) {
                                task_group.tasks.forEach(function (task) {
                                    _this.$set(task,'uploadAudio',0);
                                    _this.$set(task,'uploadImage',0);
                                });
                            });
                        });
                    }
                }

            },
            change_user_attendance(attendance){
                this.loading = true;
                let _this = this;
                console.log("me here");
                post(_this, '/api/lesson/attendance/save' , attendance, function (response) {
                    console.log(response.data);
                    _this.attendance.map(element=>{
                        if(element.id===attendance.id){
                            element=response.data;
                        }
                    });
                },function (err) {
                    console.log("ERRR",err);
                });
            },
            parseToDate(datetime){
                let date=datetime[8]+datetime[9]+'.'+datetime[5]+datetime[6]+'.'+datetime[2]+datetime[3];
                return date;
            },
            parseToTime(datetime){
                let time = datetime[11]+datetime[12]+datetime[13]+datetime[14]+datetime[15];
                return time;
            }
        },
        created(){
            if(this.$route.query.page){
                this.active_tab = this.$route.query.page;
            }else{
                this.changeTab("materials");
            }

            this.getItem();
        }
    }
/*
    import { get, post, del } from '../../helpers/api';
    import wysiwyg from "vue-wysiwyg";
    import moment from 'moment';
    export default {

        props: ['id'],

        data () {
            return {
                loading: true,
                lesson: '',
                activeTab: 'materials',
                edit: false,
                errors: [],
                selectedTestIndex: '',
            }
        },
        watch: {
            activeTab(value) {
                if(value=='manual-edit' || value=='materials-edit' || value=='tests-new' || value=='tests-edit' || value=='homework-edit') {
                    this.edit=true;
                } else {
                    this.edit=false;
                }

            }
        },
        components: {
            'materials': require('./Materials.vue'),
            'manual': require('../../components/lms/Manual.vue'),
            'manual-edit': require('../../components/lms/Edit/Manual.vue'),
            'materials-edit': require('../../components/lms/Edit/Materials.vue'),
            'mark-lesson': require('./Mark.vue'),
            'tests': require('./Tests/List.vue'),
            'tests-new': require('../../components/lms/Tests/New.vue'),
            'tests-item': require('./Tests/Item.vue'),
            'tests-edit': require('../../components/lms/Tests/Edit.vue'),
            'homework': require('./Homework/Item.vue'),
            'homework-edit': require('../../components/lms/Homework/Edit.vue'),
            'unmarked-tasks': require('./../groups/modals/Unmarked.vue'),
            'about': require('./About.vue'),
            'task_group': require('./TaskGroup/Item.vue'),
        },
        methods: {
            getItem() {
                this.loading = true;
                let _this = this;
                get(_this, '/api/lesson-content/' + _this.id, {}, function (response) {
                    _this.lesson = response.data;
                    if(response.data) _this.loading = false;
                    _this.createArray();
                });
            },
            cancel() {
                this.getItem();
                this.toViewTab();
            },
            save() {
                let _this = this;
                this.loading = true;
                post(_this, '/api/lesson-content-save/' + this.lesson.id, this.lesson, function (response) {
                    _this.errors = '';
                    _this.getItem();
                    _this.toViewTab();
                    _this.loading=false;
                }, function (error) {
                    _this.loading=false;
                    _this.errors = error.response.data;
                });
            },
            createArray() {
                let _this = this;
                if(_this.lesson.pages.length>0) {
                    _this.lesson.pages.forEach(function(page) {
                        if(page.materials.length>0) {
                            page.materials.forEach(function (material) {
                                _this.$set(material,'showAdditional',false);
                                _this.$set(material,'upload',0);
                                if(material.task_group && material.task_group.tasks && material.task_group.tasks.length > 0) {
                                    material.task_group.tasks.forEach(function (task) {
                                        _this.$set(task,'uploadAudio',0);
                                        _this.$set(task,'uploadImage',0);
                                    });
                                }
                            });
                        }
                    });
                }

                if(_this.lesson.homework.length>0) {
                    _this.lesson.homework.forEach(function (task_group) {
                        task_group.tasks.forEach(function (task) {
                            _this.$set(task,'uploadAudio',0);
                            _this.$set(task,'uploadImage',0);
                        });
                    });
                }
                if(_this.lesson.tests.length>0) {
                    _this.lesson.tests.forEach(function (test) {
                        test.task_groups.forEach(function (task_group) {
                            task_group.tasks.forEach(function (task) {
                                _this.$set(task,'uploadAudio',0);
                                _this.$set(task,'uploadImage',0);
                            });
                        });
                    });
                }
            },
            toViewTab() {
                switch(this.activeTab) {
                    case 'manual-edit':
                        this.activeTab = 'manual';
                        break;
                    case 'materials-edit':
                        this.activeTab = 'materials';
                        break;
                    case 'tests-new':
                        this.activeTab = 'tests';
                        break;
                    case 'tests-edit':
                        this.activeTab = 'tests';
                        break;
                    case 'homework-edit':
                        this.activeTab = 'homework';
                        break;
                }
            },
            deleteLesson() {
                this.$refs.modalDeleteLesson.show();
            },
            removeLesson() {
                let _this = this;
                del(_this, '/api/lesson-delete/'+this.lesson.id, {}, function (response) {
                    _this.$refs.modalDeleteLesson.hide();
                    _this.$router.push({name:'group',params:{id:_this.lesson.group.id}});
                });
            },
            newTest() {
                this.lesson.tests.push({name: '', duration: '', access: 0, task_groups: []});
                this.activeTab = 'tests-new';
            },
            testItem(id) {
                let _this = this;
                this.lesson.tests.forEach(function(test,index) {
                   if(id == test.id) _this.selectedTestIndex = index;
                });
                this.activeTab = 'tests-item';
            },
            testEdit(id) {
                let _this = this;
                this.lesson.tests.forEach(function(test,index) {
                    if(id == test.id) _this.selectedTestIndex = index;
                });
                this.activeTab = 'tests-edit';
            },
            markPossible(value) {
                var lesson_datetime = moment(value.datetime, "YYYY-MM-DD HH:mm:ss").set({
                    'hour': 0,
                    'minute': 0,
                    'second': 0
                });
                return lesson_datetime <= this.now;
            },
            startLesson() {
                let _this = this;
                post(_this,'/api/lesson-start/'+this.lesson.id, {}, function (response) {
                    _this.getItem();
                });
            },
            unmarked() {
                this.$refs.unmarked.showModal();
            },
            toGroup() {
                this.$router.push({name:'group',params:{id: this.lesson.group.id}});
            },
            task(task_group) {
                this.selectedTaskGroup = task_group;
                this.activeTab = 'task_group';
            },
            taskStudent(obj) {
                this.selectedTaskGroup = obj.task_group;
                this.activeTab = 'task_group';
                this.$nextTick(function() {
                    if(this.$refs.taskGroup) {
                        this.$refs.taskGroup.activeTab = 'student';
                        this.$refs.taskGroup.selectedId = obj.student_id;
                        this.$refs.taskGroup.selectedName = obj.name;
                    }
                });
            }
        },

        created() {

            this.getItem();
        }
    }
    */
</script>
