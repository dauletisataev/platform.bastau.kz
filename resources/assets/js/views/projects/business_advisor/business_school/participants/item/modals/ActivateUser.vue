<template>
    <b-modal ref="modal" :title="header">
        {{$tc('participants.modals.activate_user_warning')}}
        <button  class="btn btn-primary" slot="modal-footer" @click="activateUser">OK</button>
    </b-modal>
</template>
<script>
    import {  post } from '../../../../../../../helpers/api';
    export default {
        props : ['user_id','user_type','header'],
        data(){
            return {
                items:'',
                selected:"",
            }
        },
        methods:{
            showModal(){
                this.$refs.modal.show();
            },
            hideModal(){
                this.$refs.modal.hide();
            },
            activateUser(){
                let _this=this;
                post(_this, '/api/'+this.user_type+'-archive/'+this.user_id,{}, function (response) {
                    _this.$parent.getItem();
                    _this.$parent.getHistory();
                    _this.$refs.modal.hide();
                }, function () {

                });
            }

        }

    }
</script>