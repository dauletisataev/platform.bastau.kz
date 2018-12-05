<!--    Ерсултан
    Тэмплейт для содержания контейнеров от группы
-->
<template>
    <div >
        <div class="col-10 offset-2 h-100 pt-4 nav-justified">

        </div>
    </div>

</template>

<script>

    export default {

        props: ['id','user_id'],

        data() {
            return {
                errors: [],
                group: '',
            }
        },

        components: {
        },

        methods: {
            resetEditorsExcept(value) {
                if(value !== 'editName') {
                    this.$emit('resetEditor');
                }
                for(let key in this.$refs) {
                    if(key !== value && key !== 'editSchedules' && key !== 'editSocials') {
                        if(this.$refs.hasOwnProperty(key)) {
                            this.$refs[key].value = this.$refs[key].defaultValue;
                            this.$refs[key].mode = 'view';
                        }
                    }
                }
            },

        },

    }


</script>
