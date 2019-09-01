<template>
    <div class="dcf-button-group">
        <template v-for="(label, key) in settings.options">
            <label class="switch-label" :class="labelClass(key)" :for="`${settings.id}-${key}`">
                <input class="switch-input" type="radio" :id="`${settings.id}-${key}`" :value="key"
                       @change="$emit('input', $event.target.value)" :checked="value === key">
                <span v-text="label"></span>
            </label>
        </template>
    </div>
</template>

<script>
    export default {
        name: "ButtonGroup",
        props: {
            settings: {
                required: true,
            },

            value: {
                default: false
            },
        },
        methods: {
            labelClass(key) {
                return {
                    'switch-label-on': key === this.value,
                    'switch-label-off': key !== this.value,
                }
            }
        }
    }
</script>

<style lang="scss">
    .dcf-button-group {
        display: inline-flex;
        flex-wrap: wrap;

        .switch-label {
            background: rgba(0, 0, 0, .05);
            border-right: 1px solid rgba(0, 0, 0, .2);
            color: #555;
            margin: 0;
            padding: 0.75em 1em;
            font-size: 14px;
            flex-grow: 1;
            text-align: center;

            &:first-child {
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px;
            }

            &:last-child {
                border-right: none;
                border-top-right-radius: 4px;
                border-bottom-right-radius: 4px;
            }
        }

        .switch-input {
            display: none;
        }

        .switch-label-on {
            background-color: #3498DB;
            color: #fff;
        }
    }
</style>
