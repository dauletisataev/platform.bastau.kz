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
	<div>

		<div class="col-2 offset-2 fixed-top h-100 pt-4">
			<button class="btn btn-secondary btn-block text-left btn-sm" @click="edit()">
				<span class="fa fa-fw fa-pencil"></span>
				{{ $t("trainer.edit") }}
			</button>

			<button  v-if="$root.user.isAdmin()" class="btn btn-danger btn-block text-left btn-sm" @click="$refs.modalDelete.show()">
				<span class="fa fa-fw fa-trash"></span>
				{{ $t("trainer.delete") }}
			</button>

			<button v-if="$root.user.isAdmin()"  class="btn btn-default btn-block text-left btn-sm" @click="$refs.modalArchive.showModal()">
				<span class="fa fa-fw fa-trash"></span>
				{{ $t("trainer.archive") }}
				
			</button> 
		</div>

		<div class="col-8 offset-4 pr-5">
			
			<div class="mb-5 mt-1">
				<img 
				:src="trainer.photo" 
				class="rounded-circle float-left mr-2" 
				style="margin-top: -5px; width: 40px;" />
				<span class="h4">{{ fullName }}</span>
			</div> 

			<div class="h5">{{ $t("trainer.contacts") }}</div>

			<table class="table table-sm table-responsive">
				<tbody>
					<tr>
						<td><span class="h6">{{ $t("trainer.phone") }}</span></td>
						<td>{{ trainer.user.phone }}</td>
					</tr>
					<tr>
						<td><span class="h6">Email</span></td>
						<td>{{ trainer.user.email }}</td>
					</tr>
					<tr>
						<td><span class="h6">{{ $t("trainer.date_created") }}</span></td>
						<td>{{ trainer.user.created_at }}</td>
					</tr>
				</tbody>
			</table>  
		</div>

		<trainer-form v-if="form" ref="editTrainer" :_form="form" @formSending="getItem()"></trainer-form>
		
		<b-modal ref="modalDelete">
			{{ $t("trainer.confirmDelete") }}
			<div slot="modal-footer">
				<button class="btn btn-secondary" @click="$refs.modalDelete.hide()">{{ $t("trainer.cancel") }}</button>
				<button class="btn btn-danger" @click="deleteTrainer()">{{ $t("trainer.delete") }}</button>
			</div>
		</b-modal>
		
		<archiveModal ref="modalArchive" :user_id="this.trainer.user.id" v-on:archive="archive(arguments[0])"></archiveModal>
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
                errors: [],
                trainer: {},
                form: '',
                selected_reason: 0,
                description:'',
			}
		},
		components: {
			'btinfo': TrainerInfo,
			'bthitory': History,
            TrainerForm, FormError,
            'archiveModal': require('./modals/ArchiveTrainer.vue'),
			}
		},
		mounted() { 
        },
		computed: {
			fullName() {
				return this.trainer.user.first_name + ' ' + this.trainer.user.last_name + ' ' + this.trainer.user.patronymic;
			}
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
					(res) => this.trainer = res.data,
					(err) => console.log(err));
			},
			deleteTrainer() {
				let self = this;
				del(
					self, 
					`/api/trainers/delete/${this.id}`,
					(res) => console.log(res),
					(err) => console.log(err)
					);
				this.$refs.modalDelete.hide();
			},
			archive(reason_id){ 
				let _this = this;
				console.log(reason_id);
				if(this.trainer.in_archive) {  
					get(_this, '/api/trainers/archive/' + this.id, { archive_reason: reason_id }, function (response) {
						_this.getItem();
						_this.$refs.modalArchive.hideModal();
					},function (error) {
						_this.formSending = false;
						_this.errors = error.response.data;
					});
				}
				else { 
					get(_this, '/api/trainers/restore/' + this.id, {}, function (response) {
						_this.getItem();
						_this.$refs.modalArchive.hideModal();
					},function (error) {
						_this.formSending = false;
						_this.errors = error.response.data;
					});
				}
				
			},
		},
		mounted() {
			this.getItem();
		}
	};
</script>