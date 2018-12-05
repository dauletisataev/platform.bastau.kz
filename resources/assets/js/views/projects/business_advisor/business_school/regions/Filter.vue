<template>
    <!-- Поиск -->
    <div class="col-10 offset-2 pt-4 mb-4">
        <div class="row">
            <div class="col-3 ">
                <select class="form-control" v-model="filterData.region" @change="clearListLoad('reg')" >
                    <option default value="">{{$tc('regions.select_region')}}</option>
                    <option v-for="region in $common.data.regions" :value="region.name"> {{region.name}}</option>
                </select>
            </div>
            <template v-for="region in $common.data.regions" >
                <div v-if="region.name===filterData.region" class="col-3" >
                <select  class="form-control"  v-model="filterData.district"  @change="clearListLoad('distr')">
                    <option default value="">{{$tc('regions.select_district')}}</option>
                    <option v-for="district in region.districts" :value="district.name">{{district.name}}</option>
                </select>
                </div>
            </template>
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

        }

    }
</script>
