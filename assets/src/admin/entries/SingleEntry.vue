<template>
    <div>
        <h1 class="wp-heading-inline">Entry Details</h1>
        <a href="#" class="page-title-action" @click="goBackToListPage">Back to Entries</a>
        <hr class="wp-header-end">
        <columns>
            <column :tablet="8">
                <div class="entry-data entry-data--form-data">
                    <h2 class="entry-data__header">
                        <span>Entry #{{id}}: {{form_title}}</span>
                    </h2>
                    <div class="entry-data__content">
                        <ul class="entry-data__list">
                            <li class="entry-data__item" v-for="_data in form_data">
                                <span class="entry-data__label">{{_data.label}}</span>
                                <span class="entry-data__sep">:</span>
                                <span class="entry-data__value" v-html="_data.value"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </column>
            <column :tablet="4">
                <div class="entry-data entry-data--meta-data">
                    <h2 class="entry-data__header">
                        <span>Meta Information</span>
                    </h2>
                    <div class="entry-data__content">
                        <ul class="entry-data__list">
                            <li class="entry-data__item" v-for="meta in meta_data">
                                <span class="entry-data__label">{{meta.label}}</span>
                                <span class="entry-data__sep">:</span>
                                <span class="entry-data__value" v-html="meta.value"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </column>
        </columns>
    </div>
</template>

<script>
    import {CrudMixin} from "../../components/CrudMixin";
    import {columns, column} from 'shapla-columns';

    export default {
        name: "SingleEntry",
        mixins: [CrudMixin],
        components: {columns, column},
        data() {
            return {
                id: 0,
                form_id: 0,
                form_title: '',
                meta_data: [],
                form_data: [],
            }
        },
        mounted() {
            this.$store.commit('SET_LOADING_STATUS', false);
            this.id = this.$route.params.id;
            this.getEntry();
        },
        methods: {
            getEntry() {
                this.$store.commit('SET_LOADING_STATUS', true);
                this.get_item(dcfSettings.restRoot + '/entries/' + this.id).then(data => {
                    this.id = data.id;
                    this.form_id = data.form_id;
                    this.form_title = data.form_title;
                    this.form_data = data.form_data;
                    this.meta_data = data.meta_data;
                    this.$store.commit('SET_LOADING_STATUS', false);
                }).catch(error => {
                    console.log(error);
                    this.$store.commit('SET_LOADING_STATUS', false);
                })
            },
            goBackToListPage() {
                this.$router.push({name: 'EntriesList', params: {form_id: this.form_id, status: 'all'}});
            }
        }
    }
</script>

<style scoped lang="scss">
    .entry-data {
        background: #fff;
        margin-top: 1rem;

        &__header {
            border-bottom: 1px solid #ddd;
            padding: 1rem;
            margin: 0;
        }

        &__content {
        }

        &__list {
            margin: 0;
            padding: 0;
        }

        &__item {
            border-bottom: 1px dotted #ddd;
            display: flex;
            margin: 0;
            padding: 1rem;
        }

        &__label {
            font-weight: bold;
            min-width: 75px;
            display: inline-block;
            width: 30%;
        }

        &__sep {
            width: 20px;
            text-align: center;
        }
    }
</style>