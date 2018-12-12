<template>
    <div>
        <div class="card mb-4">
            <div class="card-header">
                <span class="lead">Вопросы</span>
                <span class="badge badge-pill badge-default ml-1">{{ template.test_questions.length }}</span>
            </div>
            <div style="max-height: 350px; overflow-y:scroll">
                <table class="table">
                    <draggable v-model="template.test_questions" :options="{draggable:'.item', handle: '.btn-drag'}" :element="'tbody'">
                        <tr v-for="item in template.test_questions" class="item">
                            <td>
                                <div>
                                    {{ item.value }}
                                    <ul>
                                        <li :key="answer.id" 
                                            v-for="answer in item.answers">
                                            <input :data-answer="answer.is_correct" type="checkbox" :id="answer.id">
                                            <label :for="answer.id">{{ answer.value }}</label>
                                        </li>
                                    </ul>        
                                </div>
                            </td>

                            <td>
                                <div class="pull-right">
                                    <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger" type="button">
                                        <span class="fa fa-fw fa-trash"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </draggable>
                </table>
            </div>
            <a href="javascript:void(0)" 
               @click="$emit('save')"
               class="rounded h3 p-2 bg-primary text-white rounded-circle" 
               style="position:fixed;right:20px;bottom:20px;">
               <div class="fa fa-fw fa-save"></div>
           </a>
        </div>
        <b-modal ref="modalDelete" title="Подтвердите удаление">
            Вы действительно хотите удалить занятие? Данное действие нельзя отменить.
            <div slot="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$refs.modalDelete.hide()">Отменить</button>
                <button type="button" class="btn btn-danger" @click="remove()">Удалить</button>
            </div>
        </b-modal>
    </div>
</template>
<script>

    import { get, del, post } from '../../../helpers/api'

    export default {
        props: ['template'],
        data() {
            return {
                selectedItem: ''
            }
        },
        components: {
        },
        methods: {
            deleteItem(id) {
                this.selectedId = id;
                this.$refs.modalDelete.show();
            },
            remove() {
                let _this = this;
                get(_this, '/api/lesson-template-delete-test/'+this.selectedId, {}, function (response) {
                    _this.$emit('formSending');
                    _this.$refs.modalDelete.hide();
                });
            },
            goTo(id) {
                this.$router.push({name: '', id: id, template_id: this.template.id});
            },
        },
    }
</script>