<template>
    <div class="container-fluid">
        <participant-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered"></participant-filter>
        <!-- Результаты -->
        <div class="col-10 offset-2  ">
            {{ $tc('participants.list.found_number') }} {{ total }}
            <button type="button" class="btn btn-primary btn-sm ml-2 float-right" @click="$refs.newParticipant.showModal()">{{ $tc('participants.list.add') }}</button>
            <table class="table">
                <thead class="thead-default">
                <tr>
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
                default_url: '/api/participants'
            }
        },
        components: {
            'participant-form': require('./Form.vue'),
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

            }

        },

        created() {
            window.document.body.onscroll = this.handleScroll;
        }

    }
</script>
