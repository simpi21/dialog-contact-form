<template>
	<div>
		<router-view></router-view>
		<notification :options="notification"></notification>
		<spinner :active="loading"></spinner>
	</div>
</template>

<script>
    import spinner from 'shapla-spinner';
    import notification from 'shapla-notifications';
    import {mapState} from 'vuex';

    export default {
        name: 'App',
        components: {notification, spinner},
        computed: {
            ...mapState(['loading', 'notification']),
        }
    }
</script>

<style lang="scss">

</style>
