<template>
    <div class="row mb-4" v-if="lesson">
        <div class="col-9">
            <div class="alert alert-danger clearfix" v-if="lesson.pages.length==0">
                <div class="pull-left" style="font-weight:500">Материалы не добавлены</div>
            </div>
            <div class="card" v-show="lesson.pages.length>0">
                <div class="card-block content_text">
                    <div class="mb-5" v-for="material in ordered">
                        <div class="ql-editor" v-if="material.material_type_id==1" v-html="material.content">
                        </div>
                        <div class="alert alert-warning ql-editor" v-if="material.material_type_id==4" v-html="material.content">
                        </div>
                        <div v-if="material.material_type_id==2" class="card">
                            <div class="card-block p-0">
                                <div class="row" v-if="material.showAdditional">
                                    <div class="col-8">
                                        <div v-if="matchYoutubeUrl(material.content)" class="embed-responsive embed-responsive-16by9 rounded" id="video">
                                            <iframe class="embed-responsive-item" :src="'https://www.youtube.com/embed/'+matchYoutubeUrl(material.content)+'?controls=0&amp;showinfo=0&amp;rel=0'"></iframe>
                                        </div>
                                        <video width="100%" height="auto" v-else>
                                            <source :src="material.content"/>
                                        </video>
                                    </div>
                                    <p class="col-4" style="overflow-y: scroll; white-space: pre-line;" v-html="material.additional_content"></p>
                                </div>
                                <div class="row" v-else>
                                    <div class="col-12">
                                        <div v-if="matchYoutubeUrl(material.content)" class="embed-responsive embed-responsive-16by9 rounded" id="video">
                                            <iframe class="embed-responsive-item" :src="'https://www.youtube.com/embed/'+matchYoutubeUrl(material.content)+'?controls=0&amp;showinfo=0&amp;rel=0'"></iframe>
                                        </div>
                                        <video v-else controls  style="width: 100%">
                                            <source :src="material.content"/>
                                        </video>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" v-if="material.additional_content">
                                <button @click="showAdditional(material)" class="btn pull-right btn-secondary">
                                    <span class="fa fa-file-text-o mr-2"></span>
                                    текст видео
                                </button>
                            </div>
                        </div>
                        <div class="card" v-if="material.material_type_id==3">
                            <div class="card-block">
                                <audio controls="" class="d-block w-100">
                                    <source :src="material.content">
                                </audio>
                                <div v-if="material.additional_content" class="mt-4" v-html="material.additional_content" style="white-space: pre-line;">
                                </div>
                            </div>
                        </div>
                        <preview-answers :tasks="material.task_group.tasks" v-if="material.material_type_id==5"></preview-answers>
                    </div>
                </div>
                <div class="card-footer clearfix" v-show="lesson.pages.length>1">
                    <button class="btn btn-outline-secondary pull-left" v-if="selectedPage != 0" @click="selectedPage--">
                        <span class="fa fa-angle-left mr-1"></span>
                        Предыдущая страница
                    </button>
                    <button class="btn btn-outline-primary pull-right" v-if="selectedPage != lesson.pages.length - 1" @click="selectedPage++">
                        Следующая страница
                        <span class="fa fa-angle-right ml-1"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card" v-show="lesson.pages.length>0">
                <ul class="list-group list-group-flush" style="cursor:pointer">
                    <button v-for="(page,index) in lesson.pages" :class="pageButtonClass(index)" @click="selectedPage = index" style="cursor:pointer;">
                        <div class="fa fa-fw fa-circle-o text-muted mr-2"></div>
                        <span style="display:inline-block;max-width:100%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ page.name }}</span>
                    </button>
                </ul>
                <div class="card-block clearfix" v-show="lesson.pages.length>1">
                    <button class="btn btn-secondary pull-left" v-if="selectedPage != 0" @click="selectedPage--">
                        <span class="fa fa-angle-left"></span>
                    </button>
                    <button class="btn btn-outline-primary pull-right" v-if="selectedPage != lesson.pages.length - 1" @click="selectedPage++">
                        Дальше
                        <span class="fa fa-angle-right ml-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {

        props: ['lesson'],

        data() {
            return {
                array: [],
                titles: ['задание', 'задания', 'заданий'],
                selectedTaskGroup: {},
                selectedPage: '',
            }
        },
        computed: {
            ordered() {
                let array = [];
                if(this.selectedPage === parseInt(this.selectedPage, 10)) {
                    if(this.lesson.pages[this.selectedPage].materials) {
                        array = this.lesson.pages[this.selectedPage].materials.sort(function(a,b) {
                            if(a.material_order > b.material_order) return 1;
                            if(a.material_order < b.material_order) return -1;
                            return 0;
                        });
                    }
                }
                return array;
            }
        },
        watch: {
            lesson() {
                this.lesson.pages.length > 0 ? this.selectedPage = 0 : this.selectedPage = '';
            }
        },
        mounted() {
            this.lesson.pages.length > 0 ? this.selectedPage = 0 : this.selectedPage = '';
        },
        components: {
            'preview-task': require('./TaskGroup/TaskPreview.vue'),
            'preview-answers': require('../../../components/lms/TaskGroup/Answers.vue'),
        },
        methods: {
            matchYoutubeUrl(url) {
                var p = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                if(url.match(p)){
                    return url.match(p)[1];
                }
                return false;
            },
            showAdditional(material) {
                material.showAdditional = !material.showAdditional;
            },
            pageButtonClass(value) {
                return this.selectedPage === value ? "d-block list-group-item list-group-item-info list-group-item-action" : "d-block list-group-item list-group-item-action";
            },
        },
    }
</script>