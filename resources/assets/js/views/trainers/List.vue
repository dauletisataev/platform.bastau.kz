<template>
	<div>
        <trainer-filter ref="filter" v-if="trainers" :data='trainers' @filtered="filtered"></trainer-filter>
        <div class="col-6 offset-4">

            {{ $t("trainer.found") }} <b>{{ total }}</b> {{ $t("trainer.trainers") }}
            <button class="btn btn-primary btn-sm ml-2" @click="$refs.newTrainer.showModal()">{{ $t("trainer.add") }} </button>

            <table class="table mt-4">
                <thead class="thead-default">
                <tr>
                    <th>{{ $t("trainer.name") }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="trainer in trainers">
                    <td>{{ trainer.user.name }}</td>
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

        <trainer-form ref="newTrainer" :_form="newTrainer" @formSending="filtered"></trainer-form>

    </div>
</template>

<script>
	import { get } from './../../helpers/api.js';
    import Filter from './Filter.vue';
    import Form from './Form.vue';

	export default {
		data() {
			return {
				trainers: [],
                newTrainer: '',
                resource_url: '/api/trainers',
                next_url: '',
                default_url: '/api/trainers',
                scrollLoad: false,
                load: false,
                total: 0,
			}
		},
        components: {
            'trainer-form': Form, 
            'trainer-filter': Filter
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

                    _this.trainers = _this.trainers.concat(json.data);
                    
                    _this.total = _this.trainers.length;

                    _this.scrollLoad = false;
                    _this.load = false;

                }, function () {

                    _this.scrollLoad = false;
                    _this.load = false;

                });

            },

            filtered() {
                this.resource_url = this.default_url;
                this.trainers = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;

                this.$nextTick(function () {
                    this.$router.push({ path: '/trainers', query: this.filterData });
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
        mounted() {
            window.document.body.onscroll = this.handleScroll;
        }
	};
</script>

<style scoped>
	
</style>