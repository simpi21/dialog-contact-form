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
                <div class="shortcode-inside">
                    <input type="text" class="dcf-copy-shortcode"
                       :value="shortcode(data.row)" @click="copyToClipboard($event)">
                    <span class="svg-wrapper" @click="copyTo($event)">
                        <span class="tooltip-text">Copy to clipboard</span>
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M12.656 14v-9.344h-7.313v9.344h7.313zM12.656 3.344c0.719 0 1.344 0.594 1.344 1.313v9.344c0 0.719-0.625 1.344-1.344 1.344h-7.313c-0.719 0-1.344-0.625-1.344-1.344v-9.344c0-0.719 0.625-1.313 1.344-1.313h7.313zM10.656 0.656v1.344h-8v9.344h-1.313v-9.344c0-0.719 0.594-1.344 1.313-1.344h8z"></path>
                        </svg>
                    </span>
                </div>
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
            copyTo(event) {
                let button = jQuery(event.target),
                    column = button.closest('.column.shortcode'),
                    tooltip = column.find('.tooltip-text'),
                    input = column.find('input');

                input.select();
                document.execCommand("copy");
                tooltip.html("Copied");
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

        box-sizing: border-box;

        * {
            box-sizing: border-box;
        }

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

        .svg-wrapper {
            padding: 5px;
            border: 1px solid rgb(221, 221, 221);
            display: inline-block;
            position: relative;

            .tooltip-text {
                visibility: hidden;
                width: 140px;
                background-color: #555;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 150%;
                left: 50%;
                margin-left: -75px;
                opacity: 0;
                transition: opacity 0.3s;

                &::after {
                    content: "";
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    margin-left: -5px;
                    border-width: 5px;
                    border-style: solid;
                    border-color: #555 transparent transparent transparent;
                }
            }

            &:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
            }

            svg {
                overflow: hidden;
                display:block;
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
