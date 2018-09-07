<template>
	<div>
		<h1 class="wp-heading-inline">Edit Form #{{form_id}} {{status}}</h1>
		<hr class="wp-header-end">
	</div>
</template>

<script>
	export default {
		name: "Entry",
		components: {},
		data() {
			return {
				form_id: 0,
				status: 'all',
			}
		},
		mounted() {
			this.form_id = this.$route.params.form_id;
			this.status = this.$route.params.status;
		}
	}
</script>

<style lang="scss">
</style>
