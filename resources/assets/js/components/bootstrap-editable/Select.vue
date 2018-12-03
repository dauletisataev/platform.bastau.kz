<template>
    <div>
        <span :class="{'text-muted': !_value }" :style="{'cursor': showIcon ? 'pointer' : 'inherit', 'display': mode == 'view' ? 'inline-block' : 'none'}" @mouseover="showIcon = true" @mouseleave="showIcon=false" @click="edit()">
            {{ getValueName() }}
        </span>
        <span class="text-muted pl-1" v-show="showIcon && mode == 'view'"><span class="fa fa-pencil"></span></span>
        <div v-if="mode == 'edit'" class="mb-1">
            <div class="d-inline-block" style="vertical-align: top;">
                <select class="form-control form-control-sm" v-model="value">
                    <option v-if="!required" :value="null">не выбрано</option>
                    <option v-for="option in options" :value="option.id">{{ option.name }}</option>
                </select>
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
        props: ['_value', 'options', 'required'],

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
        methods: {
            getValueName() {
                let name = 'не указано';
                let _this = this;
                if(this.options && this.options.length>0) {
                    this.options.forEach(function(option) {
                        if(_this.value === option.id) name = option.name;
                    });
                    return name;
                }
            },
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