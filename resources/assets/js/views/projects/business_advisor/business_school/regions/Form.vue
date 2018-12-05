<template>
    <b-modal ref="modal" :title="$tc('regions.'+title)">
        <div class="row">
            <label class="col-3 offset-1 col-form-label">{{$tc('regions.name')}}</label>
            <div class="col-7">
            <input  class="form-control"type="text" v-model="name"/>
            </div>
        </div>
        <button class="btn btn-primary" slot="modal-footer" @click="sendForm" :disabled="formSending">{{$tc('regions.create')}}</button>
    </b-modal>
</template>

<script>

    import { get,post } from '../../../../../helpers/api'
    import Input from "vue-strap/src/Input";

    export default {
        components: {Input},
        props: ['data', '_form'],

        data() {
            return {
                title:"",
                type:"",
                parent_id:"",
                region_id:"",
                name:"",
                formSending:false,
                parent_data:"",
                errors:"",
                is_editing:''
            }
        },
        methods:{
            sendForm() {
                let form={};
                form.name=this.name;
                form.type=this.type;
                form.region=this.parent_data.region;
                form.district=this.parent_data.district;
                this.formSending = true;
                let _this = this;

                post(_this, '/api/region/save', form, function () {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('updated');
                }, function (error) {
                    console.log(error)
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            },
            showModal(type,parent){
                this.newForm();
                this.type=type;
                this.parent_data = parent;
                if(type==="district") this.title=this.$tc('add_district');
                if(type==="locality") this.title=this.$tc('add_locality');
                if(type==="region") this.title=this.$tc('add_region');
                this.$refs.modal.show();
            },
            newForm(){
                    this.name=""
            },
            hideModal() {
                this.$refs.modal.hide();
            }
        }
    }
</script>