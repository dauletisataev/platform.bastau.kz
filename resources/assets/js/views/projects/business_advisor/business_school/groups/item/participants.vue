<template>
    <!--Participants of the group-->
    <div class="mt-4 mb-5">
        <div style="width:100%;">
            <button-group class="btn-group btn-group-sm pull-left" v-if="archived">
                <button class="btn btn-secondary"        @click="changeFilterArchived(0)">{{$tc('groups.item.participants.current')}}</button>
                <button class="btn btn-secondary active" @click="changeFilterArchived(1)">{{$tc('groups.item.participants.archived')}}</button>
            </button-group>
        <button-group class="btn-group btn-group-sm pull-left" v-else>
            <button class="btn btn-secondary active" @click="changeFilterArchived(0)">{{$tc('groups.item.participants.current')}}</button>
            <button class="btn btn-secondary"        @click="changeFilterArchived(1)">{{$tc('groups.item.participants.archived')}}</button>
        </button-group>
            <router-link :to="{name:'add-existing-participant-to-group', params:{id:id,members:participants}}">
                <button class="btn btn-primary float-right">{{$tc('groups.item.participants.add_existing_participant')}}</button>
            </router-link>
        </div>
        <br/>
        <br/>
        <div >
            <table class="table mb-0">
                <tr>
                    <th>{{$tc('participants.list.first_name')}}</th>
                    <th>{{$tc('participants.list.last_name')}}</th>
                    <th>{{$tc('participants.list.patronymic')}}</th>
                    <th>{{$tc('participants.list.iin')}}</th>
                    <th></th>
                </tr>
                <tr v-for="participant in participants" v-if="participant.in_archive===archived">
                    <td>{{participant.user.first_name}}</td>
                    <td>{{participant.user.last_name}}</td>
                    <td>{{participant.user.patronymic}}</td>
                    <td>{{participant.user.iin}}</td>
                    <td class="text-left">
                        <span data-toggle="tooltip" title="" data-original-title="Убрать из группы">
                            <button type="button" class="btn btn-outline-danger btn-sm" @click="removeFromGroup(participant.id)">
                                <span class="fa fa-close"></span>
                            </button>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <remove-participant-modal v-on:updated="$emit('updated')" ref="removeModal" :id="toremove" :group_id="id"/>
    </div>
</template>
<script>
    import ButtonGroup from "bootstrap-vue/lib/components/button-group";
    export default {
        components: {
            ButtonGroup,
            "remove-participant-modal":require("../modals/remove_from_group.vue")
        },
        props:['participants','id'],
        data(){
            return {archived:1,toremove:null};
        },
        methods:{
            changeFilterArchived(value){
                this.archived=value
            },
            removeFromGroup(id){
                this.toremove=id;
                this.$refs.removeModal.showModal();
            }
        }
    }
</script>