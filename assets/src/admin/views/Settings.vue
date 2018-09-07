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
									<template v-if="field.type === 'textarea'">
										<textarea class="regular-text" :id="field.id" :rows="field.rows"
												  v-model="options[field.id]"></textarea>
									</template>
									<template v-else-if="field.type === 'checkbox'">
										<switches v-model="options[field.id]"></switches>
									</template>
									<template v-else-if="field.type === 'radio'">
										<button-group :data="field" v-model="options[field.id]"></button-group>
									</template>
									<template v-else-if="field.type === 'select'">
										<select class="regular-text" v-model="options[field.id]">
											<option value="">-- Choose --</option>
											<option v-for="(label, value) in field.options" :value="value"
													v-text="label"></option>
										</select>
									</template>
									<template v-else>
										<input type="text" class="regular-text" :id="field.id"
											   v-model="options[field.id]">
									</template>
									<p class="description" v-if="field.desc" v-html="field.desc"></p>
								</td>
							</tr>
						</template>
					</table>
				</template>
			</tab>
		</tabs>
		<p class="submit">
			<input type="submit" class="button button-primary" value="Save Changes" @click="saveOptions">
		</p>
		<snackbars :show="snackbars.show" :body="snackbars.body"></snackbars>
	</div>
</template>

<script>
	import Tabs from '../../components/Tabs.vue';
	import Tab from '../../components/Tab.vue';
	import Switches from '../../elements/Switches.vue';
	import ButtonGroup from '../../elements/ButtonGroup.vue';
	import Snackbars from "../../elements/Snackbars";

	export default {
		name: "Settings",
		components: {Snackbars, Tabs, Tab, Switches, ButtonGroup, Snackbars},
		data() {
			return {
				panels: [],
				sections: [],
				fields: [],
				options: [],
				snackbars: {
					show: false,
					body: '',
				}
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
			},
			saveOptions() {
				let $ = jQuery, self = this;
				$.ajax({
					method: 'POST',
					url: window.dcfApiSettings.root + '/settings',
					data: {
						options: self.options,
					},
					success: function () {
						self.snackbars = {
							show: true,
							body: 'Options has been saved!'
						};
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
