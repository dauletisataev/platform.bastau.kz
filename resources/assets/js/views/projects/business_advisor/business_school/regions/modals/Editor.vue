<template>
    <b-modal ref="modal" :title="title" >
        <div class="form-group row">
        <label class="col-3 col-form-label"> {{$tc('regions.name')}} </label>
        <div class="col-8">
            <input type="text" v-model="item.name" class="form-control"/>
        </div>
        </div>
        <button  class="btn btn-primary"slot="modal-footer" @click="sendForm"> {{$tc('regions.update')}}</button>
    </b-modal>
</template>
<script>
    import { post } from '../../../../../../helpers/api';

    export default {
        data(){
            return {
                item:"",
                type:"",
                formSending:false,
                title:""
            }
        },
        methods:{
            showModal(type,item){
                this.item=item;
                this.type=type;
                if(type==="locality")this.title=this.$tc('regions.locality_editor_header');
                if(type==="district")this.title=this.$tc('regions.district_editor_header');
                if(type==="region")this.title=this.$tc('regions.region_editor_header');
                this.$refs.modal.show();
            },
            hideModal(){
                this.$refs.modal.hide();
            },
            sendForm(){
                let _this=this;
                let form={};
                form.type=this.type;
                form.item=this.item;
                console.log(form);
                post(_this, '/api/region/update', form, function () {
                    _this.$common.getData();
                    _this.$refs.modal.hide();
                }, function (error) {
                    _this.$common.getData();
                    _this.$refs.modal.hide();
                });
            }

        }
    }
</script>