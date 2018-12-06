<template>
    <!--There is a general information of the group-->
    <div>
        <div class="row pt-4">
            <div class="col-3">
            <button class="btn btn-primary " @click="changeAdvisor">{{$tc('groups.item.info.change_advisor')}}</button>
            </div>
            <div class="col-3">
            <button class="btn btn-primary" @click="edit">{{$tc('groups.item.info.edit')}}</button>
            </div>
            <div class="col-3">
            <button class="btn btn-danger" @click="archiveGroup">{{group.in_archive?$tc('groups.item.info.restore'):$tc('groups.item.info.archive')}}</button>
            </div>
            <div class="col-3">
            <button class="btn btn-danger" @click="deleteGroup">{{$tc('groups.item.info.delete_group')}}</button>
            </div>
        </div>
        <div class="col-10 mt-4">
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.id')}}</span></div>
                <div class="col-6">
                    {{group.id}}
                </div>
            </div>
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.created_date')}}</span></div>
                <div class="col-6">
                    {{group.created_at}}
                </div>
            </div>
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.group_lesson_types')}}</span></div>
                <div class="col-6">
                    {{group.online?$tc("groups.item.info.online"):$tc("groups.item.info.offline")}}
                </div>
            </div>
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.in_archive')}}</span></div>
                <div class="col-6">
                    {{group.in_archive? $tc("groups.item.info.archived"):$tc("groups.item.info.active")}}
                </div>
            </div>
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.advisor')}}</span></div>
                <div class="col-6">
                    {{group.trainer_id}}
                </div>
            </div>
            <div class="row">
                <div class="col-6"><span>{{$tc('groups.item.info.project')}}</span></div>
                <div class="col-6">
                    {{group.project_id}}
                </div>
            </div>
            <div class="row" v-if="group">
                <div class="col-6"><span>{{$tc('regions.locality')}}</span></div>
                <div class="col-6">
                    {{group.locality.name}}
                </div>
            </div>
            <div class="row" v-if="group.locality.district">
                <div class="col-6"><span>{{$tc('regions.region')}}</span></div>
                <div class="col-6">
                    {{group.locality.district.region.name}}
                </div>
            </div>
            <div class="row" v-if="group.locality">
                <div class="col-6"><span>{{$tc('regions.district')}}</span></div>
                <div class="col-6">
                    {{group.locality.district.name}}
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    import { del, get,post } from '../../../../../../helpers/api';
    import ButtonGroup from "bootstrap-vue/lib/components/button-group";
    export default {

        props: ['group'],

        data() {
            return {
                errors: [],
                file:'',
            }
        },

        components: {
            ButtonGroup,
            "inline-editing":require('../../../../../../components/bootstrap-editable/Input.vue'),
            "phone-editing":require('../../../../../../components/bootstrap-editable/InputPhone.vue'),
            "select-editing":require('../../../../../../components/bootstrap-editable/Select.vue')
        },

        methods: {
            deleteGroup(){
                console.log("going to delete",this.group.id);
            },
            archiveGroup(){
                let _this = this;
                post(_this, '/api/group-archive/'+this.group.id,{}, function () {
                    _this.$emit('updated');
                }, function (error) {
                    this.$emit('updated');
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });

            },
            changeAdvisor(){
                console.log("going to chagne advisor of group",this.group.id);
            },
            edit(){
                console.log("going to edit group",this.group.id);
            },
        },
        computed: {
            getGroup() {
                return this.group;
            }
        }
    }


</script>