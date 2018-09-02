<template>
	<div>
		<tabs>
			<tab v-for="(panel,index) in panels" :name="panel.title" :selected="index === 0">
				<template v-for="section in sections" v-if="panel.id === section.panel">
					<h2 class="title" v-if="section.title">{{section.title}}</h2>
					<p class="description" v-if="section.description" v-html="section.description"></p>

					<table class="form-table">
						<template v-for="field in fields" v-if="field.section === section.id">
							<tr>
								<th scope="row">
									<label :for="field.id" v-text="field.name"></label>
								</th>
								<td>
									<input type="text" class="regular-text" :id="field.id" :value="getOption(field.id)"
										   @input="$emit('input', $event.target.value)">
									<p class="description" v-if="field.desc" v-html="field.desc"></p>
								</td>
							</tr>
						</template>
					</table>
				</template>
			</tab>
		</tabs>
	</div>
</template>

<script>
	import Tabs from '../../components/Tabs.vue';
	import Tab from '../../components/Tab.vue';

	export default {
		name: "Settings",
		components: {Tabs, Tab},
		data() {
			return {
				panels: [],
				sections: [],
				fields: [],
				options: [],
			}
		},
		methods: {
			getOption(id) {
				return this.options[id] ? this.options[id] : null;
			},
			getSettings() {
				let $ = jQuery, self = this;
				$.ajax({
					method: 'GET',
					url: window.dcfApiSettings.root + '/settings',
					success: function (response) {
						let data = response.data;
						self.panels = data.panels;
						self.sections = data.sections;
						self.fields = data.fields;
						self.options = data.options;
					}
				})
			}
		},
		mounted() {
			this.getSettings();
		}
	}
</script>

<style lang="scss">

</style>
