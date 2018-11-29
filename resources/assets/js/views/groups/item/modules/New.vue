<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div v-if="lesson" class="col-10 offset-2">
            <div class="w-50 m-auto">
                <div class="h4">{{ title }}</div>
                <div class="small text-muted" style="font-weight:500">
                    <span class="mr-3"><span class="fa fa-users mr-1"></span> <router-link :to="{name: 'group', params: {id: group.id}}" href="#">Группа {{ group.id }}</router-link></span>
                    <span><span class="fa fa-signal mr-1"></span>{{ group.level.name }}</span>
                </div>

                <form class="card p-4 mt-4 mb-4">
                    <div class="form-group" v-bind:class="{ 'has-error': errors && errors.title }">
                        <label class="small text-muted" style="font-weight:500" for="field1">тема</label>
                        <input  v-model="lesson.title" id="field1" type="text" class="form-control" placeholder="Укажите тему занятия">
                        <form-error v-if="errors && errors.title" :errors="errors">
                            {{ errors.title[0] }}
                        </form-error>
                    </div>
                    <div class="form-group">
                        <label class="small text-muted" style="font-weight:500" for="field2">дата и время</label>
                        <div class="row">
                            <div class="col-6" v-bind:class="{ 'has-error': errors && errors.date }" style="padding-right: 0">
                                <datepicker :onChange="selectDate" v-model="lesson.date" active="1" class="rounded-left border-right-0" placeholder="укажите дату занятия"></datepicker>
                                <form-error v-if="errors && errors.date" :errors="errors">
                                    {{ errors.date[0] }}
                                </form-error>
                            </div>
                            <div class="col-6" v-bind:class="{ 'has-error': errors && errors.time }" style="padding-left: 0">
                                <timepicker v-model="lesson.time" active="1" class="rounded-right" placeholder="укажите время занятия"></timepicker>
                                <form-error v-if="errors && errors.time" :errors="errors">
                                    {{ errors.time[0] }}
                                </form-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small text-muted" style="font-weight:500" for="field3">продолжительность (в минутах)</label>
                        <input v-model="lesson.duration" class="form-control" type="number" placeholder="60">
                        <form-error v-if="errors && errors.duration" :errors="errors">
                            {{ errors.duration[0] }}
                        </form-error>
                    </div>
                    <div class="form-group" v-bind:class="{ 'has-error': errors && errors.office_cabinet_id }" >
                        <label class="small text-muted" style="font-weight:500" for="field4">аудитория</label>
                        <v-select :on-change="selectOfficeCabinet" label="name" :value="lesson.office_cabinet" :options="group.office.cabinets" placeholder="Выберите аудиторию"></v-select>
                        <form-error v-if="errors && errors.office_cabinet_id" :errors="errors">
                            {{ errors.office_cabinet_id[0] }}
                        </form-error>
                    </div>
                </form>

                <button @click="notFull" class="btn btn-primary">{{ 'Сохранить и вернуться в группу' }}</button>
                <br><button @click="full" class="btn btn-outline-primary mt-2">{{ 'Сохранить и добавить материал' }}</button>
                <br><button @click="cancel" class="btn btn-outline-secondary mt-2">Отменить добавление</button>
            </div>
        </div>
    </div>
</template>



<script>

    import { get, post } from '../../../../helpers/api';

    export default {

        props: ['group_id'],

        data() {
            return {
                errors: [],
                formSending: false,
                lesson: '',
                title: 'Запланировать занятие',
                loading: false,
                group: '',
                lessonDate: ''

            }
        },
        created() {
            this.getItem();
        },
        mounted() {

        },
        watch: {
            group(group) {
                this.lesson = this.newLesson();
            },
            lesson(lesson) {
                if(lesson) {
                    let lastIndex = this.getLastIndex();
                    if(lastIndex >= 0){
                        let lesson = this.group.lessons[this.getLastIndex()];
                        if (lesson){
                            this.lessonDate = this.group.calendar[lesson.dayId +1 ];
                        }else{
                            this.lessonDate = this.group.calendar[0];
                        }
                    }
                    if(this.lessonDate) this.setFormDate(this.lessonDate);

                }
            }
        },

        components: {
            FormError : require('./../../../../components/FormError.vue'),
            Timepicker: require('./../../../../components/Timepicker.vue'),
            Datepicker: require('./../../../../components/Datepicker.vue')
        },
        methods: {
            getItem() {
                let _this = this;
                this.loading = true;
                get(_this,'/api/group/'+_this.group_id, {}, function (response) {
                    _this.loading = false;
                    _this.group = response.data;
                }, function (error) {
                    _this.loading = false;
                });
            },
            sendForm() {
                this.loading = true;
                let _this = this;

                post(_this, '/api/lesson-save', this.lesson, function (response) {
                    _this.loading = false;
                    _this.errors = '';
                    if(_this.lesson.is_full) {
                        _this.$router.push({ name: 'lesson_content', params: { id: response.data.id, group_id: _this.group.id} });
                    } else {
                        _this.$router.push({ name: 'group', params: {id: _this.group_id }});
                    }
                }, function (error) {
                    _this.loading = false;
                    _this.errors = error.response.data;
                });

            },
            selectOfficeCabinet(val) {
                if (val)
                    this.lesson.office_cabinet_id = val.id;
                this.lesson.office_cabinet = val;
            },
            setFormDate(d){
                if (!d) return false;
                this.lesson.date = d.date;
                this.lesson.time = d.time;
            },
            newLesson() {
                return {
                    group_id: this.group.id,
                    office_cabinet_id: this.group.default_office_cabinet_id,
                    office_cabinet: this.group.default_office_cabinet,
                    title: '',
                    duration: ''

                }

            },
            cancel() {
                this.$router.push({name:'group',params:{id:this.group.id}});
            },
            getLastIndex() {

                let lastIndex = 0;
                if(this.group.lessons.length>0) {
                    for (let index = this.group.lessons.length -1; index >= 0; index--) {
                        let lesson = this.group.lessons[index];

                        if (!lesson.without_date){
                            lastIndex = index;
                            break;
                        }

                    }
                }

                return lastIndex;

            },
            selectDate(date) {

                let component = this;

                this.group.calendar.forEach(function (day, index) {
                    if(day.date == date){
                        component.lesson.time = day.time;
                        return false;
                    }
                });

            },
            full() {
                let _this = this;
                this.lesson.is_full = 1;
                this.$nextTick(function() {
                    _this.sendForm();
                });
            },
            notFull() {
                let _this = this;
                this.lesson.is_full = 0;
                this.$nextTick(function() {
                    _this.sendForm();
                });
            }

        }



    }
</script>