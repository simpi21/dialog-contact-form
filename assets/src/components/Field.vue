<template>
    <div class="field">
        <label class="label">{{label}}</label>
        <div class="control">
            <input class="input" :class="[ hasError ? 'is-danger' : '']" :type="type" :placeholder="placeholder"
                   :required="required" :value="value" @input="$emit('input', $event.target.value)" @blur="resetError">
        </div>
        <p class="help is-danger" v-if="hasError" v-text="error"></p>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "Field.vue",
        props: {
            label: {type: String, required: true, default: '',},
            type: {type: String, required: false, default: 'text',},
            placeholder: {type: String, required: false, default: '',},
            value: {required: false, default: ''},
            required: {type: Boolean, required: false, default: false},
            error: {type: String, required: false, default: ''},
        },
        computed: {
            hasError() {
                return !!this.error;
            }
        },
        methods: {
            resetError() {
                this.error = '';
            }
        }
    }
</script>
