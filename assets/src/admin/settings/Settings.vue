<template>
    <div>
        <h1 class="wp-heading-inline">Settings</h1>
        <hr class="wp-header-end">
        <tabs>
            <tab v-for="(panel,index) in panels" :key="panel.id" :name="panel.title" :selected="index === 0">
                <template v-for="section in sections" v-if="panel.id === section.panel">
                    <h2 class="title" v-if="section.title">{{section.title}}</h2>
                    <p class="description" v-if="section.description" v-html="section.description"></p>

                    <table class="form-table">
                        <template v-for="field in fields" v-if="field.section === section.id">
                            <tr>
                                <th scope="row">
                                    <label :for="field.id" v-text="field.name"></label>
                                </th>
                                <td>
                                    <template v-if="field.type === 'textarea'">
										<textarea class="regular-text" :id="field.id" :rows="field.rows"
                                                  v-model="options[field.id]"></textarea>
                                    </template>
                                    <template v-else-if="field.type === 'checkbox'">
                                        <switches v-model="options[field.id]"></switches>
                                    </template>
                                    <template v-else-if="field.type === 'radio'">
                                        <button-group :settings="field" v-model="options[field.id]"></button-group>
                                    </template>
                                    <template v-else-if="field.type === 'select'">
                                        <select class="regular-text" v-model="options[field.id]">
                                            <option value="">-- Choose --</option>
                                            <option v-for="(label, value) in field.options" :value="value"
                                                    v-text="label"></option>
                                        </select>
                                    </template>
                                    <template v-else>
                                        <input type="text" class="regular-text" :id="field.id"
                                               v-model="options[field.id]">
                                    </template>
                                    <p class="description" v-if="field.desc" v-html="field.desc"></p>
                                </td>
                            </tr>
                        </template>
                    </table>

                </template>
            </tab>
        </tabs>

        <p class="submit">
            <input type="submit" class="button button-primary" value="Save Changes" @click.prevent="saveOptions">
        </p>
    </div>
</template>

<script>
    import axios from 'axios';
    import {tabs, tab} from 'shapla-tabs';
    import ButtonGroup from "../../components/ButtonGroup";
    import Switches from "../../components/Switches";

    export default {
        name: "Settings",
        components: {tabs, tab, ButtonGroup, Switches},
        data() {
            return {
                options: {}
            }
        },
        computed: {
            settings() {
                return window.dcfAdminSettings;
            },
            panels() {
                return this.settings.panels;
            },
            sections() {
                return this.settings.sections;
            },
            fields() {
                return this.settings.fields;
            }
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
            this.options = window.dcfAdminSettings.options;
        },
        methods: {
            saveOptions() {
                this.$store.commit('SET_LOADING_STATUS', true);
                axios.post(dcfSettings.restRoot + '/settings', {options: this.options}).then(() => {
                    this.$store.commit('SET_LOADING_STATUS', false);
                    this.$store.commit('SET_NOTIFICATION', {
                        title: 'Success!',
                        message: 'Options has been updated.',
                        type: 'success'
                    })
                }).catch(error => {
                    console.error(error);
                    this.$store.commit('SET_LOADING_STATUS', false);
                    this.$store.commit('SET_NOTIFICATION', {
                        title: 'Error!',
                        message: 'Something went wrong.',
                        type: 'error'
                    })
                })
            }
        }
    }
</script>

<style scoped>

</style>