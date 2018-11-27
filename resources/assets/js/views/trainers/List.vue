<template>
	<div>
        <trainer-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered"></trainer-filter>
        <div class="col-7 offset-4">

            Найдено <b>{{ total }}</b> тренеров
            <button type="button" class="btn btn-primary btn-sm ml-2" @click="$refs.newTrainer.showModal()">добавить</button>

            <table class="table mt-4">
                <thead class="thead-default">
                <tr>
                    <th>Имя</th>
                    <th>Роль</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="trainer in trainers">
                    <td>{{ trainer.user.name }}</td>
                    <td>{{ trainer.user.role.description }}</td>
                    <td>
                        <div class="pull-right">
                            <b-tooltip title="Открыть профиль">
                                <router-link :to="{name:'trainer', params:{id: trainer.id}}" class="btn btn-outline-primary btn-sm"><span class="fa fa-user"></span></router-link>
                            </b-tooltip>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <trainer-form ref="newTrainer" :data="$common.data" :_form="newTrainer" v-on:formSending="filtered"></trainer-form>

    </div>
</template>

<script>
	import { get } from './../../helpers/api.js';

	export default {
		data() {
			return {
				resourceUrl: '/api/trainers/',
                defaultUrl: '/api/trainers/',
				trainers: [],
				load: false,
                scrollLoad: false,
                newTrainer: '',
                total: 0,
                next_url: '',
			}
		},
        components: {
            'trainer-filter': require('./Filter.vue'),
            'trainer-form': require('./Form.vue')
        },
		methods: {
			getTrainers() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;

                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
				let _this = this;
				get(_this, _this.resourceUrl, {}, function(response) {
					let data = response.data;
                    _this.next_url = data.next_page_url;
                    _this.trainers = _this.trainers.concat(data);
                    _this.total = _this.trainers.length;
                    _this.scrollLoad = false;
                    _this.load = false;
                    console.log(_this.trainers);
				}, function() {
                    _this.scrollLoad = false;
                    _this.load = false;
				});
			},

            filtered() {
                this.resourceUrl = this.defaultUrl;
                this.trainers = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;
                this.$nextTick(function() {
                    this.$router.push({path: '/control/trainers', query: this.filterData});
                    this.getTrainers();
                })
            },
            handleScroll(e){
                let body = document.body,
                    html = document.documentElement;

                let height = Math.max(body.scrollHeight, html.scrollHeight);

                if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !this.scrollLoad) {
                    this.scrollLoad = true;
                    this.$nextTick(function () {
                        this.getTrainers();
                    })
                }

            }

		},
        created() {
            window.document.body.onscroll = this.handleScroll;
        }
	};
</script>

<style scoped>
	
</style>