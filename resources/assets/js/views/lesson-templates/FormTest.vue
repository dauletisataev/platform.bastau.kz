<template>
    <b-modal ref="modal" size="lg">
        <div slot="modal-title">Добавить Тест</div>
        <div>
            <input v-model="title" class="form-control" type="text" placeholder="укажите вопрос">
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
                post(_this, '/api/lesson-template-add-test/'+this.template_id, this.title, function (response) {
                    _this.formSending = false;
                    _this.hideModal();
                    _this.$emit('formSending');
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