<template>
    <!-- Yersultan
        There would be history of the group
    -->
    <div>
        <div class="h4">{{$tc('group.item.history_tab.group_history')}}</div>
        <div v-for="action in histories">
            {{action.created_at}}{{action.action}}
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