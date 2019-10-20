<template>
    <div class="dcf-admin-field">
        <div class="dcf-admin-field__settings" :class="{'is-active':active}">
            <div class="dcf-admin-field__controls">
                <div class="dcf-admin-field__control">
                    <a href="#" title="Edit" @click.prevent="fieldAction('edit')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm7-7H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-1.75 9c0 .23-.02.46-.05.68l1.48 1.16c.13.11.17.3.08.45l-1.4 2.42c-.09.15-.27.21-.43.15l-1.74-.7c-.36.28-.76.51-1.18.69l-.26 1.85c-.03.17-.18.3-.35.3h-2.8c-.17 0-.32-.13-.35-.29l-.26-1.85c-.43-.18-.82-.41-1.18-.69l-1.74.7c-.16.06-.34 0-.43-.15l-1.4-2.42c-.09-.15-.05-.34.08-.45l1.48-1.16c-.03-.23-.05-.46-.05-.69 0-.23.02-.46.05-.68l-1.48-1.16c-.13-.11-.17-.3-.08-.45l1.4-2.42c.09-.15.27-.21.43-.15l1.74.7c.36-.28.76-.51 1.18-.69l.26-1.85c.03-.17.18-.3.35-.3h2.8c.17 0 .32.13.35.29l.26 1.85c.43.18.82.41 1.18.69l1.74-.7c.16-.06.34 0 .43.15l1.4 2.42c.09.15.05.34-.08.45l-1.48 1.16c.03.23.05.46.05.69z"/>
                        </svg>
                    </a>
                </div>
                <div class="dcf-admin-field__control">
                    <a href="#" class="sort-field" title="Sorting">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    </a>
                </div>
                <div class="dcf-admin-field__control">
                    <a href="#" title="Duplicate" @click.prevent="fieldAction('duplicate')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0z"/>
                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4l6 6v10c0 1.1-.9 2-2 2H7.99C6.89 23 6 22.1 6 21l.01-14c0-1.1.89-2 1.99-2h7zm-1 7h5.5L14 6.5V12z"/>
                        </svg>
                    </a>
                </div>
                <div class="dcf-admin-field__control">
                    <a href="#" title="Delete" @click.prevent="fieldAction('delete')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9zm7.5-5l-1-1h-5l-1 1H5v2h14V4z"/>
                            <path fill="none" d="M0 0h24v24H0V0z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <template v-if="field.field_type === 'html'">
            <div class="pt-40 pr-10 pb-10 pl-10">
                <div v-html="field.html"></div>
            </div>
        </template>
        <template v-else-if="field.field_type === 'textarea'">
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
            field: {type: Object, required: true},
            active: {type: Boolean, default: false},
        },
        methods: {
            fieldAction(action) {
                this.$emit('click:action', action, this.field);
            }
        }
    }
</script>

<style lang="scss">
    .dcf-admin-field {
        position: relative;
        background-color: #fff;

        .pt-40 {
            padding-top: 40px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pb-10 {
            padding-bottom: 10px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        &__label,
        &__input {
        }

        &__label {
            display: block;
            padding: 20px .75rem 0;

            label {
                font-weight: bold;
            }
        }

        &__input {
            padding: .75rem;

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
            border: 1px dashed rgba(#000, .1);
            background: rgba(#000, .01);
            height: 100%;
            position: absolute;
            top: 0;
            width: 100%;
            opacity: 0;

            &:hover {
                opacity: 1;
            }

            &.is-active {
                border-color: limegreen;
                opacity: 1;
            }
        }

        &:hover .dcf-admin-field__settings,
        &.is-active .dcf-admin-field__settings {
            display: block;
        }

        &__controls {
            display: flex;
            flex-direction: row-reverse;
        }

        &__control {
            padding: 5px;

            a {
                border: 1px solid rgba(#000, .1);
                height: 32px;
                width: 32px;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 16px;

                &:hover {
                    border-color: rgba(#000, .38);
                }
            }

            svg {
                fill: rgba(#000, .54);
                height: 20px;
                overflow: hidden;
                width: 20px;
            }
        }
    }
</style>