<template>
	<div>
		<h2 class="title">Entries</h2>

		<accordion :items="items">data</accordion>

	</div>
</template>

<script>
	import Accordion from '../../components/Accordion.vue';

	export default {
		name: "Entries",
		components: {Accordion},
		data() {
			return {
				items: [
					{
						title: 'Title one',
						body: 'Title one',
						isOpen: true,
					}
				]
			}
		}
	}
</script>

<style scoped>

</style>
