<template>
	<div class="tabs-container">
		<div :class="tabClass">
			<ul>
				<li v-for="tab in tabs" :class="{'is-active': tab.isActive}">
					<a :href="href" @click="changeSelectedTab(tab)">{{tab.name}}</a>
				</li>
			</ul>
		</div>
		<div class="tabs-content">
			<slot></slot>
		</div>
	</div>
</template>

<script>
	export default {
		name: "Tabs",
		props: {
			href: {
				type: String,
				require: false,
			},
			align: {
				type: String,
				require: false,
				default: 'is-left',
				validator: function (value) {
					return ['is-left', 'is-centered', 'is-right'].indexOf(value) !== -1;
				}
			},
			size: {
				type: String,
				require: false,
				validator: function (value) {
					return ['is-small', 'is-medium', 'is-large'].indexOf(value) !== -1;
				}
			},
			boxed: {type: Boolean, require: false, default: false},
			toggle: {type: Boolean, require: false, default: false},
			toggleRounded: {type: Boolean, require: false, default: false},
			fullwidth: {type: Boolean, require: false, default: false},
		},
		data() {
			return {
				tabs: [],
			}
		},
		computed: {
			tabClass() {
				let _class = 'tabs';
				_class += ' ' + this.align;

				if (this.size)
					_class += ' ' + this.size;

				if (this.boxed)
					_class += ' is-boxed';

				if (this.toggle)
					_class += ' is-toggle';

				if (this.toggleRounded)
					_class += ' is-toggle-rounded';

				if (this.fullwidth)
					_class += ' is-fullwidth';

				return _class;
			},
		},
		methods: {
			changeSelectedTab(selectedTab) {
				this.tabs.forEach(tab => {
					tab.isActive = (tab.name === selectedTab.name);
				});
			}
		},
		created() {
			this.tabs = this.$children;
		}
	}
</script>

<style lang="scss">
	@import "~bulma/sass/utilities/all";
	@import "~bulma/sass/components/tabs";

	.tabs-container {
		margin: 1rem 0;
	}

	.tabs {
		ul, li {
			margin: 0;
			padding: 0;
		}

		a {
			text-decoration: none;

			&:focus {
				box-shadow: none;
			}
		}
	}
</style>
