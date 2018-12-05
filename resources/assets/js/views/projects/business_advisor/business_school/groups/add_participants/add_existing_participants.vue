<template>
    <div  class="col-10 offset-2">
        <table class="table mt-5">
            <tr>
                <th></th>
                <th> {{$tc('participants.list.first_name')}}</th>
                <th> {{$tc('participants.list.last_name')}}</th>
                <th> {{$tc('participants.list.iin')}}</th>
            </tr>
            <tr v-for="participant in participants" >
                <td><input type="checkbox" :id="participant.id" :value="participant.id  " v-model="selected_participants" /></td>
                <td>{{participant.user.first_name}}</td>
                <td>{{participant.user.last_name}}</td>
                <td>{{participant.user.iin}}</td>
            </tr>
        </table>
        <div class="p-2 bg-faded" style="position: fixed; right: 20px; bottom: 20px;">
            <button class="btn btn-lg btn-secondary mr-2" >Отменить</button>
            <button class="btn btn-lg btn-primary" @click="addParticipants"><span class="fa fa-check"></span> Готово</button>
        </div>
    </div>
</template>
<script>
    import { get,post } from '../../../../../../helpers/api';
    export default{
        props:["id","members"],
        data(){
            return {
                load: false,
                scrollLoad: false,
                newGroup: '',
                participants: [],
                total: 0,
                resource_url: '/api/groups/'+this.id+'/add-participants',
                next_url: '',
                default_url: '/api/groups/'+this.id+'/add-participants',
                selected_participants:[],
                not_members:[]
            }
        },
        components:{
        },
        methods:{
            getList() {
                this.resource_url = this.scrollLoad ? this.next_url : this.resource_url;
                if (!this.resource_url){
                    this.scrollLoad = false;
                    return false;
                }
                this.load = true;
                let _this = this;
                get(_this, this.resource_url, {}, function (response) {

                    let json = response.data;
                    _this.next_url = json.next_page_url;

                    _this.participants = _this.participants.concat(json.data);
                    _this.total = json.total;

                    _this.scrollLoad = false;
                    _this.load = false;
                }, function () {
                    _this.scrollLoad = false;
                    _this.load = false;

                });

            },
            addParticipants(){
                let _this=this;
                let form = {
                    participants:_this.selected_participants,
                };
                post(_this, '/api/group/'+this.id+'/add-participants', form, function () {
                    _this.formSending = false;
                    _this.errors = '';
                    _this.$emit('formSending');
                    _this.$router.push({name:"group",params:{id:_this.id}})
                }, function (error) {
                    _this.formSending = false;
                    _this.errors = error.response.data;
                });
            },
        },
        created(){
            this.getList();
        }
    }
</script>