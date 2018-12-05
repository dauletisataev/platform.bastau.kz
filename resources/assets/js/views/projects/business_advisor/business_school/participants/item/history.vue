<template>
    <div class="mt-4">
        <div class="h4">{{$tc('participants.item.history_tab.participant_history')}}</div>
        <div v-for="action in histories">
            <div>
                {{action.created_at}}:{{$tc("participants.item.history_tab."+action.action)}}
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props:['histories'],
        methods:{
            getArchivedReasonName(array,id){
                for (var i = 0; i < array.length; i++) {
                    if(array[i].id*1 ===id){
                        return array[i].reason;
                    }
                }
            }
        }
    }
</script>