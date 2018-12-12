<template>
    <div class="col-10 offset-2 h-100 pt-4 nav-justified">
        <div class="row">
            {{lesson.title}}
        </div>

        <div class="btn-group container mt-4 mb-4 row">
            <div class="btn btn-default" @click="setTab('materials')">{{$tc('materials')}}</div>
        </div>
        <div>
            <materials v-if="lesson && active_tab==='materials'" :lesson="lesson"  :students="lesson.group.participants" />
        </div>
    </div>
</template>
<script>
    import {get} from"../../../helpers/api";
    export default{
        data(){
            return {
                lesson:"",
                loading:false,
                id:this.$route.params.id,
                active_tab:""
            }
        },
        components:{
            "materials":require("./Materials.vue")
        },
        methods:{
            getItem(){
                let _this=this;
                get(_this, '/api/participant-lesson/'+this.id,{},function(responce){
                    _this.lesson=responce.data;
                },function () {

                })
            },
            setTab(element){
                this.active_tab = element;
                this.$router.push({ path: '/role-participant/lesson/'+this.id, query: {page:element} });
            }
        },
        created(){
            if(!this.$route.query.page){
                this.setTab("materials")
            }else{
                this.active_tab=this.$route.query.page;
            }
            this.getItem();
        }
    }
</script>