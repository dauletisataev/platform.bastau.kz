<template>

    <div>

        <b-modal ref="modal" title="Архив">
            Вы действительно хотите {{ group.in_archive ? 'востановить' : 'переместить'}} группу {{ group.in_archive ? 'из архива' : 'в архив'}}?



            <!--
                        <div class="mt-3 mb-3 ml-3 " v-if="group.students.length && !group.in_archive">

                            <div class="h6 mb-2">Также, архивировать студентов:</div>

                            <div class="row" v-for="student in group.students">
                                <div class="col-8">
                                    <label class="custom-control custom-checkbox">
                                        <input v-model="archive_student_ids" :value="student.id"  type="checkbox" class="custom-control-input">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ student.user.name }}</span>
                                    </label>
                                </div>
                            </div>

                        </div>
            -->

            <div slot="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$refs.modal.hide()">Отменить</button>
                <button type="button" class="btn" v-bind:class="{ 'btn-success': group.in_archive, 'btn-danger': !group.in_archive }" @click="archive()" >  Подтвердить</button>
            </div>

        </b-modal>

    </div>


</template>

<script>

    import { post } from '../../../helpers/api'

    export default {

        props: ['group'],

        data() {

            return {

                archive_student_ids: []

            }

        },
        methods: {

            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            archive() {

                let _this = this;

                post(_this, '/api/group/'+this.group.id+'/archive', {student_ids: this.archive_student_ids}, function (response) {
                    _this.$refs.modal.hide();
                    _this.$emit('getItem');

                });

            },


        }

    }

</script>