<template>
    <b-modal ref="modal" title="Условия доступа к занятию">
        <div class="form-group">
            <label class="custom-control custom-checkbox d-block">
                <input type="checkbox" class="custom-control-input" :false-value="0" :true-value="1" v-model="form.access_free">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Доступно до оплаты (бесплатное занятие)</span>
            </label>
        </div>

        <div class="form-group">
            <label class="custom-control custom-checkbox d-block" @click="accessLesson()">
                <input type="checkbox" class="custom-control-input" @click="accessLesson()" :checked="form.access_tasks && form.access_homework && form.access_tests">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">
							Доступно после предыдущего занятия
						</span>
            </label>

            <label class="custom-control custom-checkbox d-block ml-2">
                <input type="checkbox" class="custom-control-input"  :false-value="0" :true-value="1" v-model="form.access_tasks">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Завершить все упражнения в материале</span>
            </label>

            <label class="custom-control custom-checkbox d-block ml-2">
                <input type="checkbox" class="custom-control-input" :false-value="0" :true-value="1" v-model="form.access_homework">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Завершить задание</span>
            </label>

            <label class="custom-control custom-checkbox d-block ml-2" >
                <input type="checkbox" class="custom-control-input" :false-value="0" :true-value="1" v-model="form.access_tests">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Завершить тестирование</span>
            </label>

            <label class="custom-control custom-checkbox d-block ml-2">
                <input type="checkbox" class="custom-control-input" :disabled="!form.access_tasks && !form.access_homework && !form.access_tests" :false-value="0" :true-value="1" v-model="form.access_offset">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">
							Не раньше чем через
							<input type="text" class="form-control form-control-sm d-inline-block" style="width:50px">
                            <b-dropdown size="sm" variant="link" class="d-inline-block bDropdownNarrow bDropdownActionLink">
                                <div slot="text">
                                    {{ offsetType() }}
									<span class="fa fa-angle-down"></span>
                                </div>
                                <b-dropdown-item @click="form.access_offset_type = 'minutes'" :disabled="form.access_offset_type == 'minutes'" class="small px-2 py-1">минут</b-dropdown-item>
                                <b-dropdown-item @click="form.access_offset_type = 'hours'" :disabled="form.access_offset_type == 'hours'" class="small px-2 py-1">часов</b-dropdown-item>
                                <b-dropdown-item @click="form.access_offset_type = 'days'" :disabled="form.access_offset_type == 'days'" class="small px-2 py-1">дней</b-dropdown-item>
                            </b-dropdown>
						</span>
            </label>
        </div>
        <div slot="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cancel()">Отменить</button>
            <button
                    type="button"
                    class="btn btn-primary"
                    @click="sendForm()"
                    :disabled="!!formSending"
            >
                <i v-show="formSending" class="fa fa-spinner fa-spin"></i> Сохранить
            </button>
        </div>
    </b-modal>
</template>
<script>

    export default {
        props: ['_form'],
        data() {
            return {
                form: '',
                formSending: false
            }
        },
        mounted() {
            this.form = this._form;
        },
        watch: {
            form: {
                handler(val) {
                    this.form = val;
                },
                deep: true,
            },
            'form.access_free'(val) {
                if(val) this.form.access_offset = 0;
            },
            'form.access_offset'(val) {
                if(val) this.form.access_free = 0;
            }
        },
        methods: {
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            accessLesson() {
                if(!this.form.access_tasks || !this.form.access_homework || !this.form.access_tests) {
                    this.form.access_tasks = 1;
                    this.form.access_homework = 1;
                    this.form.access_tests = 1;
                } else {
                    this.form.access_tasks = 0;
                    this.form.access_homework = 0;
                    this.form.access_tests = 0;
                }
            },
            offsetType() {
                switch(this.form.access_offset_type) {
                    case "minutes": return "минут";
                    break;
                    case "hours": return "часов";
                    break;
                    case "days": return "дней";
                    break;
                }
            },
            cancel() {
                this.hideModal();
                this.$emit('formSending');
            },
            sendForm() {
//                this.formSending = true;
//                post()
            }
        }
    }
</script>