<template>
	<div class="col-10 offset-2">
		<b-tabs card>
			<b-tab title='Информация' active>
				<btinfo :info="trainer"></btinfo>
			</b-tab>
			<b-tab title='История' active>
				<bthitory :histories="trainer.histories"></bthitory>
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
	import History from './item/History.vue';
	import TrainerInfo from './item/TrainerInfo.vue';
	import { get } from './../../helpers/api.js';

	export default {
		props: ['id'],
		data() {
			return {
				trainer: "",
			}
		},
		components: {
			'btinfo': TrainerInfo,
			'bthitory': History
		},
		methods: {
			getItem() {
				let self = this;
				get(self, 
					'/api/trainers/' + self.id, 
					{}, 
					(res) => self.trainer = res.data, 
					(err) => console.log("FOCKEN", err));
			}
		},
		mounted() {
			this.getItem();
		}
	};
</script>