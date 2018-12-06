<template>
    <!-- Yersultan
        There would be history of the group
    -->
    <div>
        <div class="h4">{{$tc('groups.item.history_tab.group_history')}}</div>
        <div v-for="action in histories">
            {{action.created_at}}{{$tc("groups.item.history_tab."+action.action)}}
        </div>
    </div>
</template>
<script>
    export default {
        props:['histories'],
        methods:{
        }
    }
</script>