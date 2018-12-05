<template>
    <div class="container-fluid pt-2">
        <regions-filter  v-if="$common.data" ref="filter"  v-on:filtered="filtered"></regions-filter>
        <div class="col-10 offset-2 ">
            <regions-form v-on:updated="UpdateCommonData" ref="regionsFormModal"/>
        <div class="mb-2">
            <div v-if="show==='localities'">
                <div class="h3">{{$tc('regions.localities_list_of')}}{{filterData.district}}
                    <button class="btn btn-sm btn-primary pull-right" @click="$refs.regionsFormModal.showModal('locality',filterData)">{{$tc('regions.add_locality')}}</button>
                </div>
            </div>
            <div v-else-if="show==='districts' ">
                <div class="h3">{{$tc('regions.districts_list_of')}}{{filterData.region}}
                    <button class="btn btn-sm btn-primary pull-right" @click="$refs.regionsFormModal.showModal('district',filterData)">{{$tc('regions.add_district')}}</button>
                </div>
            </div>
            <div v-else>
                <div class="h3">{{$tc('regions.regions_list')}}
                 <button class="btn btn-sm btn-primary pull-right" @click="$refs.regionsFormModal.showModal('region')">{{$tc('regions.add_region')}}</button>
                </div>
            </div>
        </div>
            <ul v-if="show==='regions'" class="list-group list-group-flush">
                <li  v-for="region in $common.data.regions" class="list-group-item" style="cursor:default">
                        <div class="col-10">
                            <span @click = "$refs.filter.filterData.region=region.name;filtered()">{{region.name}}</span>
                        </div>
                        <div class="col-2 text-right">
                            <button @click="$refs.editmodal.showModal('region',region)" class="btn btn-sm btn-secondary">
                                <span class="fa fa-pencil"></span>
                            </button>
                            <button class="btn btn-sm btn-danger"@click="remove('region',region.id)">
                                <span class="fa fa-trash"></span>
                            </button>

                        </div>
                </li>
            </ul>
            <div v-else v-for="region in $common.data.regions">
                <ul v-if="show==='districts' && region.name===filterData.region " class="list-group list-group-flush">
                    <li v-for="district in region.districts" class="list-group-item"  style="cursor:default">
                        <div class="col-10">
                            <span @click = "$refs.filter.filterData.district=district.name;filtered()">{{district.name}}</span>
                        </div>
                        <div class="col-2 text-right">
                            <button class="btn btn-sm btn-secondary" @click="$refs.editmodal.showModal('district',district)">
                                <span class="fa fa-pencil" ></span>
                            </button>
                            <button @click="remove('district',district.id)" class="btn btn-sm btn-danger">
                                <span class="fa fa-trash"></span>
                            </button>


                        </div>
                    </li>
                </ul>
                <div v-else-if="show==='localities'">
                    <div v-for="district in region.districts">
                        <ul v-if="district.name ===filterData.district"  class="list-group list-group-flush">
                            <li v-for="locality in district.localities" class="list-group-item" style="cursor:default">
                                <div class="col-10">
                                    {{locality.name}}
                                </div>
                                <div class="col-2 text-right">
                                    <button class="btn btn-sm btn-secondary" @click="$refs.editmodal.showModal('locality',locality)" >
                                        <span class="fa fa-pencil" ></span>
                                    </button>
                                    <button @click="remove('locality',locality.id)" class="btn btn-sm btn-danger">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <regions-editor ref="editmodal"/>
    </div>
</template>

<script>
    import { post } from '../../../../../helpers/api'

    export default {
        data() {
            return {
                show:"regions",
                localities:[],
                filterData:{}
            }
        },
        components: {
            'regions-filter': require('./Filter.vue'),
            'regions-form':require('./Form.vue'),
            'regions-editor':require('./modals/Editor.vue')
        },
        methods: {
            filtered() {
                this.filterData = this.$refs.filter.filterData;
                console.log(this.filterData);
                if(this.filterData.district!=="" && this.filterData.region!==""){
                    this.show="localities";
                }
                else if(this.filterData.district==="" && this.filterData.region!==""){
                    this.show="districts";
                }
                else {
                    this.show="regions";
                }
                this.$nextTick(function () {
                    this.$router.push({ path: '/control/regions', query: this.filterData });
                    this.$common.getData();
                });
            },
            handleScroll(e){
            },
            UpdateCommonData(){
                this.$common.getData();
            },
            remove(type,id){
                let form={};
                form.type=type;
                let _this=this;
                post(_this, '/api/region/delete/'+id, form, function () {
                    _this.$common.getData();
                }, function (error) {
                });
            }
        },
        created() {
            window.document.body.onscroll = this.handleScroll;
        }
    }

</script>
