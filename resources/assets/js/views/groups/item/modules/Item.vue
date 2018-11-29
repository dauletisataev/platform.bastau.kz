<template>
    <div>
        <div class="row" v-show="loading==true">
            <div class="col-10 offset-2 yspinner">
                <clip-loader :size="'70px'" :color="'#0275d8'"></clip-loader>
            </div>
        </div>
        <div class="col-10 offset-2">
            <div v-if="lesson" class="w-50 m-auto">
                <div class="h4">{{ title }}</div>
                <div class="small text-muted" style="font-weight:500">
                    <span class="mr-3"><span class="fa fa-users mr-1"></span> <router-link :to="{name: 'group', params: {id: lesson.group.id}}" href="#">Группа {{ lesson.group.id }}</router-link></span>
                    <span><span class="fa fa-signal mr-1"></span>{{ lesson.level ? lesson.level.name : lesson.group.level.name }}</span>
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
                        <v-select :on-change="selectOfficeCabinet" label="name" :value="lesson.office_cabinet" :options="lesson.group.office.cabinets" placeholder="Выберите аудиторию"></v-select>
                        <form-error v-if="errors && errors.office_cabinet_id" :errors="errors">
                            {{ errors.office_cabinet_id[0] }}
                        </form-error>
                    </div>
                    <div class="form-group" v-if="lesson.id && !lesson.is_clone">
                        <div class="col-12">
                            <label class="custom-control custom-checkbox">
                                <input v-model="moveAll" type="checkbox" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                также перенести последующие занятия
                            </label>
                        </div>
                    </div>
                </form>
                <!--<button @click="full" class="btn btn-primary">{{ 'Сохранить и добавить материал' }}</button>-->
                <!--<br><button @click="notFull" class="btn btn-outline-primary mt-2">{{ 'Сохранить и вернуться в группу' }}</button>-->
                <button @click="sendForm" class="btn btn-primary">{{ 'Сохранить' }}</button>
                <button @click="cancel" class="btn btn-outline-secondary">Отменить добавление</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { post, get } from '../../../../helpers/api';
    export default {
        props: ['id','is_clone','lessonDate','toContent','fromContent'],
        data() {
            return {
                errors: [],
                loading: false,
                lesson: '',
                title: '',
                moveAll: false,
            }
        },
        created() {
            this.getItem();
        },
        watch: {
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
                get(_this,'/api/lesson-with-content/'+_this.id, {}, function (response) {
                    _this.lesson = response.data;
                    if(_this.is_clone == true) {
                        _this.lesson.is_clone = true;
                        if(_this.lessonDate) _this.setFormDate(_this.lessonDate);
                    }
                    _this.title = _this.lesson.is_clone ? 'Дублировать занятие' : 'Редактировать занятие';
                    if(response.data) _this.loading = false;

                });
            },
            cancel() {
                if(this.fromContent) {
                    this.$router.push({ name: 'lesson_content', params: { id: this.lesson.id, group_id: this.lesson.group.id}});
                } else {
                    this.$router.push({name: 'group', params: {id: this.lesson.group.id}});
                }

            },
            sendForm() {
                this.loading = true;
                this.lesson.move_all = this.moveAll;
                let _this = this;
                post(_this, '/api/lesson-save', this.lesson, function (response) {
                    _this.loading = false;
                    _this.errors = '';
                    if(_this.toContent && _this.lesson.is_full) {
                        _this.$router.push({ name: 'lesson_content', params: { id: response.data.id, group_id: _this.lesson.group.id} });
                    } else {
                        _this.$router.push({name: 'group', params: {id: _this.lesson.group.id}});
                    }
                }, function (error) {

                    _this.loading = false;
                    if(error.response) _this.errors = error.response.data;

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
            selectDate(date) {

                let component = this;

                this.lesson.group.calendar.forEach(function (day, index) {
                    if(day.date == date){
                        component.lesson.time = day.time;
                        return false;
                    }
                });

            },
//            full() {
//                let _this = this;
//                this.lesson.is_full = 1;
//                this.$nextTick(function() {
//                    _this.sendForm();
//                });
//            },
//            notFull() {
//                let _this = this;
//                this.lesson.is_full = 0;
//                this.$nextTick(function() {
//                    _this.sendForm();
//                });
//            }

        }
    }
</script>