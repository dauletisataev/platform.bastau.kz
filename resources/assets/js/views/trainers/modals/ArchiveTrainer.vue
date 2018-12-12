<template>
    <b-modal ref="modalArchive" title="Архив" v-bind:class="{ 'has-error': errors && errors.id }">
        Вы действительно хотите {{ trainer.in_archive ? 'востановить' : 'переместить'}} преподавателя {{ trainer.in_archive ? 'из архива' : 'в архив'}}?
        <div v-if="!trainer.in_archive" class="form-group row mt-2 mb-0" >
            <label class="col-3 col-form-label">Категория</label>
            <div class="col-9">
                <select class="form-control" v-model="selected_reason" >
                            <option value="0">--Выберите причину--</option>
                            <option v-for="item in $common.data.archived_reasons" v-if="item.role_id == 3" :value="item.id">{{item.reason}}</option>
                </select>                        
                <div v-if="errors.length>0" class="danger">
                    <p v-for="error in errors">
                        {{ error }}
                    </p>
                </div>
            </div>
        </div>
        <div v-if="!trainer.in_archive" class="form-group row">
            <label class="col-3 col-form-label">Описание</label>
            <div class="col-9">
                <div class="mt-2">
                    <textarea required="true" v-model="description" class="form-control" placeholder="Укажите описание"></textarea>
                </div>
            </div>
        </div>
        <div slot="modal-footer">
            <button type="button" class="btn btn-secondary" @click="$refs.modalArchive.hide()">Отменить</button>
            <button type="button" class="btn" v-bind:class="{ 'btn-success': trainer.in_archive, 'btn-danger': !trainer.in_archive }" @click="archive()" >  Подтвердить</button>
        </div>
    </b-modal>
</template>
<script>
export default {
    data() {
        return {
            errors: [],
            trainer: {},
            form: '', 
            selected_reason: 0,
            description:'',
        }
    },
    methods:{
        showModal(){
            this.$refs.modalArchive.show();
        },
        hideModal(){
            this.$refs.modalArchive.show();
        },
        archive(){
            if(this.selected_reason == 0) {
                this.errors.push('Выберите причину');
            }else
                this.$emit('archive', this.selected_reason);
        }
        
    }
}
</script>

