<template>
    <div class="clearfix">
        <div class="pull-left">
            <b-dropdown size="sm" class="d-inline-block ml-2" v-model="filterData.project_id">
                <div slot="text">
                    <span class="mr-1" style="font-weight:500;color:#CCCCCC">
                        Проект
                    </span>
                    {{ filterData.project_id ? project() : $tc('all_projects') }}
                    <span class="fa fa-angle-down ml-1"></span>
                </div>
                <b-dropdown-item   @click="selectProgram()">{{$tc('all_projects')}}</b-dropdown-item>
                <b-dropdown-item  v-for="project in $common.data.projects" :key="project.id" @click="selectProgram(project)">{{project.name}}</b-dropdown-item>
            </b-dropdown>
            <b-dropdown size="sm" class="d-inline-block ml-2" v-if="$root.user">
                <div slot="text">
                    <span class="mr-1" style="font-weight:500;color:#CCCCCC">
                        тип:
                    </span>
                    {{ type() }}
                    <span class="fa fa-angle-down ml-1"></span>
                </div>
                <b-dropdown-item :disabled="!filterData.type" @click="selectType()">все типы</b-dropdown-item>
                <b-dropdown-item :disabled="filterData.type===type.id" v-for="type in $common.data.lesson_template_types" :key="'types'+type.name" @click="selectType(type)">{{type.name}}</b-dropdown-item>
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
                    project_id: '',
                    type: '',
                },
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
                if(this.filterData.type)
                    this.selected('type', this.$common.data.lesson_template_types, this.filterData.type);
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
                    this.filterData.project_id = val.id;
                } else {
                    this.filterData.project_id = '';
                }
                this.$nextTick(function() {
                    this.$emit('filtered');
                });
            },
            selectType(val) {
                if(val) {
                    this.filterData.type = val.value;
                } else {
                    this.filterData.type = '';
                }
                this.$nextTick(function() {
                    this.$emit('filtered');
                });
            },
            type() {
                let val = 'все типы';
                if(this.filterData.type !==""){
                    this.$common.data.lesson_template_types.map((type)=>{
                        if(type.value===this.filterData.type) val = type.name;
                    })
                }
                return val;
            },
            project(){
                let project_name="все проекты";
                this.$common.data.projects.map((project)=>{
                    if(this.filterData.project_id ===project.id)project_name = project.name;
                })
                return project_name;

            }
        }

    }
</script>