<template>
    <div></div>
</template>

<script>
    export default {
        name: "EntriesList",
        data() {
            return {form_id: 0, status: 'all'}
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
            this.form_id = this.$route.params.form_id;
            this.status = this.$route.params.status;
        },
        methods: {}
    }
</script>

<style scoped>

</style>