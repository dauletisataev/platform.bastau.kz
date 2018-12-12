<template>
    <div class="col-10 offset-2 h-100 pt-4 nav-justified">
        <template v-if="total===0">NO groups</template>
        <template v-if="total===1"></template>
        <template v-if="total>1">Groups list</template>
        <!---->
        <div class="container-fluid table">
            <select v-if="groups.length>1" v-model="selected_group">
                <option v-for="group in groups" :value="group">{{group.created_at}}</option>
            </select>
            <div class="row" v-for="lessons in selected_group.lessons">
                <router-link :to="{ name: 'participant-lesson', params: { id: lessons.id }}">{{lessons.title}}</router-link>
            </div>
        </div>
    </div>
</template>
<script>
    import {get} from "../../../helpers/api";
    export default{
        data(){
            return {
                groups: [],
                resource_url: '/api/participant-groups',
                next_url: '',
                filterData:{
                    "user_id":this.$user.data.id
                },
                default_url: '/api/participant-groups',
                scrollLoad: false,
                selected_group:"",
                load: false,
                total: 0,
            }
        },
        methods:{
            //Yersultan The data will be given according to IIN
            getList() {
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
                    _this.total = _this.groups.length;
                    if(_this.total>0)_this.selected_group=_this.groups[0];
                    _this.scrollLoad = false;
                    _this.load = false;
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;
                });

            }
        },
        created(){
            this.getList();
        }

    }
</script>