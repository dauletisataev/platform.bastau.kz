<template>
    <div class="container-fluid">
        <participant-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered" v-on:changeMode="selectMode = arguments[0]" :actionOption="selectMode ? 'select': 'action'"></participant-filter>
         <!-- 
             *Daulet
             *блок с действиями с выделенными пользователями
             -Отправить мэйл
             -Отправить смс 
          -->
        <div v-if="selectMode" class="card mt-4 col-10 offset-2" style="background-color: #f7f7f9;" >
                <div class="card-block ">
                    <div class="clearfix">
                        <div class="d-inline-block">
                            <button :disabled="selectedIds.length==0" @click="$refs.sendMailModal.showModal()" class="btn btn-primary">
                                {{$tc('participants.action_on_selected.send_mail')}}
                            </button>
                        </div>
                        <div class="d-inline-block ml-2">
                            <button  :disabled="selectedIds.length==0"  @click="$refs.sendSmsModal.showModal()" class="btn btn-primary">
                                 {{$tc('participants.action_on_selected.send_sms')}}
                            </button>
                        </div> 
                        <button class="btn btn-danger pull-right" @click="selectMode = false">
                            {{$tc('participants.action_on_selected.cancel')}}
                        </button>
                    </div>
                </div>
        </div>

        <!-- Результаты -->
        <div class="col-10 offset-2  ">
            {{ $tc('participants.list.found_number') }} {{ total }}
            <button type="button" class="btn btn-primary btn-sm ml-2 float-right" @click="$refs.newParticipant.showModal()">{{ $tc('participants.list.add') }}</button>
            <table class="table">
                <thead class="thead-default">
                <tr>
                    <th  v-if="selectMode" >
                        <input type="checkbox" @change="onCheckAll(checkAll)" name="" id="" v-model="checkAll">
                    </th>
                    <th>{{ $tc('participants.list.first_name') }}</th>
                    <th>{{ $tc('participants.list.last_name') }}</th>
                    <th>{{ $tc('participants.list.patronymic') }}</th>
                    <th>{{ $tc('participants.list.gender') }}</th>
                    <th>{{ $tc('participants.list.iin') }}</th>
                    <th>{{ $tc('participants.list.phone') }}</th>
                    <th>{{ $tc('participants.list.identity_card') }}</th>
                    <th>{{ $tc('participants.list.address_sertificate') }}</th>
                    <th>{{ $tc('participants.list.registration_date') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="participant in participants">
                    <td v-if="selectMode">
                        <!-- Даулет:  onChange-> если один элемент unchecked, then checkAll = false  -->
                        <input class="form-control" @change="!selectedIds.includes(participant.user_id) ? checkAll = false : true" :value="participant.user_id" v-model="selectedIds" type="checkbox">
                    </td>
                    <td>{{ participant.user.first_name }}</td>
                    <td>{{ participant.user.last_name}}</td>
                    <td>{{participant.user.patronymic}}</td>
                    <td>{{participant.user.gender === "M" ? $tc('participants.list.custom_data.gender.male'):$tc('participants.list.custom_data.gender.female')}}</td>
                    <td>{{participant.user.iin}}</td>
                    <td>{{participant.user.phone}}</td>
                    <td>{{participant.identity_card ? $tc('participants.list.custom_data.document.loaded'):$tc('participants.list.custom_data.document.not_loaded')}}</td>
                    <td>{{participant.address_certificate ?$tc('participants.list.custom_data.document.loaded'):$tc('participants.list.custom_data.document.not_loaded')}}</td>
                    <td>{{participant.user.created_at}}</td>
                    <td>
                        <div class="pull-right">
                            <b-tooltip  :title="$tc('participants.list.edit_participant')" placement="left">
                                <router-link :to="{name:'participant', params:{id: participant.id}}"
                                             class="btn btn-outline-primary btn-sm">
                                            <span class="fa fa-user"></span></router-link>
                            </b-tooltip>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <participant-form ref="newParticipant" :data="$common.data" :_form="newParticipant" v-on:formSending="filtered"></participant-form>
        <send-mail-modal ref="sendMailModal" :data="Array.from(selectedIds)" ></send-mail-modal>
        <send-sms-modal ref="sendSmsModal" :data="Array.from(selectedIds)" ></send-sms-modal>

    </div>
</template>

<script>

    import { get } from '../../../../../helpers/api'

    export default {

        data() {
            return {
                load: false,
                scrollLoad: false,
                newParticipant: '',
                participants: [],
                total: 0,
                resource_url: '/api/participants',
                next_url: '',
                default_url: '/api/participants',
                /*
                    selectMode variable
                    creator: Daulet
                */
                selectMode: true, 
                checkAll: false,
                selectedIds: []
            }
        },
        components: {
            'participant-form': require('./Form.vue'),
            'send-mail-modal': require('./modals/sendMailModal.vue'),
            'send-sms-modal': require('./modals/sendSmsModal.vue'),
            'participant-filter': require('./Filter.vue')
        },
        methods: {
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;
                get(_this, this.resource_url, {params: this.filterData}, function (response) {

                    let json = response.data;

                    _this.next_url = json.next_page_url;

                    _this.participants = _this.participants.concat(json.data);

                    _this.total = json.total;

                    _this.scrollLoad = false;
                    _this.load = false;

                }, function () {

                    _this.scrollLoad = false;
                    _this.load = false;

                });

            },
            filtered() {
                this.resource_url = this.default_url;
                this.participants = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;
                this.checkAll = false;
                this.selectedIds = [];
                this.$nextTick(function () {
                    this.$router.push({ path: '/participants', query: this.filterData });
                    this.getList();
                });

            },
            handleScroll(e){
                let body = document.body,
                    html = document.documentElement;

                let height = Math.max( body.scrollHeight, html.scrollHeight);

                if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !this.scrollLoad) {
                    this.scrollLoad = true;
                    this.$nextTick(function () {
                        this.getList();
                    })
                }

            }, 
            onCheckedChange(participant){
                console.log(participant.selected);
                if(!participant.selected) {
                    this.checkAll = false;
                    this.selectedIds.delete(participant.user_id);
                } else{
                    this.selectedIds.add(participant.user_id);
                } 
                //console.log(Array.from(this.selectedIds));
            },
            onCheckAll(checked){
                this.selectedIds = [];
                if(checked)
                for(let x =0; x<this.participants.length; x++){ 
                    this.selectedIds.push(this.participants[x].user_id); 
                } 
            },
            selectedChanged(checked){
                if(!checked) checkAll = false;;
            }

        }, 
        created() {
            window.document.body.onscroll = this.handleScroll;
        }

    }
</script>
