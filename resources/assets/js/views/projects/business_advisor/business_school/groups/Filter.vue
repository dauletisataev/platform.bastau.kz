<template>
    <!-- Поиск -->
    <div class="col-10 offset-2 fixed-top pt-4">
        <div class="row">
            <div class='input-group col-10'>
                <input v-model="filterData.search_text" type="text" class="form-control form-control-sm" :placeholder='$tc("groups.filter.name_or_phone")'>
                <div class="input-group-append">
                    <button @click="clearListLoad()" :disabled="load" class="btn btn-primary " >{{$tc("participants.filter.apply")}}</button>
                </div>
            </div>
            <div class="col-2">
                <button @click="resetFilter()" :disabled="load" class="btn btn-secondary  btn-block">{{$tc("groups.filter.reset")}}</button>
            </div>
            <div class="col-12 pt-3 ">
                    <div class="row">
                        <div class="col-3">
                            <button v-if="filterData==='current' "class="btn btn-secondary active" @click = "changeGroupType('current')">{{$tc('groups.filter.current')}}</button>
                            <button v-else class="btn btn-secondary" @click = "changeGroupType('current')">{{$tc('groups.filter.current')}}</button>
                        </div>
                        <div class="col-3">
                            <button v-if="filterData==='without_participants'"class="btn btn-secondary active" @click = "changeGroupType('without_participants')">{{$tc('groups.filter.without_participants')}}</button>
                            <button v-else class="btn btn-secondary" @click = "changeGroupType('without_participants')">{{$tc('groups.filter.without_participants')}}</button>
                        </div>
                        <div class="col-3">
                            <button v-if="filterData==='inactive'" class="btn btn-secondary active" @click = "changeGroupType('inactive')">{{$tc('groups.filter.inactive')}}</button>
                            <button v-else class="btn btn-secondary" @click = "changeGroupType('inactive')">{{$tc('groups.filter.inactive')}}</button>
                        </div>
                        <div class="col-3">
                            <button v-if="filterData==='partial'" class="btn btn-secondary active" @click = "changeGroupType('partial')">{{$tc('groups.filter.partial')}}</button>
                            <button v-else class="btn btn-secondary" @click = "changeGroupType('partial')">{{$tc('groups.filter.partial')}}</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: ['load'],

        data() {
            return {
                filterData: {
                    search_text: '',
                    group_type:'current'
                },
                temp: {
                },
            }

        },

        mounted(){
            this.$nextTick(function () {
                this.setFiltered(this.$route.query);
            });
        },

        methods: {
            changeGroupType(val){
                this.filterData.group_type=val;
                this.clearListLoad();
            },
            resetFilter(){
                this.filterData.search_text = '';
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
                        if(filterKey == queryKey) {
                            if(this.filterData[filterKey].constructor === Array) {
                                this.filterData[filterKey].push(query[queryKey]);
                            } else {
                                this.filterData[filterKey] = query[queryKey];
                            }

                        }
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
            selected(key, options, ids)
            {
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