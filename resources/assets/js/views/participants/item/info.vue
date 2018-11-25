<template>
    <div>
        <div class="mb-5 mt-1">
            <img :src="participant.photo" class="rounded-circle float-left mr-2" style="margin-top: -5px; width: 40px;">
            <span class="h4">{{participant.user.first_name }} {{participant.user.last_name }} {{participant.user.patronymic }}</span>
        </div>
        <div class="h5">Персональные данные</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">ИИН</span></td>
                <td>
                    <inline-editing ref="user.iin"  @editOn="resetEditorsExcept('user.iin')"   :_value="participant.user.iin"  @save="saveField('user','iin',$event)" ></inline-editing></td>
            </tr>
            <tr>
                <td><span class="h6">Пол</span></td>
                <td>
                    <select-editing ref="user.gender"  @editOn="resetEditorsExcept('user.gender')"  :_value="participant.user.gender"   :options="[{name:'Мужской',id:'M'},{name:'Женский',id:'F'}]" @save="saveField('user','gender',$event)" />
                </td>
            </tr>
            <tr>
                <td><span class="h6">Роль</span></td>
                <td>Участник</td>
            </tr>
            <tr>
                <td><span class="h6">Инвалидность</span></td>
                <td>
                    <select-editing ref="disability" @editOn="resetEditorsExcept('disability')" :_value="participant.disability"  :options="[{name:0,id:0},{name:1,id:1},{name:2,id:2},{name:3,id:3}]" @save="saveField('participant','disability',$event)" />
                </td>
            </tr>
            <tr>
                <td><span class="h6">Удостоверение личности</span></td>
                <td v-if="participant.identity_card" @click="$refs.modalIC.openModal('IC')">Загружено</td>
                <td v-else><label @click='$refs.uploadIdentityCard.show()'>Не загружено</label></td>
            </tr>
            <tr>
                <td><span class="h6">Адресная справка</span></td>
                <td v-if="participant.address_certificate">Загружено</td>
                <td v-else><label @click='$refs.uploadAddressCertificate.show()'>Не загружено</label></td>
            </tr>
            <tr>
                <td><span class="h6">Дата создания участника</span></td>
                <td>{{ participant.created_at }}</td>
            </tr>
            </tbody>
        </table>
        <div class="h5">Контактная информация</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">Телефон</span></td>
                <td>
                    <phone-editing ref="user.phone"  @editOn="resetEditorsExcept('user.phone')"  :_value="participant.user.phone" @save="saveField('user','phone',$event)" /></td>
            </tr>
            <tr>
                <td><span class="h6">Email</span></td>
                <td>{{participant.user.email }}</td>
            </tr>
            <tr>
                <td><span class="h6">Дата создания пользователя</span></td>
                <td>{{ participant.user.created_at }}</td>
            </tr>
            </tbody>
        </table>
        <div class="h5">Информация для администратора</div>
        <table class="table table-sm table-responsive">
            <tbody>
            <tr>
                <td><span class="h6">Статус</span></td>
                <td v-if="participant.in_archive">
                    Архивирован
                </td>
                <td v-else>
                    Активный
                </td>
            </tr>
            <tr v-if="participant.in_archive">
                <td><span class="h6">Время архивации</span></td>
                <td>{{participant.user.deleted_at }}</td>
            </tr>
            <tr v-if="participant.in_archive">
                <td><span class="h6">Причина архивации</span></td>
                <td>{{ participant.archive_reason.reason }}</td>
            </tr>
            </tbody>
        </table>
        <b-modal ref="uploadIdentityCard" title="Загрузка удостоверения личности">
            <div class="form-group">
                Выберите файл удостоверения линости
            </div>
            <div class="form-group">
                <input ref="uploadIC" type="file" v-show="false" accept=".jpeg , .pdf , .jpg" v-on:change="selectFile"/>
                <button class="btn btn-primary "@click="$refs.uploadIC.click()">
                    <template v-if="file">{{file && file.name}}</template>
                    <template v-else>Выберите</template>
                </button>
            </div>
            <button :disabled="!file" class="btn btn-secondary" slot="modal-footer" @click="uploadFile('identity_card','uploadIdentityCard')">Загрузить</button>
        </b-modal>
        <b-modal ref="uploadAddressCertificate" title="Загрузка адресной справки">
            <div class="form-group">
                Выберите файл адресной справки
            </div>
            <div class="form-group">
                <input ref="uploadAC" type="file" v-show="false" accept=".jpeg , .pdf , .jpg" v-on:change="selectFile"/>
                <button class="btn btn-primary "@click="$refs.uploadAC.click()">
                    <template v-if="file">{{file && file.name}}</template>
                    <template v-else>Выберите</template>
                </button>
            </div>
            <button :disabled="!file" class="btn btn-secondary" slot="modal-footer" @click="uploadFile('address_certificate','uploadAddressCertificate')">Загрузить</button>
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