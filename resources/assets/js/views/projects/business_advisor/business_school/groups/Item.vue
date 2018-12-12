<!--    Ерсултан
    Тэмплейт для содержания контейнеров от группы
-->
<template>
    <div >
        <div class="col-10 offset-2 h-100 pt-4 nav-justified">
            <b-tabs>
                <b-tab :title="$tc('groups.item.group_info')" >
                    <group-info v-on:updated="updateData" :group="group"/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_statistics')" >
                    <group-statistics statistics=""/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_participants')"  >
                    <group-participants v-on:updated="updateData" :participants="group.participants" :id="id"/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_lessons')" >
                    <group-lessons v-on:show_import_lessons_modal="$refs.importLessons.showModal()" :lessons="group.lessons"/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_history')">
                    <group-history :histories="group.histories"/>
                </b-tab>
            </b-tabs>
            <import-lesson-template v-on:updated="updateData" :lessons="group.lessons" ref="importLessons" :group_id="group.id" :pseudotimes="pseudotimes" :online="group.online" :language="group.language" :project_id="group.project_id"/>
        </div>
    </div>

</template>

<script>

    import { get,post } from '../../../../../helpers/api';
    export default {

        props: ['id','user_id'],

        data() {
            return {
                errors: [],
                group: '',
                pseudotimes:[]
            }
        },

        components: {
            'group-history':require('./item/history.vue'),
            'group-info':require('./item/info.vue'),
            'group-statistics':require('./item/statistics.vue'),
            'group-participants':require('./item/participants.vue'),
            'group-lessons':require('./item/lessons.vue'),
            "import-lesson-template":require('./modals/import_lesssons.vue')
        },

        methods: {
            resetEditorsExcept(value) {
                if(value !== 'editName') {
                    this.$emit('resetEditor');
                }
                for(let key in this.$refs) {
                    if(key !== value && key !== 'editSchedules' && key !== 'editSocials') {
                        if(this.$refs.hasOwnProperty(key)) {
                            this.$refs[key].value = this.$refs[key].defaultValue;
                            this.$refs[key].mode = 'view';
                        }
                    }
                }
            },
            getItem() {
                let _this = this;
                get(_this, '/api/group/' +  this.id, {}, function (response) {
                    _this.group = response.data;
                });
                get(_this,'/api/lessons/get-pseudotime/'+this.id,{},function (responce) {
                    _this.pseudotimes = responce.data;
                })
            },
            updateData(){
                console.log("updated");
                this.getItem();
            },

        },

        created() {
            console.log("updated");
            this.getItem();
        }

    }


</script>
