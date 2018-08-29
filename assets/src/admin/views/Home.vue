<template>
    <div>
        <h1 class="wp-heading-inline">Forms</h1>
        <a href="#" class="page-title-action">Add New</a>
        <hr class="wp-header-end">

        <list-table
                :columns="{
    'title': {      label: 'Title',      sortable: true    },
    'author': {      label: 'Author'    }
  }"
                :loading="false"
                :items="[
    {
      id: 1,
      title: 'Wings of Fire: An Autobiography',
      author: ['A.P.J. Abdul Kalam'],
      image: 'https://images.gr-assets.com/books/1295670969l/634583.jpg'
    },
    {
      id: 2,
      title: 'Who Moved My Cheese?',
      author: ['Spencer Johnson', 'Kenneth H. Blanchard'],
      image: 'https://images.gr-assets.com/books/1388639717l/4894.jpg'
    },
    {
      id: 3,
      title: 'Option B',
      author: ['Sheryl Sandberg', 'Adam Grant', 'Adam M. Grant'],
      image: 'https://images.gr-assets.com/books/1493998427l/32938155.jpg'
    }
  ]"
                :actions="[
    {
      key: 'edit',
      label: 'Edit'
    },
    {
      key: 'trash',
      label: 'Delete'
    }
  ]"
                :show-cb="true"
                :total-items="15"
                :bulk-actions="[
    {
      key: 'trash',
      label: 'Move to Trash'
    }
  ]"
                :total-pages="5"
                :per-page="3"
                :current-page="1"
                action-column="title"
                @pagination="goToPage"
                @action:click="onActionClick"
                @bulk:click="onBulkAction"
        >
            <template slot="title" slot-scope="data">
                <img :src="data.row.image" :alt="data.row.title" width="50">
                <strong><a href="#">{{ data.row.title }}</a></strong>
            </template>

            <template slot="author" slot-scope="data">
                {{ data.row.author.join(', ') }}
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
                currentPage: 1,
            }
        },
        components: {
            Search,
            ListTable
        },
        methods: {
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
        }
    }
</script>

<style lang="scss">

</style>
