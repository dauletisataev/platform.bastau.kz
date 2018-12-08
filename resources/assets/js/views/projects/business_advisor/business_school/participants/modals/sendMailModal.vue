<!-- 
    *Daulet 
    *всплывающее окно для отправки эмэйлов длч выделенных пользователей
-->
<template>

    <div>
        <b-modal v-if="data" ref="modal" size="lg" :title='$tc("participants.send_mail_modal.title")' @ok="onModalSubmit">
                <div class="row">
                    <div class="col-12"> 
                        <select name="templates" class="form-control" >
                            <option value="0">---{{$tc('participants.send_mail_modal.choose_template')}}---</option>
                            <option v-for="(template, index) in templates" :value="template.id" :key="index">
                                {{template.name}}
                            </option>
                        </select>
                        <input class="form-control" type="text" :placeholder='$tc("participants.send_mail_modal.mail_title")'>
                        <textarea class="form-control" :placeholder='$tc("participants.send_mail_modal.mail_body")' name="" id="" cols="30" rows="10"></textarea>
                        <p>{{data}}</p>
                    </div>
                    
                </div> 
        </b-modal>
    </div>


</template>



<script>

    import { post, get } from '../../../../../../helpers/api'

    export default {

        props: ['data'],

        data() {
            return {
                errors: [],  
                region:'',
                district:'',
                title: 'Подтвердите действие',
                templatesLoaded: false,
                templates: [],
                errors: '',
            }
        },
        computed: { 
        },
        created() {  
            //this.step=1;
        }, 
        components: {
            FormError : require('../../../../../../components/FormError.vue')
        },
        methods: { 
            showModal() {
                this.$refs.modal.show();
                if(!this.templatesLoaded) this.initTemplates();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            initTemplates(){
                let _this = this;
                get(_this, '/api/sendpulse/getTemplates', {params:{}}, function (res) {  
                    _this.errors = '';
                    console.log(res);
                    _this.templates = res.data;
                    _this.templatesLoaded = true;
                }, function (error) {  
                    _this.errors = error.response.data; 
                });
            },
            onModalSubmit(){
                let _this = this;
                post(_this, '/api/sendpulse/sendEmail', {params:{}}, function (res) {  
                    _this.errors = '';
                    console.log(res);
                    _this.templates = res.data;
                    _this.templatesLoaded = true;
                }, function (error) {  
                    _this.errors = error.response.data; 
                });
            }
        }
    }
</script>