<template>
	<div>
		<h1 class="wp-heading-inline">Edit Form #{{form_id}}</h1>
		<hr class="wp-header-end">

		<tabs>
			<tab name="Form Fields" :selected="true">Form Fields</tab>
			<tab name="Form Settings">Form Settings</tab>
			<tab name="Actions After Submit">Actions After Submit</tab>
			<tab name="Validation Messages">Validation Messages</tab>
		</tabs>
	</div>
</template>

<script>
	import Tabs from '../../components/Tabs.vue';
	import Tab from "../../components/Tab.vue";

	export default {
		name: "Form",
		components: {Tabs, Tab},
		data() {
			return {
				form_id: 0,
			}
		},
		mounted() {
			this.form_id = this.$route.params.form_id;
		}
	}
</script>

<style scoped>

</style>
