<template>
	<div v-show="isActive">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: "Tab",
		props: {
			name: {type: String, required: true},
			selected: {type: Boolean, required: false}
		},
		computed:{
			href(){
				return '#'+ this.name.toLowerCase().replace(/ /g, '-');
			},
		},
		data() {
			return {
				isActive: false,
			}
		},
		mounted() {
			this.isActive = this.selected;
		}
	}
</script>

<style scoped>

</style>
