<template>
    <div class="dialog-contact-form--edit-form">
        <h1 class="wp-heading-inline">Edit Form: {{title}}</h1>
        <hr class="wp-header-end">
        <tabs>
            <tab name="Form Fields"></tab>
            <tab name="Form Actions"></tab>
            <tab name="Form Settings"></tab>
            <tab name="Validation Message"></tab>
        </tabs>
    </div>
</template>

<script>
    import {CrudMixin} from "../../components/CrudMixin";
    import {tabs, tab} from 'shapla-tabs';

    export default {
        name: "EditForm",
        mixins: [CrudMixin],
        components: {tabs, tab},
        data() {
            return {
                id: 0,
                title: '',
            }
        },
        mounted() {
            this.id = this.$route.params.id;
            this.$store.commit('SET_LOADING_STATUS', false);
            this.getForm();
        },
        methods: {
            getForm() {
                this.$store.commit('SET_LOADING_STATUS', true);
                this.get_item(dcfSettings.restRoot + '/forms/' + this.id).then(response => {
                    console.log(response);
                    this.title = response.title;
                    this.$store.commit('SET_LOADING_STATUS', false);
                }).catch(error => {
                    console.log(error);
                    this.$store.commit('SET_LOADING_STATUS', false);
                })
            }
        }
    }
</script>

<style scoped>

</style>