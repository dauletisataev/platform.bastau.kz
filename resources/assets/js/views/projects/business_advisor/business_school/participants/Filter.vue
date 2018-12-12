<template>
    <!-- Поиск -->
    <div class="col-10 offset-2  pt-4 mb-3">
    <div class="row">
            <div class='input-group col-8 mb-4'>
                <input v-model="filterData.search_text" type="text" class="form-control form-control-sm" :placeholder='$tc("participants.filter.Name_or_ID")'>
                <div class="input-group-append">
                    <button @click="clearListLoad()" :disabled="load" class="btn btn-primary " > {{$tc("participants.filter.apply")}}</button>
                </div>
            </div>
            <div class="col-2">
                <button @click="resetFilter()" :disabled="load" class="btn btn-secondary  btn-block">{{$tc("participants.filter.reset")}}</button>
            </div>
            <div class="col-2">
                <select @change="actionOptionChanged()" class="form-control" v-model="actionOption">
                    <option value="action">{{$tc('participants.action_options.action')}}</option>
                    <option value="select">{{$tc('participants.action_options.select')}}</option>
                </select>
            </div> 
 
        <div class="col-12 mb-2 ">
            <b-tabs>
                <b-tab :title='$tc("participants.filter.active")' active @click="changeArchiveType(false)" >
                </b-tab>
                <b-tab :title='$tc("participants.filter.archived")' @click="changeArchiveType(true)" >
                </b-tab>
            </b-tabs>
        </div>
    </div>
    </div>
</template>

<script>
    export default {

        props: ['load', 'actionOption'],

        data() {

            return {

                filterData: {
                    search_text: '',
                    is_archived:false
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
            changeArchiveType(val){
                this.filterData.is_archived=val;
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
            actionOptionChanged(){
                switch(this.actionOption){
                    case "action": 
                        this.$emit('changeMode', false);
                        console.log('selectMode', false);
                        break;
                    case "select": 
                        this.$emit('changeMode', true);
                        console.log('selectMode', true);
                        break;
                }
            }
        }

    }
</script>
