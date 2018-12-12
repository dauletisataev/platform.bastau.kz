<template>
    <!-- Поиск -->
    <div class="col-10 offset-2  pt-4 mb-3">
        <div class="row">
            <div class="col-2">
                <select class="form-control"v-model="filterData.month" @change="clearListLoad" >
                    <option value="">{{$tc('holidays.months.select')}}</option>
                    <option value="1">{{$tc('holidays.months.january')}}</option>
                    <option value="2">{{$tc('holidays.months.february')}}</option>
                    <option value="3">{{$tc('holidays.months.march')}}</option>
                    <option value="4">{{$tc('holidays.months.april')}}</option>
                    <option value="5">{{$tc('holidays.months.may')}}</option>
                    <option value="6">{{$tc('holidays.months.june')}}</option>
                    <option value="7">{{$tc('holidays.months.july')}}</option>
                    <option value="8">{{$tc('holidays.months.august')}}</option>
                    <option value="9">{{$tc('holidays.months.september')}}</option>
                    <option value="10">{{$tc('holidays.months.october')}}</option>
                    <option value="11">{{$tc('holidays.months.november')}}</option>
                    <option value="12">{{$tc('holidays.months.december')}}</option>
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
                    search_text: '',
                    month:""
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

            }
        }

    }
</script>
