<template>
    <div>
        <div class="mb-5 mt-1">
            <img :src="participant.photo" class="rounded-circle float-left mr-2" style="margin-top: -5px; width: 40px;">
            <span class="h4">{{participant.user.first_name }} {{participant.user.last_name }} {{participant.user.patronymic }}</span>
        </div>
        <div class="h5">{{$tc('participants.item.general_info_tab.personal_data')}}</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">{{$tc('participants.list.iin')}}</span></td>
                <td>
                    <inline-editing ref="user.iin"  @editOn="resetEditorsExcept('user.iin')"   :_value="participant.user.iin"  @save="saveField('user','iin',$event)" ></inline-editing></td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.list.gender')}}</span></td>
                <td>
                    <select-editing ref="user.gender"  @editOn="resetEditorsExcept('user.gender')"  :_value="participant.user.gender"   :options="[{name:$tc('participants.list.custom_data.gender.male'),id:'M'},{name:$tc('participants.list.custom_data.gender.female'),id:'F'}]" @save="saveField('user','gender',$event)" />
                </td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.item.general_info_tab.role')}}</span></td>
                <td>{{$tc('participants.item.general_info_tab.participant')}}</td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.item.general_info_tab.disability')}}</span></td>
                <td>
                    <select-editing ref="disability" @editOn="resetEditorsExcept('disability')" :_value="participant.disability"  :options="[{name:0,id:0},{name:1,id:1},{name:2,id:2},{name:3,id:3}]" @save="saveField('participant','disability',$event)" />
                </td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.list.identity_card')}}</span></td>
                <td v-if="participant.identity_card" @click="$refs.modalIC.openModal('IC')">Загружено</td>
                <td v-else><label @click='$refs.uploadIdentityCard.show()'>Не загружено</label></td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.list.address_sertificate')}}</span></td>
                <td v-if="participant.address_certificate">Загружено</td>
                <td v-else><label @click='$refs.uploadAddressCertificate.show()'>Не загружено</label></td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.list.registration_date')}}</span></td>
                <td>{{ participant.created_at }}</td>
            </tr>
            </tbody>
        </table>
        <div class="h5">{{$tc('participants.item.general_info_tab.contact_information ')}}</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">{{$tc('participants.list.phone')}}</span></td>
                <td>
                    <phone-editing ref="user.phone"  @editOn="resetEditorsExcept('user.phone')"  :_value="participant.user.phone" @save="saveField('user','phone',$event)" /></td>
            </tr>
            <tr>
                <td><span class="h6">{{$tc('participants.item.general_info_tab.email')}}</span></td>
                <td>{{participant.user.email }}</td>
            </tr>
            </tbody>
        </table>
        <div class="h5">{{$tc('participants.item.general_info_tab.information_for_administrator')}}</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">Статус</span></td>
                <td v-if="participant.in_archive">
                    {{$tc('participants.item.general_info_tab.archived')}}
                </td>
                <td v-else>
                    {{$tc('participants.filter.active')}}
                </td>
            </tr>
            <tr v-if="participant.in_archive">
                <td><span class="h6">{{$tc('participants.item.general_info_tab.archived_time')}}</span></td>
                <td>{{participant.user.deleted_at }}</td>
            </tr>
            <tr v-if="participant.in_archive">
                <td><span class="h6">{{$tc('participants.item.general_info_tab.archived_reason')}}</span></td>
                <td>{{ participant.archive_reason.reason }}</td>
            </tr>
            </tbody>
        </table>
        <b-modal ref="uploadIdentityCard" :title="$tc('participants.item.general_info_tab.upload_identity_card')">
            <div class="form-group">
                {{$tc('participants.item.general_info_tab.select_identity_card')}}
            </div>
            <div class="form-group">
                <input ref="uploadIC" type="file" v-show="false" accept=".jpeg , .pdf , .jpg" v-on:change="selectFile"/>
                <button class="btn btn-primary "@click="$refs.uploadIC.click()">
                    <template v-if="file">{{file && file.name}}</template>
                    <template v-else>{{$tc('participants.item.general_info_tab.select')}}</template>
                </button>
            </div>
            <button :disabled="!file" class="btn btn-secondary" slot="modal-footer" @click="uploadFile('identity_card','uploadIdentityCard')">{{$tc('participants.item.general_info_tab.upload')}}</button>
        </b-modal>
        <b-modal ref="uploadAddressCertificate" :title="$tc('participants.item.general_info_tab.upload_address_sertificate')">
            <div class="form-group">
                {{$tc('participants.item.general_info_tab.select_address_sertificate')}}
            </div>
            <div class="form-group">
                <input ref="uploadAC" type="file" v-show="false" accept=".jpeg , .pdf , .jpg" v-on:change="selectFile"/>
                <button class="btn btn-primary "@click="$refs.uploadAC.click()">
                    <template v-if="file">{{file && file.name}}</template>
                    <template v-else>{{$tc('participants.item.general_info_tab.select')}}</template>
                </button>
            </div>
            <button :disabled="!file" class="btn btn-secondary" slot="modal-footer" @click="uploadFile('address_certificate','uploadAddressCertificate')">{{$tc('participants.item.general_info_tab.upload')}}</button>
        </b-modal>
        <document-modal ref="modalIC" :participant_id="participant.id" :src="participant.identity_card"/>
    </div>
</template>
<script>

    import { del, get,post } from '../../../helpers/api';
    export default {

        props: ['participant'],

        data() {
            return {
                errors: [],
                file:'',
            }
        },

        components: {
            "inline-editing":require('../../../components/bootstrap-editable/input.vue'),
            "phone-editing":require('../../../components/bootstrap-editable/inputPhone.vue'),
            "select-editing":require('../../../components/bootstrap-editable/Select.vue'),
            'document-modal':require('./modals/documentPreviewModal.vue'),
        },

        methods: {
            resetEditorsExcept(value) {
                if(value !== 'editName') {
                    this.$emit('resetEditor');
                }
                for(let key in this.$refs) {
                    if(key !== value && key !== 'editSchedules' && key !== 'editSocials') {
                        if(this.$refs.hasOwnProperty(key)) {
                            this.$refs[key].value = this.$refs[key].defaultValue;
                            this.$refs[key].mode = 'view';
                        }
                    }
                }
            },

            selectFile(e){
                this.file = e.target.files[0];
            },
            saveField(type,key,value) {
                let _this = this;
                let params={
                    key:key,
                    value:value
                };
                if(type==="user"){
                    this.participant.user[key]=value;
                    params.user_id=this.participant.user.id;
                }else if(type==="participant"){
                    this.participant[key]=value;
                }
                post(_this,'/api/participant-field-save/' + this.participant.id, params, function(response) {
                    _this.$emit('updated');
                }, function(error){

                });
            },
            uploadFile(name,ref){
                let _this=this;
                let data=new FormData();
                data.append("file",this.file);
                data.append("type",name);
                post(_this,'/api/participant-document-save/' + this.participant.id, data, function(response) {
                    _this.$emit('updated');
                }, function(error){

                });
                this.$refs[ref].hide();

            },
        },


    }


</script>