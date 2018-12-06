<template>
    <!-- Поиск -->
    <div class="col-10 offset-2 pt-4 mb-4">
        <div class="row">
            <div class="col-3 ">
                <select class="form-control" v-model="filterData.region" @change="clearListLoad('reg')" >
                    <option default value="">{{$tc('regions.select_region')}}</option>
                    <option v-for="region in $common.data.regions" :value="region.id" :selected="region.id===filterData.region"> {{region.name}}</option>
                </select>
            </div>
            <div v-if="filterData.region!==''" class="col-3" >
                <select  class="form-control"  v-model="filterData.district"  @change="clearListLoad('distr')">
                    <option default value="">{{$tc('regions.select_district')}}</option>
                    <option v-for="district in $common.data.districts" v-if="district.region_id===filterData.region" :value="district.id">{{district.name}}</option>
                </select>
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
                    region:"",
                    district:""
                },
            }

        },

        mounted(){
            this.$nextTick(function () {
                this.setFiltered(this.$route.query);
            });
        },

        methods: {
            clearListLoad(type){
                if(type==="reg")this.filterData.district="";
                this.$nextTick(function () {
                    this.$emit('filtered');
                });

            },
            setFiltered(query){
                for (let filterKey in this.filterData) {
                    for(let queryKey in query){
                        if(filterKey == queryKey) {
                            if(queryKey==="region")queryKey="regions";
                            if(queryKey==="district")queryKey="districts";
                            if(queryKey==="locality")queryKey="localities";
                            this.$common.data[queryKey].forEach((item)=>{
                                if(item.id ==query[filterKey]){
                                    if(this.filterData[filterKey].constructor === Array) {
                                        this.filterData[filterKey].push(item.id);
                                    } else {
                                        this.filterData[filterKey] = item.id;
                                    }
                                }
                            });
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

        }

    }
</script>
