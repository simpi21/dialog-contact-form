<template>
    <div class="field-option-data">
        <div class="panel-field-opt panel-field-opt-text">
            <label class="field-option-data__label clearfix">
                <strong>{{ option_field.title }}</strong>
                <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>
                <span class="pull-right">
                    <input type="checkbox" v-model="show_value">Show values
                </span>
            </label>

            <ul :class="['option-field-option-chooser', show_value ? 'show-value' : '']">
                <li class="clearfix margin-0 header">
                    <div class="selector">&nbsp;</div>

                    <div class="sort-handler">&nbsp;</div>

                    <div class="label">Label</div>

                    <div v-if="show_value" class="value">Value</div>

                    <div class="action-buttons">&nbsp;</div>
                </li>
            </ul>

            <ul :class="['option-field-option-chooser margin-0', show_value ? 'show-value' : '']">
                <li v-for="(option, index) in options" :key="option.id" :data-index="index"
                    class="clearfix option-field-option">
                    <div class="selector">
                        <input
                                v-if="option_field.is_multiple"
                                type="checkbox"
                                :value="option.value"
                                v-model="selected"
                        >
                        <input
                                v-else
                                type="radio"
                                :value="option.value"
                                v-model="selected"
                                class="option-chooser-radio"
                        >
                    </div>

                    <div class="sort-handler">
                        <i class="fa fa-bars"></i>
                    </div>

                    <div class="label">
                        <input type="text" v-model="option.label" @input="set_option_label(index, option.label)">
                    </div>

                    <div v-if="show_value" class="value">
                        <input type="text" v-model="option.value">
                    </div>

                    <div class="action-buttons clearfix">
                        <i class="fa fa-minus-circle" @click="delete_option(index)"></i>
                    </div>
                </li>
                <li>
                    <div class="plus-buttons clearfix" @click="add_option">
                        <i class="fa fa-plus-circle"></i>
                    </div>
                </li>
            </ul>

            <a v-if="!option_field.is_multiple && selected" href="#clear" @click.prevent="clear_selection">Clear
                Selection</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FieldOptions",
        props: {
            option_field: {},
            options: [],
            selected: null,
        },
        data() {
            return {
                show_value: false,
            }
        },
        methods: {
            add_option() {
            },
            set_option_label(index, label) {
            },
            delete_option(index) {
            },
            clear_selection() {
            }
        }
    }
</script>

<style lang="scss">
    .field-option-data {
        &__label {
            display: flex;
            justify-content: space-between;
        }
    }
</style>