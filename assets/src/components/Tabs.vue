<template>
	<div class="tabs-container">
		<div class="tabs" :class="[align ? align : '']">
			<ul>
				<li v-for="tab in tabs" :class="{'is-active': tab.isActive}">
					<a :href="tab.href" @click="changeSelectedTab(tab)">{{tab.name}}</a>
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
			align: {type: String, require: false, default: 'is-left'},
		},
		data() {
			return {
				tabs: [],
			}
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

	.tabs {
		li {
			margin-bottom: 0;
		}

		a {
			text-decoration: none;
		}
	}
</style>
