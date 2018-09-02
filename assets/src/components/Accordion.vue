<template>
	<div>
		<accordion-item
				v-for="(item, index) in items"
				:title="item[titleName]"
				:body="item[bodyName]"
				:key="index"
				:is-open="activeItemIndex === index"
				@toggled="onToggle"
		></accordion-item>
	</div>
</template>

<script>
	import AccordionItem from './AccordionItem.vue';

	export default {
		name: "Accordion",

		props: {
			items: {default: {}},
			titleName: {default: 'title'},
			bodyName: {default: 'body'}
		},

		components: {AccordionItem},

		data() {
			return {
				activeItemIndex: null
			};
		},

		methods: {
			onToggle(index) {
				if (this.activeItemIndex === index) {
					return (this.activeItemIndex = null);
				}

				this.activeItemIndex = index;
			}
		}
	}
</script>

<style scoped>

</style>
