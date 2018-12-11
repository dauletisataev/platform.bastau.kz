<template>
	<div>

		<div class="col-2 offset-2 fixed-top h-100 pt-4">
			<button class="btn btn-secondary btn-block text-left btn-sm" @click="edit()">
				<span class="fa fa-fw fa-pencil"></span>
				{{ $t("trainer.edit") }}
			</button>

			<!-- <button class="btn btn-danger btn-block text-left btn-sm" @click="$refs.modalDelete.show()">
				<span class="fa fa-fw fa-trash"></span>
				{{ $t("trainer.delete") }}
			</button> -->

			<button class="btn btn-default btn-block text-left btn-sm" @click="$refs.archiveReason.show()">
				<span class="fa fa-fw fa-trash"></span>
				<!-- {{ $t("trainer.archive") }} -->
				Архивировать
			</button>
		</div>

		<div class="col-8 offset-4 pr-5">
			
			<div class="mb-5 mt-1">
				<img 
				:src="trainer.photo" 
				class="rounded-circle float-left mr-2" 
				style="margin-top: -5px; width: 40px;" />
				<!-- <span class="h4">{{ fullName }}</span> -->
			</div> 

			<div class="h5">{{ $t("trainer.contacts") }}</div>

			<!-- <table class="table table-sm table-responsive">
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
			</table>  -->

		</div>

		<trainer-form v-if="form" ref="editTrainer" :_form="form" @formSending="getItem()"></trainer-form>
		
		<!-- <b-modal ref="modalDelete">
			{{ $t("trainer.confirmDelete") }}
			<div slot="modal-footer">
				<button class="btn btn-secondary" @click="$refs.modalDelete.hide()">{{ $t("trainer.cancel") }}</button>
				<button class="btn btn-danger" @click="archive()">{{ $t("trainer.delete") }}</button>
			</div>
		</b-modal> -->

		<b-modal ref="archiveReason">
			<!-- {{ $t("trainer.archiveReason") }} -->Причина архивации
			<select v-model="archive_reason" class="form-control">
				<option :value="archive.id" v-for="archive in $common.data.archived_reasons">{{ archive.reason }}
				</option>
			</select>
			<div slot="modal-footer">
				<button class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отмена</button>
				<button class="btn btn-danger" @click="archive()">Архивировать</button>
			</div>
		</b-modal>

	</div>
</template>

<script>
	import { del, get, post  } from './../../../helpers/api.js';
	import TrainerForm from './../Form.vue';
	import FormError from './../../../components/FormError.vue';

	export default {
		props: ['info'],
		data() {
			return {
				errors: [],
				trainer: {},
				form: '',
				archive_reason: 0,
			}
		},

		components: {
			TrainerForm, FormError,
		},
		methods: {
			edit() {
				this.form = JSON.parse(JSON.stringify(this.trainer));
				this.$nextTick(function() {
					this.$refs.editTrainer.showModal();
				});
			},
			getItem() {
				let self = this;
				get(self, 
					'/api/trainers/' + this.id, 
					{}, 
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
				// this.$router.go(-1);
			},

			archive() {
				let self = this;
				post(
					self, 
					`/api/trainers/archive/${this.id}`,
					{'archive_reason': self.archive_reason},
					(res) => console.log(res),
					(err) => console.log(err)
					);
				// this.$router.go(-1);
			}
		},
		created() {
			// this.getItem();
			console.log(this.info);
		}
	};
</script>

<style scoped>
	
</style>