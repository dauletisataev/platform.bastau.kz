<!--    Ерсултан
    Тэмплейт для содержания контейнеров от участников
-->
<template>
    <div v-on:updated="updateData">
        <!--Боковая панель в которой имеются кнопки удаления, архивации и активации участника
            НАЧАЛО
        -->
        <div class="col-2 offset-2  h-100 pt-4">
            <button v-if="!participant.in_archive" type="button" class="btn btn-secondary btn-block text-left btn-sm"
                    @click="$refs.modalDelete.showModal()"><span class="fa fa-fw fa-trash"></span>
                {{$tc('participants.item.archive')}}
            </button>
            <button v-else type="button" class="btn btn-secondary btn-block text-left btn-sm"
                    @click="$refs.modalRestore.showModal()">
                {{$tc('participants.item.restore')}}
            </button>
            <button  type="button" class="btn btn-danger btn-block text-left btn-sm"
                    @click="remove">
                {{$tc('participants.item.delete')}}
            </button>
        </div>
        <!--КОНЕЦ-->
        <!-- основной табовый контейнер, который содержит более подробное описание о участнике(история и основная информация)
                НАЧАЛО-->
        <div class="col-8 offset-4 fixed-top h-100 pt-4">
            <b-tabs>
                <b-tab :title="$tc('participants.item.general_info')" active>
                    <info :participant="participant"/>
                </b-tab>
                <b-tab :title="$tc('participants.item.history')" >
                    <history :histories="histories"/>
                </b-tab>
            </b-tabs>
        </div>
        <!--КОНЕЦ -->
        <!--Модали для добавления документов-->
        <archive-modal ref="modalDelete" :header="$tc('participants.item.delete_warning')" user_type="participant" :user_id="this.participant.user.id"></archive-modal>
        <activate-modal ref="modalRestore" :header="$tc('participants.item.restore_warning')" user_type="participant" :user_id="this.participant.user.id"></activate-modal>
        <!--КОНЕЦ-->
    </div>

</template>

<script>

    import { get,post } from '../../helpers/api';
    export default {

        props: ['id','user_id'],

        data() {
            return {
                errors: [],
                participant: '',
                histories:'',
            }
        },

        components: {
            FormError : require('./../../components/FormError.vue'),
            "inline-editing":require('../../components/bootstrap-editable/input.vue'),
            "phone-editing":require('../../components/bootstrap-editable/inputPhone.vue'),
            "select-editing":require('../../components/bootstrap-editable/Select.vue'),
            "archive-modal":require('../../components/modals/ArchiveUser.vue'),
            "activate-modal":require('../../components/modals/ActivateUser.vue'),
            "history":require('./item/history.vue'),
            "info":require('./item/info.vue')
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
                console.log(params);
                post(_this,'/api/participant-field-save/' + this.participant.id, params, function(response) {
                    _this.$emit('updated');
                }, function(error){

                });
            },
            getItem() {
                let _this = this;
                get(_this, '/api/participant/' +  this.id, {}, function (response) {
                    _this.participant = response.data;
                    console.log(_this.participant);
                });
            },
            getHistory(){
                let _this=this;
                get(_this,'/api/participant/histories/'+this.id,{},function (response) {
                    _this.histories = response.data.data;
                });
            },
            updateData(){
                this.getItem();
                this.getHistory();
            },
            remove(){
                let _this=this;
                get(_this,'/api/participants/full-delete/'+this.id,{},function (response) {
                    _this.$router.back();
                });
            }

        },

        created() {
            this.getItem();
            this.getHistory();
        }

    }


</script>