<template>
    <div>
        <h1 class="wp-heading-inline">Entries</h1>
        <a href="#" class="page-title-action" @click="goBackToStatusPage">Back to Entries Counts</a>
        <hr class="wp-header-end">
        <status-list :statuses="statuses" @change="changeStatus"></status-list>
        <data-table
                :rows="items"
                :columns="columns"
                :actions="actions"
                :bulk-actions="bulkActions"
                :action-column="metaData.primaryColumn"
                @pagination="goToPage"
                @action:click="handleAction"
                @bulk:apply="handleBulkAction"
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
                currentPage: 1,
                columns: [],
                metaData: {},
            }
        },
        computed: {
            actions() {
                if ('trash' === this.status) {
                    return this.metaData.trashActions;
                }
                return this.metaData.actions;
            },
            bulkActions() {
                if ('trash' === this.status) {
                    return this.metaData.trashBulkActions;
                }
                return this.metaData.bulkActions;
            },
            statuses() {
                if (this.metaData.statuses && this.metaData.statuses.length > 0) {
                    return this.metaData.statuses.map(element => {
                        if (element.key === this.status) {
                            element['active'] = true;
                        }
                        return element;
                    });
                }
                return [];
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
                        form_id: this.form_id
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
            handleAction(action, item) {
                if ('view' === action) {
                    this.$router.push({name: 'SingleEntry', params: {id: item.id}});
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