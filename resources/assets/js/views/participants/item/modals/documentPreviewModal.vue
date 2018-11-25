<template>
    <div>
        <b-modal ref="modal" v-if="type==='IC'" title="Просмотр Удостоверния личности">
            <img :src = "src"/>
            <button slot="modal-footer" @click="removeFile('identity_card')" class="btn btn-danger btn-block"> Удалить</button>
        </b-modal>
        <b-modal v-else ref="modal" title="Просмотр Адресной справки">
            <img :src = "src"/>
            <button slot="modal-footer"class="btn btn-danger btn-block"> Удалить</button>
        </b-modal>
    </div>
</template>
<script>
    import { post } from '../../../../helpers/api';

    export default {
     props:['src','participant_id'],
     data(){
         return {
            type:''
         }
     },
     methods:{
         openModal(type){
             this.type=type
             this.$refs.modal.show();
         },
         removeFile(name){
             let _this=this;
             let data=new FormData();
             data.append("type",name);
             post(_this,'/api/participant-document-remove/' + this.participant_id, data, function(response) {
                 _this.$emit('updated');
             }, function(error){
             });
             this.$refs.modal.hide();

         },
     }
 }
</script>