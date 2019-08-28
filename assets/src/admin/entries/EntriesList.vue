<template>
    <div>
        <h1 class="wp-heading-inline">Entries</h1>
        <a href="#" class="page-title-action" @click="goBackToStatusPage">Back to Entries Counts</a>
        <hr class="wp-header-end">
        <status-list :statuses="metaData.statuses" @change="changeStatus"></status-list>
        <data-table
                :rows="items"
                :columns="columns"
                :actions="actions"
                :bulk-actions="bulkActions"
                :action-column="metaData.primaryColumn"
                :per-page="pagination.limit"
                :current-page="currentPage"
                :total-items="pagination.totalCount"
                @pagination="goToPage"
                @action:click="handleAction"
                @bulk:apply="handleBulkAction"
                @search:submit="handleSearchSubmit"
                @search:input="handleSearchInput"
        >
            <template slot="created_at" slot-scope="item">
                {{(new Date(item.row.created_at)).toLocaleString()}}
            </template>
        </data-table>
    </div>
</template>

<script>
    import {CrudMixin} from "../../components/CrudMixin";
    import dataTable from 'shapla-data-table';
    import StatusList from "../../shapla/shapla-data-table/src/statusList";

    export default {
        name: "EntriesList",
        mixins: [CrudMixin],
        components: {StatusList, dataTable},
        data() {
            return {
                form_id: 0,
                status: 'all',
                search: '',
                currentPage: 1,
                columns: [],
                metaData: {
                    statuses: [],
                },
            }
        },
        computed: {
            actions() {
                return this.metaData.actions;
            },
            bulkActions() {
                return this.metaData.bulk_actions;
            }
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
            this.form_id = this.$route.params.form_id;
            this.status = this.$route.params.status;
            this.getEntries();
        },
        methods: {
            getEntries() {
                this.$store.commit('SET_LOADING_STATUS', true);
                this.get_items(dcfSettings.restRoot + '/entries', {
                    params: {
                        page: this.currentPage,
                        status: this.status,
                        form_id: this.form_id,
                        search: this.search,
                    }
                }).then(data => {
                    this.metaData = data.metaData;
                    this.columns = this.metaData.columns;
                    this.$store.commit('SET_LOADING_STATUS', false);
                }).catch(error => {
                    console.log(error);
                    this.$store.commit('SET_LOADING_STATUS', false);
                })
            },
            changeStatus(status) {
                this.status = status.key;
                this.search = '';
                this.currentPage = 1;
                this.$router.push({
                    name: 'EntriesList', params: {
                        form_id: this.form_id,
                        status: status.key
                    }
                });
                this.getEntries();
            },
            goToPage(page) {
                this.currentPage = page;
                this.getEntries();
            },
            goBackToStatusPage() {
                this.$router.push({name: 'EntriesCounts'});
            },
            handleSearchSubmit(text) {
                this.search = text;
                this.getEntries();
            },
            handleSearchInput(text) {
                if (text.length < 1) {
                    this.search = '';
                    this.getEntries();
                }
            },
            handleAction(action, item) {
                if ('view' === action) {
                    this.$router.push({name: 'SingleEntry', params: {id: item.id}});
                }
                if ('mark_read' === action) {
                    this.batchReadUnreadAction([item.id], action);
                }
                if ('mark_unread' === action) {
                    this.batchReadUnreadAction([item.id], action);
                }
                if (-1 !== ['trash', 'restore', 'delete'].indexOf(action)) {
                    let message = 'Are you sure to do this?';
                    if ('trash' === action) message = 'Are you sure move this item to trash?';
                    if ('restore' === action) message = 'Are you sure restore this item again?';
                    if ('delete' === action) message = 'Are you sure to delete permanently?';
                    this.$modal.confirm(message).then(confirmed => {
                        if (confirmed) {
                            this.batchTrashAction([item.id], action);
                        }
                    });
                }
            },
            handleBulkAction(action, ids) {
                if ('mark_read' === action) {
                    this.batchReadUnreadAction(ids, action);
                }
                if ('mark_unread' === action) {
                    this.batchReadUnreadAction(ids, action);
                }
                if (-1 !== ['trash', 'restore', 'delete'].indexOf(action)) {
                    let message = 'Are you sure to do this?';
                    if ('trash' === action) message = 'Are you sure to trash all selected items?';
                    if ('restore' === action) message = 'Are you sure to restore all selected items?';
                    if ('delete' === action) message = 'Are you sure to delete all selected items permanently?';
                    this.$modal.confirm(message).then(confirmed => {
                        if (confirmed) {
                            this.batchTrashAction(ids, action);
                        }
                    });
                }
            },
            batchReadUnreadAction(ids, action) {
                this.$store.commit('SET_LOADING_STATUS', true);
                let data = {};
                data[action] = ids;
                this.update_item(dcfSettings.restRoot + '/entries/batch', data).then(() => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    this.getEntries();
                }).catch(error => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    console.log(error);
                });
            },
            batchTrashAction(ids, action) {
                this.$store.commit('SET_LOADING_STATUS', true);
                this.action_batch_trash(dcfSettings.restRoot + '/entries/batch', ids, action).then(() => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    this.getEntries();
                }).catch(error => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    console.log(error);
                });
            }
        }
    }
</script>

<style scoped lang="scss">
</style>