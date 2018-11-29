<template>
    <div class="w-75 m-auto">
        <editor :id="'quillContainer'" :text="lesson.manual" @update="update"></editor>
        <a href="javascript:void(0)" @click="$emit('save')" class="rounded h2 p-3 bg-primary text-white rounded-circle" style="position:fixed;right:20px;bottom:20px;"><div class="fa fa-fw fa-save"></div></a>
    </div>
</template>
<script>
    export default {
        props: ['lesson'],
        data() {
            return {
            }
        },
        components: {
            'editor': require('../Editor.vue'),
        },
        methods: {
            update(text) {
                this.lesson.manual = text;
            }
        },
    }
</script>