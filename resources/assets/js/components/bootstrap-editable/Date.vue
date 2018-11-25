<template>
    <div>
        <span :class="{'text-muted': !_value }" @mouseover="showIcon = true" @mouseleave="showIcon=false" @click="edit()" :style="{'cursor': showIcon ? 'pointer' : 'inherit', 'display': mode == 'view' ? 'inline-block' : 'none'}">
            {{ !value ? 'не указано' : isBirthdate ? birthday(value) : value }}
        </span>
        <span class="text-muted pl-1" v-show="showIcon && mode == 'view'"><span class="fa fa-pencil"></span></span>
        <div v-if="mode == 'edit'" class="mb-1">
            <div class="d-inline-block" style="vertical-align: top;position:relative;">
                <datepicker v-model="value" :small="true" active="1" placeholder="дедлайн цели"></datepicker>
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


    import moment from 'moment';

    export default {
        props: ['_value','isBirthdate'],

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
            Datepicker: require('../Datepicker.vue')
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
            },

            birthday(value) {
                let str = moment(value, "DD.MM.YY").format("DD.MM.YYYY ");
                let date = moment(value, "DD.MM.YY");
                let _this = this;
                str += "(" + moment().diff(date, 'years') + " " + _this.$common.declOfNum(moment().diff(date, 'years'),["год","года","лет"]) + ")";
                return str;
            },
        }
    }
</script>