<template>
    <div :class="{ 'table-loading': loading }">

        <div class="table-loader-wrap" v-show="loading">
            <div class="table-loader-center">
                <div class="table-loader">Loading</div>
            </div>
        </div>

        <slot name="status-navigation"></slot>

        <div>
            <search :id="searchId" :button-text="searchButtonText" :screen-reader-text="searchButtonText"></search>

            <div class="tablenav top">

                <div class="alignleft actions bulkactions" v-if="hasBulkActions" v-model="bulkLocal">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">Bulk Actions</option>
                        <option v-for="action in bulkActions" :value="action.key">{{ action.label }}</option>
                    </select>
                    <input type="submit" class="button action" value="Apply" @click.prevent="handleBulkAction"
                           :disabled="!checkedItems.length">
                </div>

                <div class="alignleft actions">
                    <slot name="filters"></slot>
                </div>

                <h2 class="screen-reader-text">Items list navigation</h2>
                <pagination :total_items="totalItems" :current_page="currentPage" :per_page="perPage"
                            @pagination="paginate"></pagination>

                <br class="clear">
            </div>

            <table :class="tableClass">

                <thead>
                <tr>
                    <td v-if="showCb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                        <input id="cb-select-all-1" type="checkbox" v-model="selectAll">
                    </td>
                    <th v-for="(value, key) in columns" :class="['manage-column', key]">{{ value.label }}</th>
                </tr>
                </thead>

                <tbody>
                <template v-if="hasItems">
                    <tr v-for="row in items" :key="row[index]">
                        <th scope="row" class="check-column" v-if="showCb">
                            <input type="checkbox" name="item[]" :value="row[index]" v-model="checkedItems">
                        </th>
                        <td v-for="(value, key) in columns" :class="['column', key]">
                            <slot :name="key" :row="row">
                                {{ row[key] }}
                            </slot>

                            <div v-if="actionColumn === key && hasActions" class="row-actions">
                                <slot name="row-actions" :row="row">
                                    <span v-for="action in actions" :class="action.key">
                                        <a href="#"
                                           @click.prevent="actionClicked(action.key, row)">{{ action.label }}</a>
                                        <template v-if="!hideActionSeparator(action.key)"> | </template>
                                    </span>
                                </slot>
                            </div>
                        </td>
                    </tr>
                </template>

                <tr class="no-items" v-if="!hasItems">
                    <td class="colspanchange" :colspan="colspan" v-text="notFound"></td>
                </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td v-if="showCb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                        <input id="cb-select-all-2" type="checkbox" v-model="selectAll">
                    </td>
                    <th v-for="(value, key) in columns" :class="['manage-column', key]">{{ value.label }}</th>
                </tr>
                </tfoot>

            </table>

        </div>

    </div>
</template>

<script>
    import Pagination from '../../components/Pagination.vue';
    import Search from '../../components/Search.vue';

    export default {
        name: "ListTable",
        components: {
            Pagination,
            Search,
        },
        props: {
            columns: {type: Object, required: true, default: {}},
            items: {type: Array, required: true, default: []},
            index: {type: String, default: 'id'},
            showCb: {type: Boolean, default: true},
            loading: {type: Boolean, default: false},
            actionColumn: {type: String, default: ''},
            actions: {type: Array, required: false, default: []},
            bulkActions: {type: Array, required: false, default: []},
            tableClass: {type: String, default: 'wp-list-table widefat fixed striped'},
            notFound: {type: String, default: 'No items found.'},
            // Search
            searchId: {type: String, required: false, default: 'search-items'},
            searchButtonText: {type: String, required: false, default: 'Search'},
            // Pagination
            totalItems: {type: Number, required: true, default: 0},
            currentPage: {type: Number, required: false, default: 1},
            perPage: {type: Number, required: false, default: 20},
            // Sorting
            sortBy: {type: String, default: null},
            sortOrder: {type: String, default: "asc"}
        },
        data() {
            return {
                bulkLocal: '-1',
                checkedItems: [],
            }
        },
        computed: {
            hasActions() {
                return this.actions.length > 0;
            },
            hasBulkActions() {
                return this.bulkActions.length > 0;
            },

            colspan() {
                let columns = Object.keys(this.columns).length;

                if (this.showCb) {
                    columns += 1;
                }

                return columns;
            },
            hasItems() {
                return this.items.length;
            },
            selectAll: {

                get: function () {
                    if (!this.items.length) {
                        return false;
                    }

                    return this.items ? this.checkedItems.length === this.items.length : false;
                },

                set: function (value) {
                    let selected = [],
                        self = this;

                    if (value) {
                        this.items.forEach(function (item) {
                            if (item[self.index] !== undefined) {
                                selected.push(item[self.index]);
                            } else {
                                selected.push(item.id);
                            }
                        });
                    }

                    this.checkedItems = selected;
                }
            }
        },
        methods: {

            hideActionSeparator(action) {
                return action === this.actions[this.actions.length - 1].key;
            },

            actionClicked(action, row) {
                this.$emit('action:click', action, row);
            },

            paginate(data) {
                this.$emit('pagination', data);
            },

            handleBulkAction() {
                if (this.bulkLocal === '-1') {
                    return;
                }

                this.$emit('bulk:click', this.bulkLocal, this.checkedItems);
            },

            isSortable(column) {
                return column.hasOwnProperty('sortable') && column.sortable === true;
            },

            isSorted(column) {
                return column === this.sortBy;
            },

            handleSortBy(column) {
                let order = this.sortOrder === 'asc' ? 'desc' : 'asc';

                this.$emit('sort', column, order);
            }
        }
    }
</script>

<style lang="scss">

    .table-loading {
        position: relative;

        .table-loader-wrap {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 9;

            .table-loader-center {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100%;
            }
        }

        .wp-list-table,
        .tablenav {
            opacity: 0.4;
        }
    }

    .table-loader {
        font-size: 10px;
        margin: 50px auto;
        text-indent: -9999em;
        width: 11em;
        height: 11em;
        border-radius: 50%;
        background: #ffffff;
        background: linear-gradient(to right, #ffffff 10%, rgba(255, 255, 255, 0) 42%);
        position: relative;
        animation: tableLoading 1s infinite linear;
        transform: translateZ(0);

        &:before {
            width: 50%;
            height: 50%;
            background: #ffffff;
            border-radius: 100% 0 0 0;
            position: absolute;
            top: 0;
            left: 0;
            content: '';
        }

        &:after {
            background: #f4f4f4;
            width: 75%;
            height: 75%;
            border-radius: 50%;
            content: '';
            margin: auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
    }

    @-webkit-keyframes tableLoading {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes tableLoading {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

</style>
