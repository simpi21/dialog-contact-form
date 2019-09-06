<template>
    <div class="dialog-contact-form--edit-form">
        <h1 class="wp-heading-inline">Edit Form: {{title}}</h1>
        <hr class="wp-header-end">
        <tabs>
            <tab name="Form Fields" :selected="true">
                <columns>
                    <column :tablet="8">
                        <columns :multiline="true" :gapless="true">
                            <column :class="`${field.field_width}`" v-for="field in fields" :key="field.field_id">
                                <field :field="field"></field>
                            </column>
                        </columns>
                    </column>
                    <column></column>
                </columns>
            </tab>
            <tab name="Form Actions"></tab>
            <tab name="Form Settings"></tab>
            <tab name="Validation Message"></tab>
        </tabs>
    </div>
</template>

<script>
    import {CrudMixin} from "../../components/CrudMixin";
    import {tabs, tab} from 'shapla-tabs';
    import {columns, column} from 'shapla-columns'
    import Field from "./Field";

    export default {
        name: "EditForm",
        mixins: [CrudMixin],
        components: {Field, tabs, tab, columns, column},
        data() {
            return {
                id: 0,
                title: '',
                fields: [],
                actions: [],
                settings: [],
                messages: [],
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
                    this.fields = response.fields;
                    this.actions = response.actions;
                    this.settings = response.settings;
                    this.messages = response.messages;
                    this.$store.commit('SET_LOADING_STATUS', false);
                }).catch(error => {
                    console.log(error);
                    this.$store.commit('SET_LOADING_STATUS', false);
                })
            },
            isTextField(type) {
                return -1 !== ['text', 'email', 'url', 'date', 'password'].indexOf(type);
            }
        }
    }
</script>

<style scoped>

</style>