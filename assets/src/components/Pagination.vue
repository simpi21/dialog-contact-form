<template>
    <div class="tablenav-pages">
        <span class="displaying-num">{{displaying_num}}</span>
        <span class="pagination-links" v-if="total_pages > 1">

            <span aria-hidden="true" class="tablenav-pages-navspan button disabled" v-if="disable_first">«</span>
            <span aria-hidden="true" class="tablenav-pages-navspan button disabled" v-if="disable_prev">‹</span>

            <a class="first-page button" href="#" @click.prevent="firstPage" v-if="!disable_first">
                <span class="screen-reader-text">First page</span>
                <span aria-hidden="true">«</span>
            </a>
            <a class="prev-page button" href="#" @click.prevent="prePage" v-if="!disable_prev">
                <span class="screen-reader-text">Previous page</span>
                <span aria-hidden="true">‹</span>
            </a>

            <span class="paging-input">
                <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                <input class="current-page" id="current-page-selector" type="text" min="1" size="1"
                       v-model="current_page" @blur="goToPage" :max="total_pages" aria-describedby="table-paging">
                <span class="tablenav-paging-text"> of <span class="total-pages">{{total_pages}}</span></span>
            </span>

            <a href="#" class="next-page button" @click.prevent="nextPage" v-if="!disable_next">
                <span class="screen-reader-text">Next page</span>
                <span aria-hidden="true">›</span>
            </a>
            <a href="#" class="last-page button" @click.prevent="lastPage" v-if="!disable_last">
                <span class="screen-reader-text">Last page</span>
                <span aria-hidden="true">»</span>
            </a>

            <span class="tablenav-pages-navspan button disabled" aria-hidden="true" v-if="disable_next">›</span>
            <span class="tablenav-pages-navspan button disabled" aria-hidden="true" v-if="disable_last">»</span>
        </span>
    </div>
</template>

<script>
    export default {
        name: "Pagination",
        props: {
            total_items: {type: Number, required: true, default: 0},
            current_page: {type: Number, required: false, default: 1},
            per_page: {type: Number, required: false, default: 20},
        },

        computed: {
            total_pages() {
                return Math.ceil(this.total_items / this.per_page)
            },
            /**
             * Disable first nav
             *
             * @returns {boolean}
             */
            disable_first() {
                return this.current_page < 3;
            },

            /**
             * Disable previous nav
             *
             * @returns {boolean}
             */
            disable_prev() {
                return this.current_page < 2;
            },

            /**
             * Disable next nav
             *
             * @returns {boolean}
             */
            disable_next() {
                return this.current_page >= this.total_pages;
            },

            /**
             * Disable last nav
             *
             * @returns {boolean}
             */
            disable_last() {
                return this.current_page >= (this.total_pages - 1);
            },

            /**
             * Get offset
             *
             * @returns {number}
             */
            offset: function () {
                return (this.current_page - 1) * this.per_page
            },

            /**
             * Get current page number
             *
             * @returns {string}
             */
            displaying_num() {
                if (this.total_items > 1) {
                    return `${this.total_items} items`;
                }
                return `${this.total_items} item`;
            }
        },

        methods: {
            nextPage() {
                if (this.current_page === this.total_pages) {
                    return;
                }
                this.current_page++;
                this.query();
            },
            prePage() {
                if (this.current_page === 1) {
                    return;
                }
                this.current_page--;
                this.query();
            },
            firstPage() {
                this.current_page = 1;
                this.query();
            },
            lastPage() {
                this.current_page = this.total_pages;
                this.query();
            },
            goToPage() {
                if (this.current_page < 1) this.current_page = 1;
                if (this.current_page > this.total_pages) this.current_page = this.total_pages;
                this.query();
            },
            query() {
                let data = {
                    current_page: this.current_page,
                    per_page: this.per_page,
                    offset: this.offset,
                };
                this.$emit('pagination', data);
            }
        }
    }
</script>

<style scoped>

</style>