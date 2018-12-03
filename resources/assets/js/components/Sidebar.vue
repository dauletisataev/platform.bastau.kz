<template>

    <div class="sidebar container-fluid mt-4">
        <div class="row">
            <div class="col-2 bg-faded fixed-top h-100 pt-4 text-white bg-inverse small" style="z-index:3003">
                <div class="clearfix">
                    <div class="pull-left mr-2">
                        <div
                                :style="{
                                    'background-image': 'url(' + $root.user.getPhotoThumb() + ')',
                                    'background-size': 'cover',
                                    'width': '36px',
                                    'height': '36px',
                                    'display': 'block',
                                    'border-radius': '18px',
                                    '-webkit-border-radius': '18px',
                                    '-moz-border-radius': '18px'
                                }"

                        >
                        </div>
                    </div>
                    <div class="pull-left">
                        {{ $root.user.getName() }}
                        <div class="small text-muted">
                            {{ $root.user.getRoleDescription() }}
                        </div>
                    </div>
                </div>

                <hr>

                <ul>
                    <li class="mb-2">
                        <router-link to="/dashboard" exact
                                     :class="{'text-muted': dashboard.indexOf($route.name) < 0,'text-white': dashboard.indexOf($route.name) > -1 }">
                            <span class="fa fa-fw fa-signal mr-2"></span>
                            {{ $t("sidebar.indicator") }}
                        </router-link>
                    </li>

                    <li class="mb-2">
                        <router-link to="/control" exact
                                     :class="{'text-muted': control.indexOf($route.name) < 0,'text-white': control.indexOf($route.name) > -1 }">
                            <span class="fa fa-fw fa-wrench mr-2"></span>
                            {{ $t("sidebar.control") }}
                        </router-link>
                    </li>

                    <li class="mb-2">
                        <router-link to="/trainers" exact
                                     :class="{'text-muted': trainers.indexOf($route.name) < 0, 'text-white': trainers.indexOf($route.name) > -1 }">
                            <span class="fa fa-fw fa-user mr-2"></span>
                            {{ $t("sidebar.trainer") }}
                        </router-link>
                    </li>
                </ul>
                <hr>
                <ul>
                    <li class="mb-2"><router-link to="/participants" :class="{'text-muted': participants.indexOf($route.name) < 0,'text-white': participants.indexOf($route.name) > -1 }" ><span class="fa fa-fw fa-users mr-2"></span> {{ $t("sidebar.participants") }}</router-link></li>
                    <li class="mb-2"><router-link to="/groups" :class="{'text-muted': groups.indexOf($route.name) < 0,'text-white': groups.indexOf($route.name) > -1 }" ><span class="fa fa-fw fa-users mr-2"></span> {{ $t("sidebar.groups") }}</router-link></li>
                </ul>
                <hr>
                <ul>
                    <li class="mb-2"><a @click="$refs.profile.showModal()" style="cursor: pointer;"
                                        class="text-muted"><span class="fa fa-fw fa-cog  mr-2"></span> {{
                        $t("sidebar.preferences") }}</a></li>
                    <li class="mb-2">
                        <div class="sidebar-logout">
                            <button class="text-muted pl-0" @click="logout"><span
                                    class="fa fa-fw fa-sign-out  mr-2"></span> {{ $t("sidebar.exit") }}
                            </button>
                        </div>
                    </li>
                </ul>

                <div class="form-group">
                    <select class="form-control" id="lang" name="lang" v-model="$i18n.locale">
                        <option v-for="(lang, i) in langs" :key="`Lang${i}`" :value="lang">{{ lang }}</option>
                    </select>
                </div>
            </div>
        </div>
        <profile ref="profile" :_form="$root.user.data"></profile>

    </div>

</template>


<script>
    export default {

        data() {
            return {
                dashboard: [
                    'dashboard',
                ],
                control: [
                    'control',
                    'users',
                    'user',
                ],
                langs: ['ru', 'kz'],
                participants:[
                    'participants'
                ],
                groups:[
                    'groups'
                ],
                trainers: ["trainers"],
            }
        },
        components: {
            Profile : require('./Profile.vue'),
        },
        methods: {
            getCsrf() {
                return Laravel.csrfToken;
            },

            logout() {

                if(this.$root.accounts.length > 1){

                    this.$router.push({name: 'select-account'});

                }else{
                    this.$auth.destroyToken();

                    this.$router.push({name: 'login'});

                }

                this.$root.user = '';



            }
        }
    };
</script>
