<template>
    <div class="dcf-admin-field">
        <div class="dcf-admin-field__settings">

        </div>
        <template v-if="field.field_type === 'textarea'">
            <div class="dcf-admin-field__label">
                <label :for="field.field_id">{{field.field_title}}</label>
            </div>
            <div class="dcf-admin-field__input">
            <textarea
                    :placeholder="field.placeholder"
                    :required="field.required_field === 'on'"
            ></textarea>
            </div>
        </template>
        <template v-else>
            <div class="dcf-admin-field__label">
                <label :for="field.field_id">{{field.field_title}}</label>
            </div>
            <div class="dcf-admin-field__input">
                <input
                        :type="field.field_type"
                        :placeholder="field.placeholder"
                        :required="field.required_field === 'on'"
                >
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Field",
        props: {
            field: {type: Object, required: true}
        }
    }
</script>

<style lang="scss" scoped>
    .dcf-admin-field {
        position: relative;

        &__label,
        &__input {
            padding: .75rem;
        }

        &__label {
            display: block;
            margin-bottom: 10px;

            label {
                font-weight: bold;
            }
        }

        &__input {
            textarea,
            input:not([type=radio]):not([type=checkbox]) {
                border-radius: 4px;
                min-height: 48px;
                padding: 5px 10px;
                width: 100%;
            }

            textarea:not(rows) {
                min-height: 100px;
            }
        }

        &__settings {
            display: none;
            background: rgba(#000, .2);
            height: 100%;
            position: absolute;
            top: 0;
            width: 100%;
        }

        &:hover .dcf-admin-field__settings,
        &.is-active .dcf-admin-field__settings {
            display: block;
        }
    }
</style>