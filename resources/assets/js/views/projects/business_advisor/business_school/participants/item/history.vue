<template>
    <div>
        <div class="h4">{{$tc('participants.item.history_tab.participant_history')}}</div>
        <div v-for="action in histories">
            <div>
                {{action.created_at}}:{{action.action}}
                <span v-if="action.action ==='Участник архивирован'">
                    по причине:{{getArchivedReasonName($common.data.archived_reasons,action.archive_reason_id)}}
                </span>
                <span v-if="action.action ==='Изменена персональная информация:'">
                     {{action.field}} c {{action.old_value}} на {{action.new_value}}
                </span>
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