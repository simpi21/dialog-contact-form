<template>
	<div>
		<h1 class="wp-heading-inline">Entries</h1>
		<hr class="wp-header-end">

		<div class="dcf-form-list">
			<div class="dcf-form-list-item" v-for="item in items">
				<h3 class="dcf-form-item-title" v-html="item.form_title"></h3>
				<ul class="subsubsub">
					<li class="all">
						<a :href="`#/entries/${item.form_id}/all`">
							All ({{item.status.unread + item.status.read}})
						</a> |
					</li>
					<li class="unread">
						<a :href="`#/entries/${item.form_id}/unread`">
							Unread ({{item.status.unread}})
						</a> |
					</li>
					<li class="read">
						<a :href="`#/entries/${item.form_id}/read`">
							Read ({{item.status.read}})
						</a> |
					</li>
					<li class="trash">
						<a :href="`#/entries/${item.form_id}/trash`">
							Trash ({{item.status.trash}})
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		name: "Entries",
		components: {},
		data() {
			return {
				items: [],
			}
		},
		methods: {
			get() {
				let $ = jQuery, self = this;
				$.ajax({
					method: 'GET',
					url: window.dcfApiSettings.root + '/entries/list',
					success: function (response) {
						self.items = response.data.items;
					}
				});
			}
		},
		mounted() {
			this.get();
		}
	}
</script>

<style lang="scss">
	.dcf-form-list-item {
		background: white;
		margin-bottom: 20px;
		padding: 20px;
		text-align: center;

		&:before,
		&:after {
			display: table;
			content: '';
		}

		&:after {
			clear: both;
		}

		&:first-child {
			margin-top: 20px;
		}
	}

	.dcf-form-item-title {
		margin-top: 0;
		text-align: center;
	}

	.subsubsub {
		width: 100%;
		margin-top: 0;
	}
</style>
