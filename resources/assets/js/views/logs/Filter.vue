<template>
    <!-- Поиск -->
    <div class="col-2 offset-2 fixed-top h-100 pt-4">
        <div class="form-group input-group input-group-sm">
            <date-range-picker v-model="filterData.dateRange"></date-range-picker>
            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
        </div>
        <div class="form_group">
            <button @click="clearListLoad()" :disabled="load" class="btn btn-primary btn-block" >Применить</button>
            <button @click="resetFilter()" :disabled="load" class="btn btn-secondary btn-block btn-sm">Сбросить фильтр</button>
        </div>
    </div>
</template>

<script>

    import { get } from '../../helpers/api'

    export default {

        props: ['load'],

        data() {

            return {

                users: [],
                filterData: {
                    user_id: '',
                    role_id: '',
                    dateRange: '',
                },
                temp: {
                    user: '',
                    role: ''
                }

            }

        },

        computed: {

            users_filtered: function() {
                var comp = this;
                return this.users.filter(function(user) {
                   return (comp.filterData.role_id && comp.filterData.office_ids.length===0) ? (user.role_id == comp.filterData.role_id) : (comp.filterData.office_ids.length>0 && !comp.filterData.role_id) ? comp.intersection(comp.filterData.office_ids,user.office_ids) : (comp.filterData.role_id && comp.filterData.office_ids.length>0) ? ((user.role_id == comp.filterData.role_id) && comp.intersection(comp.filterData.office_ids,user.office_ids)) : true;
                });
            }

        },
        watch:{
            'filterData.dateRange'(){
                this.$nextTick(function () {
                    this.setFiltered(this.$route.query);
                });
            }
        },

        components: {
            'date-range-picker': require('./../../components/DateRangePicker.vue')
        },

        mounted(){

            this.getUsers();

        },

        methods: {

            intersection(arr1,arr2) {
                let a = new Set(arr1);
                let b = new Set(arr2);
                let result = new Set([...a].filter(x => b.has(x)));
                return (!!result && result.size>0);
            },

            getUsers(){

                let _this = this;

                get(_this, '/api/users-all', {}, function (response) {
                    _this.users = response.data;
                });
            },
            resetFilter(){
                this.clearListLoad();
            },
            clearListLoad(){

                this.$nextTick(function () {
                    this.$emit('filtered');
                });

            },
            setFiltered(query){
                for (let filterKey in this.filterData) {
                    for(let queryKey in query){
                        if(filterKey == queryKey)
                            this.filterData[filterKey] = this.filterData[filterKey] ? this.filterData[filterKey] :
                                (query[queryKey] === 'false' ? false : (query[queryKey] === 'true' ? true : query[queryKey]));
                    }
                }
                this.$nextTick(function () {
                    this.setSelect();
                });
            },
            setSelect()
            {
                this.$nextTick(function(){
                    this.clearListLoad();
                })

            },
            selectedArray(key, options, ids) {
                let comp = this;
                ids.forEach(function (id) {
                    options.forEach(function(value) {
                        if(value.id == id) comp.temp[key].push(value);
                    });
                });
            },

        }

    }
</script>

