<template>
    <b-modal ref="modal" :title="$tc('groups.modals.remove_participant_header')">
        {{$tc('groups.modals.remove_participant_body')}}
        <div slot="modal-footer">
            <button class="btn btn-secondary" @click="hideModal">{{$tc('groups.modals.remove_participant_cancel')}}</button>
            <button class="btn btn-danger" @click="removeParticipant">{{$tc('groups.modals.remove_participant_ok')}}</button>
        </div>
    </b-modal>
</template>
<script>
    import { post } from '../../../../../../helpers/api';
    export default{
        props:['id','group_id'],
        methods:{
            showModal(){
                this.$refs.modal.show();
            },
            hideModal(){
                this.$refs.modal.hide();
            },
            removeParticipant(){
                console.log("removing participant ",this.id, "from group",this.group_id);
                let _this=this;
                let form = {
                    participant_id:this.id,
                }
                post(_this, '/api/group/'+this.group_id+'/remove-participant', form, function () {
                    _this.formSending = false;
                    _this.hideModal();
                    _this.$emit('formSending');
                    _this.$emit('updated');
                }, function (error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            }
        }
    }
</script>