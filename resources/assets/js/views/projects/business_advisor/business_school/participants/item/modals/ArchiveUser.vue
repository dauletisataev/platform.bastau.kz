<template>
    <b-modal ref="modal" :title="header">
        Выберите причину архивации
        <select class="form-control" v-model="selected" >
            <option v-for="item in items" :value="item.id">{{item.reason}}</option>
        </select>
        <button slot="modal-footer" @click="archiveUser">OK</button>
    </b-modal>
</template>
<script>
    import {  get,post } from '../../../../../../../helpers/api';
    export default {
        props : ['user_id','user_type','header'],
        data(){
            return {
                items:'',
                selected:1,
            }
        },
        mounted: function () {
            let _this=this;
            get(_this, '/api/'+this.user_type+'-archive/reasons', {}, function (response) {
                _this.items = response.data.data;
            }, function () {
            });
        },
        methods:{
            showModal(){
                this.$refs.modal.show();
            },
            hideModal(){
                this.$refs.modal.hide();
            },
            archiveUser(){
                let data = new FormData();
                data.append("archive_reason_id",this.selected);
                let _this=this;
                let url = '/api/'+this.user_type+'-archive/';
                post(_this, url+this.user_id, data, function (response) {
                    _this.$parent.getItem();
                    _this.$refs.modal.hide();
                    _this.$parent.getHistory();
                }, function (err) {
                });
            }

        }

    }
</script>