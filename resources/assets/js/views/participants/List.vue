<template>
    <div class="container-fluid">
        <participant-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered"></participant-filter>
        <!-- Результаты -->
        <div class="col-10 offset-2  mt-5 pt-5">
            Найдено <b>{{ total }}</b> заявок
                <button type="button" class="btn btn-primary btn-sm ml-2" @click="$refs.newParticipant.showModal()">добавить</button>
            <table class="table">
                <thead class="thead-default">
                <tr>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Отчество</th>
                    <th>Пол</th>
                    <th>ИИН</th>
                    <th>Телефон</th>
                    <th>Удостоверение личности</th>
                    <th>Адресная справка</th>
                    <th>Время подачи</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="participant in participants">
                    <td>{{ participant.user.first_name }}</td>
                    <td>{{ participant.user.last_name}}</td>
                    <td>{{participant.user.patronymic}}</td>
                    <td>{{participant.user.gender === "M" ? "М":"Ж"}}</td>
                    <td>{{participant.user.iin}}</td>
                    <td>{{participant.user.phone}}</td>
                    <td>{{participant.identity_card ? "загружен":"не загружен"}}</td>
                    <td>{{participant.address_certificate ?"загружен":"не загружен"}}</td>
                    <td>{{participant.user.created_at}}</td>
                    <td>
                        <div class="pull-right">
                            <b-tooltip title="Редактирование участника">
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

    import { get } from '../../helpers/api'

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