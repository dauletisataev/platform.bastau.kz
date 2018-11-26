<template>
	<div>
        <!-- <trainer-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered"></trainer-filter> -->
        <!-- Результаты -->
        <div class="col-8 offset-4">
            Найдено <b>{{ total }}</b> пользователя
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
                                <router-link :to="{name:'trainer', params:{id: trainer.id}}" class="btn btn-outline-primary btn-sm"><span class="fa fa-trainer"></span></router-link>
                            </b-tooltip>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- <trainer-form ref="newTrainer" :data="$common.data" :_form="newTrainer" v-on:formSending="filtered"></trainer-form> -->

    </div>
</template>

<script>
	import { get } from './../../helpers/api.js';

	export default {
		data() {
			return {
				resourceUrl: '/api/trainers',
                default_url: '/api/trainers',
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
				let _this = this;
				get(_this, _this.resourceUrl, {}, function(response) {
					let data = response.data;
                    console.log(data);
					for (let trainer in data) {
						_this.trainers.push(data[trainer]);
					};
                    _this.total = data.total;
                    _this.scrollLoad = false;
                    _this.load = false;
                    // console.log(_this.trainers);
				}, function(err) {
					console.log("Got error", err);
				})
			},

		},
		mounted() {
			this.getTrainers();
		}
	};
</script>

<style scoped>
	
</style>