<template>
	<div class="tw-mb-8 tw-flex">
		<div class="tw-mr-4">
			<button class="tw-border tw-border-solid tw-border-grey-dark tw-rounded-full tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center tw-text-xs tw-text-grey-dark tw-font-bold tw-leading-none tw-cursor-pointer"
					@click="toggle"
			>
				<svg v-if="isOpen" xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16"
					 class="tw-fill-current">
					<path d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48z"></path>
				</svg>

				<svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16"
					 class="tw-fill-current">
					<path d="M12 9H7v5H5V9H0V7h5V2h2v5h5z"></path>
				</svg>
			</button>
		</div>

		<div class="tw-leading-loose">
			<h3 v-text="title"
				class="tw-font-bold tw-text-2xl tw-mb-3 tw-text-black tw-leading-none tw-cursor-pointer"
				@click="toggle"
			></h3>

			<div v-html="body" v-show="isOpen"></div>
		</div>
	</div>
</template>

<script>
	export default {
		name: "AccordionItem",

		props: ['title', 'body', 'isOpen'],

		methods: {
			toggle() {
				this.$emit('toggled', this.$vnode.key);
			}
		}

	}
</script>

<style scoped>

</style>
