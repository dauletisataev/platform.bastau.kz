<template>
    <div class="container-fluid pt-2">
        <group-filter v-if="$common.data.roles" ref="filter" :load="load" v-on:filtered="filtered"></group-filter>
        <div class="col-10 offset-2 ">
            <hr/>
            {{ $tc('groups.list.found_number') }} {{ total }}
            <button class="btn btn-primary float-right" @click="$refs.createGroupModal.showModal()">{{$tc('groups.list.create_group')}}</button>
            <group-form ref="createGroupModal"  v-on:updated="getList"/>
            <div>
                <table class="table mb-0">
                    <tr>
                        <td>{{$tc('groups.list.table.project')}}</td>
                        <td>{{$tc('groups.list.table.advisor')}}</td>
                        <td>{{$tc('regions.region')}}</td>
                        <td>{{$tc('regions.district')}}</td>
                        <td>{{$tc('regions.locality')}}</td>
                        <td>{{$tc('groups.list.table.participants_number')}}</td>
                        <td>{{$tc('groups.list.table.next_lesson')}}</td>
                    </tr>
                <tr v-for="item in groups">
                    <td>{{item.project_id}}</td>
                    <td>{{item.trainer_id}}</td>
                    <td>{{item.locality.district.region.name}}</td>
                    <td>{{item.locality.district.name}}</td>
                    <td>{{item.locality.name}}</td>
                    <td>{{item.participants.length}}/{{item.capacity}}</td>
                    <td>{{item.next_lesson}}</td>
                    <td class="row pull-right" >
                            <b-tooltip  :title="$tc('groups.list.get_info')" placement="left">
                                <router-link :to="{name:'group', params:{id: item.id}}"
                                             class="btn btn-outline-primary btn-sm">
                                    <span class="fa fa-eye"></span></router-link>
                            </b-tooltip>
                            <b-dropdown size="sm" :text="$tc('groups.list.edit')" right>
                                <b-dropdown-item>{{$tc('groups.list.options.cancel_lesson')}}</b-dropdown-item>
                                <b-dropdown-item><router-link :to="{name:'add-existing-participant-to-group', params:{id:item.id}}">{{$tc('groups.list.options.add_participant')}}</router-link></b-dropdown-item>
                            </b-dropdown>
                    </td>
                </tr>
                </table>
            </div>
        </div>

    </div>
</template>

<script>

    import { get } from '../../../../../helpers/api'

    export default {
        data() {
            return {
                load: false,
                scrollLoad: false,
                newGroup: '',
                groups: [],
                total: 0,
                resource_url: '/api/groups',
                next_url: '',
                default_url: '/api/groups'
            }
        },
        components: {
            'group-filter': require('./Filter.vue'),
            'group-form':require('./Form.vue')
        },
        methods: {
            getList(type) {
                if(type!=="old")this.groups=[];
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;
                get(_this, this.resource_url, {params: this.filterData}, function (response) {

                    let json = response.data;

                    _this.next_url = json.next_page_url;

                    _this.groups = _this.groups.concat(json.data);

                    _this.total = json.total;

                    _this.scrollLoad = false;
                    _this.load = false;
                    console.log(this.groups);
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;

                });

            },
            filtered() {
                this.resource_url = this.default_url;
                this.groups = [];
                this.total = 0;
                this.filterData = this.$refs.filter.filterData;

                this.$nextTick(function () {
                    this.$router.push({ path: '/groups', query: this.filterData });
                    this.getList();
                });

            },
            handleScroll(e){
                let body = document.body,
                    html = document.documentElement;

                let height = Math.max( body.scrollHeight, html.scrollHeight);

                if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !this.scrollLoad) {
                    this.scrollLoad = true;
                    this.$nextTick(function () {
                        this.getList("old");
                    })
                }
            }
        },
        created() {
            window.document.body.onscroll = this.handleScroll;
        }
    }

</script>
