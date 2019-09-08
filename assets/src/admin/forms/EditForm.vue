<template>
    <div class="dialog-contact-form--edit-form">
        <h1 class="wp-heading-inline">Edit Form: {{title}}</h1>
        <a href="#" class="page-title-action" @click.prevent="backToForms">Back to Forms</a>
        <hr class="wp-header-end">
        <tabs>
            <tab name="Form Fields" :selected="true">
                <columns>
                    <column :tablet="8">
                        <columns :multiline="true" :gapless="false">
                            <column :class="`${field.field_width}`" v-for="field in fields" :key="field.field_id">
                                <field :field="field" @click:action="handleFieldAction"></field>
                            </column>
                        </columns>
                    </column>
                    <column>
                        <h4>Available Fields</h4>
                        <columns class="dcf-available-fields" :multiline="true" :gapless="false" mobile>
                            <column :mobile="6" :tablet="6" v-for="_field in formFields" :key="_field.id">
                                <div class="dcf-available-field">
                                    <span class="dcf-available-field__icon" v-html="_field.icon"></span>
                                    <span class="dcf-available-field__title" v-html="_field.title"></span>
                                </div>
                            </column>
                        </columns>
                    </column>
                </columns>
            </tab>
            <tab name="Form Actions">
                <columns>
                    <column :tablet="8"></column>
                    <column :tablet="4">
                        <h4>Available Actions</h4>
                        <div v-for="_action in formActions" :key="_action.id">
                            <button class="button">{{_action.title}}</button>
                        </div>
                    </column>
                </columns>
            </tab>
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
        computed: {
            formActions() {
                return window.dialogContactForm.actions;
            },
            formFields() {
                return window.dialogContactForm.fields;
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
            },
            backToForms() {
                this.$router.push({name: 'FormsList'});
            },
            handleFieldAction(action, field) {
                let index = this.fields.indexOf(field);
                if ('delete' === action && confirm('Are you sure to delete?')) {
                    this.$delete(this.fields, index);
                }
                if ('duplicate' === action && confirm('Are you sure to duplicate?')) {
                    let _field = JSON.parse(JSON.stringify(field));
                    _field['field_name'] = _field['field_id'] = field['field_id'] + '-copy';
                    _field['field_title'] = field['field_title'] + ' Copy';
                    this.fields.splice(index + 1, 0, _field);
                }
            }
        }
    }
</script>

<style lang="scss">
    .dcf-available-field {
        display: flex;
        border: 1px solid darkgreen;
        border-radius: 4px;

        &__icon,
        &__title {
            padding: 5px;
        }

        &__icon {
            padding-right: 5px;

            svg {
                display: block;
                height: 16px;
                width: 16px;
            }
        }

        &__title {
        }
    }
</style>