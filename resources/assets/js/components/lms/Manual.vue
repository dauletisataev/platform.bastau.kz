<template>
    <div class="w-75 m-auto">
        <div class="alert alert-danger clearfix" v-if="manual==''">
            <div class="pull-left" style="font-weight:500">Методичка не добавлена</div>
        </div>
        <div class="ql-editor" v-html="manual"></div>
    </div>
</template>
<script>
    export default {

        props: ['manual'],

        data() {
            return {
            }
        },
    }
</script>