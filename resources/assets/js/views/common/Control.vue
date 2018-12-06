<template>

    <div class="col-10 offset-2" v-if="$root.user">
        <div class="h6 mb-3" style="color:#C6C6C6">сотрудники</div>
        <div class="row ml-0 mr-0">

            <div class="col-2 pr-1 pl-1 mb-2 text-center">
                <div class="card p-2 pt-4 pb-4">
                    <router-link to="/control/users" class="card-full-link"></router-link>
                    <div class="h3 mb-0"><span class="fa fa-user h6 mb-0"></span></div>
                    <div style="font-weight:500;margin-bottom: -6px;">Пользователи</div>
                    <div class="small" style="color:#C6C6C6">администраторы</div>
                </div>
            </div>
            <div class="col-2 pr-1 pl-1 mb-2 text-center">
                <div class="card p-2 pt-4 pb-4">
                    <router-link to="/control/logs" class="card-full-link"></router-link>
                    <div class="h3 mb-0"><span class="fa fa-list-alt h6 mb-0"></span></div>
                    <div style="font-weight:500;margin-bottom: -6px;">лог действий</div>
                    <div class="small" style="color:#C6C6C6">действия пользователей системы</div>
                </div>
            </div>
        </div>
        <div class="h6 mt-4 mb-3" style="color:#C6C6C6">обучение</div>
        <div class="row ml-0 mr-0">
            <div class="col-2 pr-1 pl-1 mb-2 text-center">
                <div class="card p-2 pt-4 pb-4">
                    <router-link to="/control/lesson-templates" class="card-full-link"></router-link>
                    <div class="h3 mb-0"><span class="fa fa-graduation-cap h6 mb-0"></span></div>
                    <div style="font-weight:500;margin-bottom: -6px;">программы</div>
                    <div class="small" style="color:#C6C6C6">стандарты занятий</div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    export default {



    }

</script>