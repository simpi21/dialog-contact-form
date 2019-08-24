<template>
	<div>
		<h1 class="wp-heading-inline">Settings Page</h1>
		<hr class="wp-header-end">
	</div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "Home",
        components: {},
        data() {
            return {}
        },
        computed: {
            ...mapState(['loading'])
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
            this.$store.commit('SET_NOTIFICATION', {message: 'Show Notification.', type: 'success'});
        },
        methods: {}
    }
</script>

<style lang="scss">

</style>
