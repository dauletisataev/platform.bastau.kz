<template>
	<div>
        <tfilter v-if="trainers" :data='trainers'></tfilter>
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
                                <router-link :to="{name:'trainer', params:{id: trainer.id}}" :id='trainer.id' class="btn btn-outline-primary btn-sm"><span class="fa fa-user"></span></router-link>
                            </b-tooltip>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <tform ref="newTrainer" :_form="newTrainer" @formSending="filtered"></tform>

    </div>
</template>

<script>
	import { get } from './../../helpers/api.js';
    import Filter from './Filter.vue';
    import Form from './Form.vue';

	export default {
		data() {
			return {
				resourceUrl: '/api/trainers/',
				trainers: [],
                newTrainer: ''
			}
		},
        components: {
            'tform': Form, 
            'tfilter': Filter
        },
		methods: {
			getList() {
                let self = this;
                get(
                    self, 
                    self.resourceUrl, 
                    {}, 
                    (response) => _.each(response.data, trainer => self.trainers.push(trainer)), 
                    (err) => console.log(err));
			},

            filtered() {
            },
		},
        computed: {
            total() {
                return this.trainers.length;
            }
        },
        mounted() {
            this.getList();
        }
	};
</script>

<style scoped>
	
</style>