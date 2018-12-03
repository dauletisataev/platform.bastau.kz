<template>
    <div>
        <span :class="{'text-muted': !_value }" @mouseover="showIcon = true" @mouseleave="showIcon=false" @click="edit()" :style="{'cursor': showIcon ? 'pointer' : 'inherit', 'display': mode == 'view' ? 'inline-block' : 'none'}">
            {{ value ? value : 'не указано' }}
        </span>
        <span class="text-muted pl-1" v-show="showIcon && mode == 'view'"><span class="fa fa-pencil"></span></span>
        <div v-if="mode == 'edit'" class="mb-1">
            <div class="d-inline-block" style="vertical-align: top;position:relative;">
                <input type="text" v-model="value" class="form-control form-control-sm" style="padding-right:24px;">
                <span
                        @mouseover="eraseHover=true"
                        @mouseleave="eraseHover=false"
                        @click="value = ''"
                        class="fa fa-times-circle"
                        :style="{
                            'display': value ? 'block' : 'none',
                            'width': '13px',
                            'height': '13px',
                            'position': 'absolute',
                            'opacity': eraseHover ? '0.6' : '0.3',
                            'z-index': '100',
                            'top': '50%',
                            'right': '6px',
                            'margin-top': '-8px',
                            'cursor': eraseHover ? 'pointer' : 'inherit'
                            }"
                ></span>
            </div>
            <div class="d-inline-block" style="vertical-align: top;">
                <button class="btn btn-primary btn-sm" @click="save()">
                    <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-secondary btn-sm" @click="cancel()">
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
                eraseHover: false,
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