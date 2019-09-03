<template>
    <div>
        <h1 class="wp-heading-inline">Forms</h1>
        <a href="#" class="page-title-action" @click="createNewForm">Add New</a>
        <hr class="wp-header-end">
        <status-list :statuses="metaData.statuses" @change="changeStatus"></status-list>
        <data-table
                :rows="items"
                :columns="columns"
                :actions="actions"
                :bulk-actions="bulkActions"
                :action-column="metaData.primaryColumn"
                :current-page="currentPage"
                :per-page="pagination.per_page"
                :total-items="pagination.total_items"
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
    import StatusList from "shapla-data-table/src/statusList";

    export default {
        name: "FormsList",
        mixins: [CrudMixin],
        components: {StatusList, dataTable},
        data() {
            return {
                status: 'all',
                search: '',
                currentPage: 1,
                columns: [
                    {key: 'title', label: 'Title'},
                    {key: 'shortcode', label: 'Shortcode'},
                    {key: 'entries', label: 'Entries'},
                ],
                metaData: {
                    statuses: [],
                    actions: [],
                    bulk_actions: [],
                    primaryColumn: 'title',
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
            this.getForms();
        },
        methods: {
            getForms() {
                this.$store.commit('SET_LOADING_STATUS', true);
                this.get_items(dcfSettings.restRoot + '/forms/', {
                    params: {
                        page: this.currentPage,
                        status: this.status,
                        search: this.search,
                        metadata: true,
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
                // this.$router.push({name: 'EntriesList', params: {status: status.key}});
                this.getForms();
            },
            goToPage(page) {
                this.currentPage = page;
                this.getForms();
            },
            createNewForm() {
                console.log('create from template.');
            },
            handleSearchSubmit(text) {
                this.search = text;
                this.getForms();
            },
            handleSearchInput(text) {
                if (text.length < 1) {
                    this.search = '';
                    this.getForms();
                }
            },
            handleAction(action, item) {
                if ('edit' === action) {
                    // this.$router.push({name: 'SingleEntry', params: {id: item.id}});
                    window.location.href = item.edit_url;
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
                this.action_batch_trash(dcfSettings.restRoot + '/forms/batch', ids, action).then(() => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    this.getForms();
                }).catch(error => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    console.log(error);
                });
            }
        }
    }
</script>

<style scoped>

</style>