<template>
    <div>
        <span :class="{'text-muted': !_value }" @mouseover="showIcon = true" @mouseleave="showIcon=false" @click="edit()" :style="{'cursor': showIcon ? 'pointer' : 'inherit', 'display': mode == 'view' ? 'inline-block' : 'none'}">
            {{ value ? value : 'не указано' }}
        </span>
        <span class="text-muted pl-1" v-show="showIcon && mode == 'view'"><span class="fa fa-pencil"></span></span>
        <div v-if="mode == 'edit'" class="mb-1">
            <div class="d-inline-block" style="vertical-align: top;">
                <masked-input v-model="value" class="form-control form-control-sm" mask="1 (111) 111 11 11" placeholder="8 (707) 465 48 12"/>
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