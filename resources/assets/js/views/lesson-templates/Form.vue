<template>

    <b-modal ref="modal" size="lg">

        <div slot="modal-title">Шаблон занятий</div>
        <div v-bind:class="{ 'has-error': errors && errors.name }" class="form-group row">
            <label class="col-3 col-form-label">Название</label>
            <div class="col-9">
                <input v-model="formData.name" class="form-control" type="text" placeholder="укажите название шаблона">
                <form-error v-if="errors && errors.name" :errors="errors">
                    {{ errors.name[0] }}
                </form-error>
            </div>
        </div>
        <div v-bind:class="{ 'has-error': errors && errors.program_id }" class="form-group row">
            <label class="col-3 col-form-label">Программа</label>
            <div class="col-9">
                <div class="btn-group btn-group-sm">
                    <label class="btn btn-primary" v-for="program in $common.data.programs" :class="{active: form.program_id==program.id}">
                        <input type="radio" v-on:click="selectProgram(program)" autocomplete="off" v-model="formData.program" style="display: none" :value="program" >{{ program.name }}
                    </label>
                </div>
                <form-error style="display: block" v-if="errors && errors.program_id" :errors="errors">
                    {{ errors.program_id[0] }}
                </form-error>
            </div>
        </div>
        <div v-bind:class="{ 'has-error': errors && errors.level_id }" class="form-group row">
            <label class="col-3 col-form-label">Уровень языка</label>
            <div class="col-9">
                <div class="btn-group btn-group-sm btn-group-justified">
                    <label class="btn btn-primary" v-for="level in $common.data.levels" :class="{active: form.level_id==level.id}">
                        <input type="radio" v-on:click="selectLevel(level)" autocomplete="off" v-model="formData.level" :value="level" style="display: none">{{ level.short_name }}
                    </label>
                </div>
                <form-error style="display: block" v-if="errors&& errors.level_id" :errors="errors">
                    {{ errors.level_id[0] }}
                </form-error>
            </div>
        </div>

<!--
        <draggable v-model="formData.items" :options="{draggable:'.item'}">

            <div class="form-group mb-2 item" v-for="(item, index) in formData.items" :key="item.id">
                <div class="input-group">
                    <div class="input-group-addon">{{ index+1 }}</div>
                    <input v-model="item.title" class="form-control" type="text" placeholder="укажите тему занятия">
                    <div class="input-group-btn">
                        <button @click="deleteItem(index)" class="btn btn-danger" type="button"><span class="fa fa-fw fa-trash"></span></button>
                    </div>
                </div>
            </div>

            <form-error v-if="errors && errors.items" :errors="errors">
                Нужно добавить занятия
            </form-error>

            <div slot="footer" class="row">
                <div class="col-12">
                    <button @click="addItem()" class="btn btn-success">Добавить занятие</button>
                </div>
            </div>

        </draggable>
-->

        <div slot="modal-footer">
            <button type="button" class="btn btn-primary" @click="sendForm();" :disabled="formSending? true : false">
                <i v-show="formSending" class="fa fa-spinner fa-spin"></i>
                Сохранить шаблон
            </button>
        </div>

    </b-modal>

</template>


<script>

    import { get, post } from '../../helpers/api'

    export default {

        props: ['form'],

        data() {

            return {
                formData: '',
                options: [],
                errors : [],
                formSending : false,
                cacheOptions: []
            }

        },

        watch: {
            form(form) {
                this.formData = form;
            }
        },

        components: {
            FormError : require('./../../components/FormError.vue')
        },
        methods: {

            sendForm() {

                this.formSending = true;

                let _this = this;

                post(_this, '/api/lesson-template-save', this.formData, function (response) {

                    _this.formSending = false;
                    _this.errors = '';
                    _this.hideModal();
                    _this.$emit('formSending');
                    if(response.data) _this.$router.push({ name: 'lesson_template', params:{id: response.data} });

                }, function (error) {

                    _this.formSending = false;
                    _this.errors = error.response.data;
                });

            },

            addItem() {

                this.formData.items.push({
                    name: ''
                });

            },

            deleteItem(index) {

                this.formData.items.splice(index, 1);

            },


            showModal() {
                this.$refs.modal.show();
            },
            hideModal() {
                this.$refs.modal.hide();
            },
            selectProgram(val){
                if(val) {
                    this.formData.program_id = val.id;
                    this.formData.program = val;
                }

            },

            selectLevel(val){
                if (val) {
                    this.formData.level_id = val.id;
                    this.formData.level = val;
                }
            },

        },

        created() {


        }


    }


</script>

<style>
    .flip-list-move {
        transition: transform 0.5s;
    }
    .no-move {
        transition: transform 0s;
    }
    .ghost {
        opacity: .5;
        background: #C8EBFB;
    }
    .list-group {
        min-height: 20px;
    }
    .list-group-item {
        cursor: move;
    }
    .list-group-item i{
        cursor: pointer;
    }
</style>