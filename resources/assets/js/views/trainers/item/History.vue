<template>
	<div>
		<p v-for="history in histories">
			Дата создания: {{ history.created_at }} | Действие: {{ history.action_name }}
		</p>
	</div>
</template>

<script>
	export default {
		props: ['histories'],
	};
</script>