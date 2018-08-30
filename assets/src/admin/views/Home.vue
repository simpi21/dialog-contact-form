<template>
    <div>
        <h1 class="wp-heading-inline">Forms</h1>
        <a href="#" class="page-title-action">Add New</a>
        <hr class="wp-header-end">

        <list-table
                :columns="{
                    'title': { label: 'Title', sortable: true },
                    'shortcode': { label: 'Shortcode' },
                    'entries': { label: 'Entries' },
                }"
                :loading="false"
                :items="items"
                :actions="[ { key: 'edit', label: 'Edit' }, { key: 'view', label: 'Preview' }, { key: 'trash', label: 'Trash' }]"
                :show-cb="true"
                :bulk-actions="[{ key: 'trash', label: 'Move to Trash' }]"
                :total-items="totalItems"
                :total-pages="totalItems"
                :per-page="perPage"
                :current-page="currentPage"
                action-column="title"
                @pagination="goToPage"
                @action:click="onActionClick"
                @bulk:click="onBulkAction"
        >
            <template slot="title" slot-scope="data">
                <strong><a href="#">{{ data.row.title }}</a></strong>
            </template>

            <template slot="shortcode" slot-scope="data">
                <input type="text" class="dcf-copy-shortcode"
                       :value="shortcode(data.row)" @click="copyToClipboard($event)">
            </template>

            <template slot="entries" slot-scope="data">
                {{data.row.entries}}
            </template>
        </list-table>

    </div>
</template>

<script>
    import Search from '../../components/Search.vue';
    import ListTable from '../components/ListTable.vue';

    export default {
        name: "Home",
        data() {
            return {
                items: [],
                currentPage: 1,
                totalItems: 0,
                perPage: 20,
                loading: true,
            }
        },
        components: {
            Search,
            ListTable
        },
        methods: {
            shortcode(item) {
                return `[dialog_contact_form id='${item.id}']`
            },
            copyToClipboard(event) {
                let input = event.target,
                    value = input.value;

                input.select();
                document.execCommand("copy");
                alert("Copied: " + value);
            },
            search(query) {
                console.log(query);
            },
            onActionClick(action, row) {
                if ('trash' === action) {
                    if (confirm('Are you sure to delete?')) {
                        alert('deleted: ' + row.title);
                    }
                }
            },
            onBulkAction(action, items) {
                console.log(action, items);
                alert(action + ': ' + items.join(', '));
            },
            goToPage(page) {
                console.log('Going to page: ' + page);
                this.currentPage = page;
            },
            sortCallback(column, order) {
                this.sortBy = column;
                this.sortOrder = order;

                // this.loadItems(comun, order);
            },
            list() {
                let $ = jQuery, self = this;
                $.ajax({
                    method: 'GET',
                    url: window.dcfApiSettings.root + '/forms',
                    success: function (response) {
                        let items = response.data.items,
                            pagination = response.data.meta.pagination;

                        self.loading = false;
                        self.items = items;
                        self.currentPage = pagination.currentPage;
                        self.totalItems = pagination.totalCount;
                        self.perPage = pagination.limit;
                    }
                });
            }
        },
        created() {
            this.list();
        },
    }
</script>

<style lang="scss">
    .toplevel_page_dialog-contact-form {
        .dcf-copy-shortcode {
            background-color: #f1f1f1;
            letter-spacing: 1px;
            padding: 5px 8px;
            width: 100%;
            max-width: 20em;

            &.widefat {
                max-width: 100%;
            }
        }

        @media screen and (min-width: 783px) {
            .wp-list-table {
                th, td {
                    &.title {
                        width: calc(50% - 2.5em);
                    }
                    &.shortcode {
                        width: 40%;
                    }
                    &.entries {
                        width: 10%;
                    }
                }
            }
        }
    }
</style>
