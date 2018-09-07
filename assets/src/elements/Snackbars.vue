<template>
	<div class="snackbars" v-show="showMessage" v-html="body"></div>
</template>

<script>
	export default {
		name: "Snackbars",
		props: {
			show: {type: Boolean, default: false,},
			body: {type: String, required: true,},
			timeout: {type: Number, required: false, default: 3000},
		},
		data() {
			return {
				enable: false,
			}
		},
		computed: {
			showMessage() {
				if (false === this.show) return false;

				let self = this;
				setTimeout(() => {
					self.show = false;
					return self.show;
				}, this.timeout);

				return this.show;
			}
		}
	}
</script>

<style lang="scss">
	.snackbars {
		min-width: 250px; /* Set a default minimum width */
		margin-left: -125px; /* Divide value of min-width by 2 */
		background-color: #333; /* Black background color */
		color: #fff; /* White text color */
		text-align: center; /* Centered text */
		border-radius: 2px; /* Rounded borders */
		padding: 16px; /* Padding */
		position: fixed; /* Sit on top of the screen */
		z-index: 10; /* Add a z-index if needed */
		left: 50%; /* Center the snackbar */
		bottom: 30px; /* 30px from the bottom */
		visibility: visible; /* Show the snackbar */
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}

	/* Animations to fade the snackbar in and out */
	@-webkit-keyframes fadein {
		from {
			bottom: 0;
			opacity: 0;
		}
		to {
			bottom: 30px;
			opacity: 1;
		}
	}

	@keyframes fadein {
		from {
			bottom: 0;
			opacity: 0;
		}
		to {
			bottom: 30px;
			opacity: 1;
		}
	}

	@-webkit-keyframes fadeout {
		from {
			bottom: 30px;
			opacity: 1;
		}
		to {
			bottom: 0;
			opacity: 0;
		}
	}

	@keyframes fadeout {
		from {
			bottom: 30px;
			opacity: 1;
		}
		to {
			bottom: 0;
			opacity: 0;
		}
	}
</style>
