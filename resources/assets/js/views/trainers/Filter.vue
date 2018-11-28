<template>
    <div class="col-2 offset-2 fixed-top h-100 pt-4" id="search_filter">
    	<div>
    		<input type="text" name="search" v-model='search' placeholder="Имя, телефон или эл.почта">
    	</div>
    	<div v-for="trainer in filterTrainers">
    		{{ trainer.user.name }}
            {{ trainer.user.phone }}
            {{ trainer.user.email }}
    	</div>
    </div>
</template>

<script>
	import { get } from './../../helpers/api.js';

    export default{
        props: ['data','_form'],
    	data() {
    		return {
    			search: '',
                trainers: this.data
    		}
    	},
        computed: {
            filterTrainers() {
                return this.trainers.filter(trainer => {
                    return trainer.user.phone.includes(this.search) || 
                    trainer.user.email.toLowerCase().includes(this.search.toLowerCase()) || 
                    trainer.user.name.toLowerCase().includes(this.search.toLowerCase());
                })
            }
        }
    };
</script>