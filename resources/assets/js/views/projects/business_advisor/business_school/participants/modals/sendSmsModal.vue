<!-- 
    *Daulet 
    *всплывающее окно для отправки эмэйлов длч выделенных пользователей
-->
<template>

    <div v-if="form">
        <b-modal v-if="data" ref="modal" size="lg" :title='$tc("participants.send_sms_modal.title")'>
                <div class="row">
                    <div class="col-12"> 
                        <textarea class="form-control" :placeholder='$tc("participants.send_sms_modal.sms_body")' name="" id="" cols="30" rows="10"></textarea>
                        <p>{{data}}</p>
                    </div>
                    
                </div> 
        </b-modal>
    </div>


</template>



<script>

    import { post } from '../../../../../../helpers/api'

    export default {

        props: ['data'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                region:'',
                district:'',
                title: 'Подтвердите действие'
            }
        },
        computed: {
            roles() {
                var arr=[];
                if(this.$common.data) {
                    this.$common.data.forEach(function(role) {
                        arr.push(role);
                    });
                }
                return arr;
            }
        },
        created() {
            this.form = this._form ? this._form : this.newParticipant();
            this.step=1;
        }, 
        components: {
            FormError : require('../../../../../../components/FormError.vue')
        },
        methods: {
            sendForm() {
                this.formSending = true;

                let _this = this;


                post(_this, '/api/participant-save', this.form, function () {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.form = _this.form.id ? _this.form : _this.newParticipant();
                    _this.$emit('formSending');


                }, function (error) {

                    _this.formSending = false;
                    _this.errors = error.response.data;

                });


            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            newParticipant() {
                return {
                    new_first_name: '',
                    new_last_name:'',
                    new_patronymic:'',
                    new_gender:'',
                    new_disability:'',
                    new_phone:'',
                    new_iin:'',
                    new_email:'',
                    locality_id:''
                }
            },
            computeBirthDateAndGender(){
                if(!this.form.new_iin) return null;
                else console.log(this.form.new_iin);
                if (this.form.new_iin.length === 12){
                    this.form.new_birth_date =  this.form.new_iin[4]+this.form.new_iin[5]+'.'
                          +this.form.new_iin[2]+this.form.new_iin[3]+'.'
                          +this.form.new_iin[0]+this.form.new_iin[1]
                    let number = this.form.new_iin[6]*1;
                    if(number%2 ===0){
                        this.form.new_gender= "F";
                    }else{
                        this.form.new_gender = "M";
                    }
                }
            },
        }
    }
</script>