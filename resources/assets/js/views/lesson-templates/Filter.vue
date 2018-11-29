<template>
    <div class="clearfix">
        <div class="pull-left">
            <b-dropdown size="sm" class="d-inline-block ml-2" v-if="$root.user">
                <div slot="text">
                    <span class="mr-1" style="font-weight:500;color:#CCCCCC">
                        тип:
                    </span>
                    {{ type() }}
                    <span class="fa fa-angle-down ml-1"></span>
                </div>
                <b-dropdown-item :disabled="!filterData.type" @click="selectType()">все типы</b-dropdown-item>
                <b-dropdown-item :disabled="filterData.type == 'online'" @click="selectType('online')">онлайн</b-dropdown-item>
                <b-dropdown-item :disabled="filterData.type == 'teacher'" @click="selectType('teacher')">с преподавателем</b-dropdown-item>
            </b-dropdown>
            <b-dropdown size="sm" class="d-inline-block ml-2">
                <div slot="text">
                    <span class="mr-1" style="font-weight:500;color:#CCCCCC">
                        услуга:
                    </span>
                    {{ temp.program ? temp.program.name : 'все услуги' }}
                    <span class="fa fa-angle-down ml-1"></span>
                </div>
                <b-dropdown-item :disabled="!filterData.program_id" @click="selectProgram()">все услуги</b-dropdown-item>
                <b-dropdown-item :disabled="filterData.program_id==program.id" v-for="program in $common.data.programs" :key="program.id" @click="selectProgram(program)">{{ program.name }}</b-dropdown-item>
            </b-dropdown>
            <b-dropdown size="sm" class="d-inline-block ml-2">
                <div slot="text">
                    <span class="mr-1" style="font-weight:500;color:#CCCCCC">
                        уровень:
                    </span>
                    {{ temp.level ? temp.level.name : 'все уровни' }}
                    <span class="fa fa-angle-down ml-1"></span>
                </div>
                <b-dropdown-item :disabled="!filterData.level_id" @click="selectLevel()">все уровни</b-dropdown-item>
                <b-dropdown-item :disabled="filterData.level_id==level.id" v-for="level in $common.data.levels" :key="level.id" @click="selectLevel(level)">{{ level.name }}</b-dropdown-item>
            </b-dropdown>
        </div>
        <div class="pull-right" v-if="$route.name != 'student_lesson_templates'" >
            <router-link v-if="$root.user" :to="{ name: 'lesson_template_new'}" tag="button" class="btn btn-primary btn-sm">добавить шаблон</router-link>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                filterData: {
                    program_id: '',
                    level_id: '',
                    type: '',
                },
                temp: {
                    program: '',
                    level: '',
                }
            }
        },
        mounted() {
            this.$nextTick(function () {
                this.setFiltered(this.$route.query);
            });
        },
        methods: {
            setFiltered(query){
                for (let filterKey in this.filterData) {
                    for(let queryKey in query){
                        if(filterKey == queryKey) this.filterData[filterKey] = query[queryKey];
                    }
                }
                this.$nextTick(function () {
                    this.setSelect();
                });
            },
            setSelect()
            {
                if(this.filterData.program_id)
                    this.selected('program', this.$common.data.programs, this.filterData.program_id);
                if(this.filterData.level_id)
                    this.selected('level', this.$common.data.levels, this.filterData.level_id);
                this.$nextTick(function(){
                    this.clearListLoad();
                })
            },

            selected(key, options, id)
            {
                let comp = this;
                options.forEach(function (value) {
                    if(value.id == id) comp.temp[key] = value;
                });
            },

            clearListLoad(){
                this.$nextTick(function () {
                    this.$emit('filtered');
                });
            },

            selectProgram(val) {
                if(val) {
                    this.temp.program = val;
                    this.filterData.program_id = val.id;
                } else {
                    this.temp.program = '';
                    this.filterData.program_id = '';
                }
                this.$nextTick(function() {
                    this.$emit('filtered');
                });
            },
            selectLevel(val) {
                if(val) {
                    this.temp.level = val;
                    this.filterData.level_id = val.id;
                } else {
                    this.temp.level = '';
                    this.filterData.level_id = '';
                }
                this.$nextTick(function() {
                    this.$emit('filtered');
                });
            },
            selectType(val) {
                if(val) {
                    this.filterData.type = val;
                } else {
                    this.filterData.type = '';
                }
                this.$nextTick(function() {
                    this.$emit('filtered');
                });
            },
            type() {
                let val = 'все типы';
                if(this.filterData.type === 'online') val = 'онлайн';
                if(this.filterData.type === 'teacher') val = 'с преподавателем';
                return val;
            }
        }

    }
</script>