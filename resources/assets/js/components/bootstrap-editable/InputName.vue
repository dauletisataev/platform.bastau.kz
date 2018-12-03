<template>
    <div>
        <span @mouseover="showIcon = true" @mouseleave="showIcon=false" @click="edit()" :style="{'cursor': showIcon ? 'pointer' : 'inherit', 'display': mode == 'view' ? 'inline-block' : 'none'}">
            {{ value ? value : 'Неизвестно' }}
        </span>
        <span class="text-muted pl-1" v-show="showIcon && mode == 'view'"><span class="fa fa-pencil"></span></span>
        <div v-if="mode == 'edit'" class="mb-1">
            <div class="input-group input-group-sm" style="vertical-align: top;position:relative;">
                <input type="text" v-model="value" class="form-control form-control-sm">
                <button class="btn btn-primary btn-sm input-group-addon" @click="save()">
                    <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-danger btn-sm input-group-addon" @click="cancel()">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['_value'],

        data() {
            return {
                value: '',
                showIcon: false,
                mode: 'view',
                defaultValue: '',
            }
        },

        created() {
            this.value = this._value;
            this.defaultValue = this._value ? JSON.parse(JSON.stringify(this._value)) : null;
        },

        watch: {
            _value(value) {
                this.value = value;
                this.defaultValue = value ? JSON.parse(JSON.stringify(value)) : null;
            }
        },

        components: {

        },

        methods: {
            edit() {
                this.mode = 'edit';
                this.showIcon = false;
                this.$emit('editOn');
            },
            cancel() {
                this.mode = 'view';
                this.value = this.defaultValue;
            },
            save() {
                this.$emit('save', this.value);
                this.mode = 'view';
            }
        }
    }
</script>