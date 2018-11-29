<template>
    <b-modal ref="modal" size="lg">
        <div slot="modal-title">Добавить занятие</div>
        <div>
            <input v-model="title" class="form-control" type="text" placeholder="укажите тему занятия">
        </div>
        <div slot="modal-footer">
            <button type="button" class="btn btn-primary" @click="sendForm();" :disabled="formSending? true : false">
                <i v-show="formSending" class="fa fa-spinner fa-spin"></i>
                Добавить
            </button>
        </div>

    </b-modal>
</template>
<script>
    import { post } from '../../helpers/api'

    export default {

        props: ['template_id'],

        data() {

            return {
                title: '',
                formSending: false,
            }

        },
        components: {
            FormError : require('./../../components/FormError.vue')
        },
        methods: {
            sendForm() {

                this.formSending = true;
                let _this = this;
                post(_this, '/api/lesson-template-item-save/'+this.template_id, this.title, function (response) {
                    _this.formSending = false;
                    _this.hideModal();
                    _this.$emit('formSending');
                    if(response.data) _this.$router.push({ name: 'lesson_template_content', params:{id: response.data, template_id: _this.template_id} });
                });

            },
            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
        }
    }
</script>