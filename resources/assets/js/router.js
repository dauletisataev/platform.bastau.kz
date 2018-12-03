import VueRouter from 'vue-router';

const Dashboard = require('./views/dashboard/Index.vue');
const Control = require('./views/common/Control.vue');
const Users = require('./views/users/List.vue');

const User = require('./views/users/Item.vue');
const PermissionDenied = require('./views/system/PermissionDenied.vue');
const NotFound = require('./views/system/NotFound.vue');

const Login = require('./views/common/Login.vue');
const SelectAccount = require('./views/common/SelectAccount.vue');
const ResetPass = require('./views/common/ResetPass.vue');

const LessonTemplates = require('./views/lesson-templates/Index.vue');
const LessonTemplate = require('./views/lesson-templates/Item.vue');
const LessonTemplateContent = require('./views/lesson-templates/Content/Item.vue');
const LessonTemplateNew = require('./views/lesson-templates/NewForm.vue');
const LessonContent = require('./views/lessons/Item.vue');

const Participants = require('./views/projects/business_advisor/business_school/participants/List.vue');
const Participant = require('./views/projects/business_advisor/business_school/participants/Item.vue');

const Group = require('./views/projects/business_advisor/business_school/groups/Item.vue');
const Groups = require('./views/projects/business_advisor/business_school/groups/List.vue');
const AddExistingParticipantToGroup = require("./views/projects/business_advisor/business_school/groups/add_participants/add_existing_participants.vue");
export default new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        { name:'login', path: '/login', component: Login, meta: {title: 'Авторизация', forVisitors: true} },
        { name: 'select-account', path: '/select-account', component: SelectAccount, meta: {title: 'Выберите ученика', forAuth: true}},
        { name: 'reset-pass', path: '/reset-pass', component: ResetPass, meta: {title: 'Восстановление пароля'}},
        { path: '/', component: Dashboard, meta: {title: 'Показатели', forAuth: true} },
        { name: 'dashboard', path: '/dashboard', component: Dashboard, meta: {title: 'Показатели', forAuth: true} },
        { name: 'control', path: '/control', component: Control, meta: {title: 'Управление', forAuth: true} },
        { name: 'users', path: '/control/users', component: Users, meta: {title: 'Пользователи', forAuth: true} },
        { path: '/control/user/:id', name: 'user', component: User, props:true, meta: {title: 'Пользователь', forAuth: true} },
        { path: '/401', component: PermissionDenied},
        { path: '*', component: NotFound},
        { name: 'lesson_template_new', path: '/control/add_lesson_template', component: LessonTemplateNew, meta: {title: 'Программы', forAuth: true, forTeacher: true}},
        { name: 'lesson_templates', path: '/control/lesson-templates', component: LessonTemplates, meta: {title: 'Программы', forAuth: true, forTeacher: true} },
        { name: 'lesson_template', path: '/control/lesson-templates/:id', component: LessonTemplate, props:true, meta: {title: 'Программа', forAuth: true, forTeacher: true} },
        { name: 'lesson_template_content', path: '/control/lesson-templates/:template_id/lesson/:id', component: LessonTemplateContent, props: true, meta: {title: 'Занятие', forAuth: true, forTeacher: true}},
        {
            name: 'participants',
            path: '/participants',
            props: true,
            component:Participants,
            meta:{
                title:"Участники",
                forAuth:true
            }
        },
        {
            name: 'participant',
            path: '/participant/:id',
            props: true,
            component:Participant,
            meta:{
                title:"Участник",
                forAuth:true
            }
        },
        {
            name: 'groups',
            path: '/groups',
            props: true,
            component:Groups,
            meta:{
                title:"Группы",
                forAuth:true
            }
        },
        {
            name: 'group',
            path: '/group/:id',
            props: true,
            component:Group,
            meta:{
                title:"Группа",
                forAuth:true
            }
        },
        {
            name: 'add-existing-participant-to-group',
            path: '/group/:id/add-existing-participant-to-group',
            props: true,
            component:AddExistingParticipantToGroup,
            meta:{
                title:"Добавление существующего пользователя в группу",
                forAuth:true
            }
        },
    ]

});
