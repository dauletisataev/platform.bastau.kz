<!--    Ерсултан
    Тэмплейт для содержания контейнеров от группы
-->
<template>
    <div >
        <div class="col-10 offset-2 h-100 pt-4 nav-justified">
            <b-tabs>
                <b-tab :title="$tc('groups.item.group_info')" active>
                    <group-info v-on:updated="updateData" :group="group"/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_statistics')" active>
                    <group-statistics statistics=""/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_participants')" active>
                    <group-participants :participants="group.participants" :id="id"/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_lessons')" active>
                    <group-lessons lessons=""/>
                </b-tab>
                <b-tab :title="$tc('groups.item.group_history')">
                    <group-history :histories="group.histories"/>
                </b-tab>
            </b-tabs>
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
            }
        },

        components: {
            'group-history':require('./item/history.vue'),
            'group-info':require('./item/info.vue'),
            'group-statistics':require('./item/statistics.vue'),
            'group-participants':require('./item/participants.vue'),
            'group-lessons':require('./item/lessons.vue')
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
