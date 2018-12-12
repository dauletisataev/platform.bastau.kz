<template>
    <b-modal ref="modal" :title="$tc('groups.item.lessons_tab.import_lesson_modal_header')" size="lg">
        <div class="form-group" >
            <label class="label">{{$tc('groups.item.lessons_tab.select_lesson_template')}}</label>
            <select v-if="lesson_templates.length!==0" v-model="selected_template" class="form-control  ">
                <option v-for="template in lesson_templates" v-if="template.name" :value="template">{{template.name}}</option>
            </select>
            <small>
                {{$tc('groups.item.lessons_tab.templates_that_shown_part1')}}
                {{language === "ru" ? $tc('groups.item.lessons_tab.ru'):$tc("groups.item.lessons_tab.kz")}}
                {{$tc('groups.item.lessons_tab.templates_that_shown_part2')}}
                {{$tc('groups.item.lessons_tab.templates_that_shown_part3')}}
                {{projectName()}}.
                {{online?$tc("groups.item.lessons_tab.for_online_groups"): $tc("groups.item.lessons_tab.for_offline_groups")}}
            </small>
            <div class="form-control">
                <small>
                    {{$tc('groups.item.lesson_tab.arrange_time_description')}}
                </small>
                <button @click="arrangeTime">
                    {{$tc('groups.item.lesson_tab.arrange_time_button')}}
                </button>
            </div>
        </div>
        <div class="card" v-if="selected_template" style="max-height: 300px;overflow-y:scroll">
            <table class="table mb-0 table-sm">
                <thead class="thead-default h6 mb-0">
                    <th></th>
                    <th>{{$tc("groups.item.lessons_tab.title")}}
                    </th>
                    <th></th>
                    <th>{{$tc("groups.item.lessons_tab.date")}}</th>
                    <th>{{$tc("groups.item.lessons_tab.time")}}</th>
                </thead>
                <tr v-for="(item,index) in selected_template.items">
                    <input type="checkbox" v-model="selected_templates" :id="index" :value="index" />
                    <td colspan="2">
                        <span data-toggle="tooltip"
                              :data-title="item.title"
                              style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;display:block;max-width:500px">
                              {{item.title}}
                        </span>
                    </td>
                    <td>
                        <input-date :_value="item.date"/>
                    </td>
                    <td>
                        <input  class="form-control"v-model="item.time"/>
                    </td>
                </tr>
            </table>
        </div>
        <button @click="importLessons" slot="modal-footer"> button</button>
    </b-modal>
</template>
<script>
    import { get,post } from '../../../../../../helpers/api'
    export default {
        props:["language","online","project_id","pseudotimes","group_id","lessons"],
        data(){
            return {
                next_url: '/api/lesson-templates',
                lesson_templates:[],
                selected_templates:[],
                selected_template:"",
                times:""
            }
        },
        components:{
          "input-date":require("../../../../../../components/bootstrap-editable/Date.vue")
        },
        methods:{
            getListOfTemplates() {
                let _this = this;
                let filterData={
                    language:this.language,
                    type:'',
                    project_id:this.project_id
                }
                this.$common.data.lesson_template_types.map(item=>{
                    if(this.online && item.name==="online")filterData.type=item.value;
                    if(!this.online && item.name==="teacher")filterData.type=item.value;
                });
                get(_this, this.next_url, {params: filterData}, function (response) {
                    _this.lesson_templates = response.data.templates
                    console.log(this.lesson_templates);
                }, function () {
                });
            },
            arrangeTime(){
                let _selected_template = this.selected_template;
                this.selected_templates.map(index=>{
                    for(let i=0;i<this.times.length;i++){
                        if (this.times[i].available){
                            _selected_template.items[index].time=this.times[i].time;
                            _selected_template.items[index].date=this.times[i].date;
                            this.times[i].available=false;
                            break;
                        }
                    }
                })
            },
            showModal(){
                if(this.lessons.length===0){
                    this.times = this.pseudotimes.map(el=>{
                        return{
                            date:el.date,
                            time:"9:00",
                            available:true
                        }
                    })
                }
                this.lessons.map(date=>{
                   let parsedDate=date.datetime[8]+date.datetime[9]+'-'+date.datetime[5]+date.datetime[6]+'-'+date.datetime[2]+date.datetime[3]
                   this.pseudotimes.map(date2=>{
                        if(parsedDate==date2.date){
                            date2.available = false
                        }
                   });
                    this.times =this.pseudotimes.map(element=>{
                        return {
                            date:element.date,
                            time:"9:00",
                            available:element.available
                        };
                    });
                });
                this.getListOfTemplates();
                this.$refs.modal.show();
            },
            hideModal(){
                this.$refs.modal.hide();
            },
            importLessons(){
                let _this=this;
                let final= [];
                this.selected_templates.map(index=>{
                    final.push(this.selected_template.items[index]);
                });
                let _form = {data:final};
                post(_this, '/api/lessons/import-from-templates/'+this.group_id,_form , function () {
                    _this.hideModal();
                    _this.$emit('updated');
                }, function (error) {

                });
            },
            projectName(){
                var x = "";
                this.$common.data.projects.map(elem=>{
                    if (elem.id===this.project_id)x=elem.name}
                )
                return x;
            }
        },

    }
</script>