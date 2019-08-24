<template>
	<div>
		<h1 class="wp-heading-inline">Home Page</h1>
		<hr class="wp-header-end">
		<data-table :columns="[]" :rows="[]"></data-table>
	</div>
</template>

<script>
    import {mapState} from 'vuex';
    import dataTable from 'shapla-data-table';

    export default {
        name: "Home",
        components: {dataTable},
        data() {
            return {}
        },
        computed: {
            ...mapState(['loading'])
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
        },
        methods: {}
    }
</script>

<style lang="scss">

</style>
