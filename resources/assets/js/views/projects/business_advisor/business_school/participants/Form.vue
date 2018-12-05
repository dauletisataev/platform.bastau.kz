<template>

    <div v-if="form">
        <b-modal v-if="data" ref="modal" size="lg" :title="title">
                <div class="row">
                    <div v-bind:class="{ 'has-error': errors && errors.new_first_name }" class="form-group col-4">
                            <input v-model="form.new_first_name" class="form-control" :placeholder="$tc('participants.form.enter_first_name')"/>
                            <form-error v-if="errors && errors.new_first_name" :errors="errors">
                                {{ errors.new_first_name[0] }}
                            </form-error>
                    </div>

                    <div v-bind:class="{ 'has-error': errors && errors.new_last_name }" class="form-group col-4">
                            <input v-model="form.new_last_name" class="form-control" :placeholder="$tc('participants.form.enter_last_name')"/>
                            <form-error v-if="errors && errors.new_last_name" :errors="errors">
                                {{ errors.new_last_name[0] }}
                            </form-error>
                    </div>

                    <div v-bind:class="{ 'has-error': errors && errors.new_patronymic }" class="form-group col-4">
                            <input v-model="form.new_patronymic" class="form-control" :placeholder="$tc('participants.form.enter_patronymic')"/>
                            <form-error v-if="errors && errors.new_patronymic" :errors="errors">
                                {{ errors.new_patronymic[0] }}
                            </form-error>
                    </div>
                </div>
                <div v-bind:class="{ 'has-error': errors && errors.new_iin }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.list.iin')}}</label>
                    <div class="col-9">
                        <input v-model="form.new_iin" class="form-control" :placeholder="$tc('participants.form.iin')" @change="computeBirthDateAndGender()"/>
                        <form-error v-if="errors && errors.new_iin" :errors="errors">
                            {{ errors.new_iin[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_gender }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.list.gender')}}</label>
                    <div class="col-9">
                        <input v-model="form.new_gender" :placeholder="$tc('participants.form.enter_correct_iin')" class="form-control" disabled/>
                        <form-error v-if="errors && errors.new_gender" :errors="errors">
                            {{ errors.new_gender[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_birth_date }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.item.general_info_tab.birth_date')}}</label>
                    <div class="col-9">
                        <input v-model="form.new_birth_date"  :placeholder="$tc('participants.form.enter_correct_iin') " class="form-control" disabled/>
                        <form-error v-if="errors && errors.new_birth_date" :errors="errors">
                            {{ errors.new_birth_date[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_phone }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.list.phone')}}</label>
                    <div class="col-9">
                        <masked-input v-model="form.new_phone" class="form-control" mask="1 (111) 111 11 11" placeholder="8 (777) 777 77 77"/>
                        <form-error v-if="errors && errors.new_phone" :errors="errors">
                            {{ errors.new_phone[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_email }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.item.general_info_tab.email')}} </label>
                    <div class="col-9">
                        <input type="email" v-model="form.new_email" class="form-control"  :placeholder="$tc('participants.form.enter_email')"/>
                        <form-error v-if="errors && errors.new_email" :errors="errors">
                            {{ errors.new_email[0] }}
                        </form-error>
                    </div>
                </div>

                <div v-bind:class="{ 'has-error': errors && errors.new_disability }" class="form-group row">
                    <label class="col-3 col-form-label">{{$tc('participants.item.general_info_tab.disability')}} </label>
                    <div class="col-9">
                        <select v-model="form.new_disability"  class="form-control">
                            <option value="0">{{$tc('participants.item.general_info_tab.disability_level_0')}}</option>
                            <option value="1">{{$tc('participants.item.general_info_tab.disability_level_1')}}</option>
                            <option value="2">{{$tc('participants.item.general_info_tab.disability_level_2')}}</option>
                            <option value="3">{{$tc('participants.item.general_info_tab.disability_level_3')}}</option>
                            <option value="4">{{$tc('participants.item.general_info_tab.disability_level_4')}}</option>
                        </select>
                        <form-error v-if="errors && errors.new_disability" :errors="errors">
                            {{ errors.new_disability[0] }}
                        </form-error>
                    </div>
                </div>
                <div v-bind:class="{ 'has-error': errors && errors.locality }" class="form-group ">
                    <label>{{$tc('participants.form.locality_select')}}</label>
                    <div class="row">
                        <label class="col-3 col-form-label">{{$tc('regions.region')}}</label>
                        <div class="col-9">
                            <select class="form-control" v-model="region" >
                                <option default value="">{{$tc('regions.select_region')}}</option>
                                <option v-for="region in $common.data.regions" :value="region"> {{region.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="region!==''" class="row">
                        <label class="col-3 col-form-label">{{$tc('regions.district')}}</label>
                        <div class="col-9" >
                            <select class="form-control" v-model="district" >
                                <option default value="">{{$tc('regions.select_district')}}</option>
                                <option v-for="district in region.districts" :value="district"> {{district.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div  v-if="district!=='' && region!==''" class="row">
                        <label class="col-3 col-form-label">{{$tc('regions.locality')}}</label>
                        <div class="col-9">
                            <select class="form-control" v-model="form.locality_id" >
                                <option default value="">{{$tc('regions.select_locality')}}</option>
                                <option v-for="locality in district.localities" :value="locality.id"> {{locality.name}}</option>
                            </select>

                        </div>
                        <form-error v-if="errors && errors.locality" :errors="errors">
                            {{ errors.locality[0] }}
                        </form-error>
                    </div>
                </div>
                <div slot="modal-footer">
                    <button
                            type="button"
                            class="btn btn-primary"
                            @click="sendForm"
                            :disabled="(formSending? true : false)"
                    >
                        <i v-show="formSending" class="fa fa-spinner fa-spin"></i> {{ form.id ? $tc("participants.form.update") :  $tc("participants.form.create") }}
                    </button>
                </div>


        </b-modal>
    </div>


</template>



<script>

    import { post } from '../../../../../helpers/api'

    export default {

        props: ['data', '_form'],

        data() {
            return {
                errors: [],
                formSending: false,
                form: '',
                region:'',
                district:'',
                title: ''
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
        mounted() {
            this.title = this.form.id ? this.$tc("participants.form.edit_participant") : this.$tc("participants.form.create_participant");
        },
        components: {
            FormError : require('../../../../../components/FormError.vue')
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