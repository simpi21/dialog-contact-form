<template>
    <div class="modal is-active" v-show="active">
        <div class="modal-background"></div>

        <div class="modal-content" v-show="!is_card">
            <slot name="body"></slot>
        </div>
        <button class="modal-close is-large" aria-label="close" v-show="!is_card" @click.prevent="close"></button>

        <div class="modal-card" v-show="is_card">
            <div class="modal-card-head">
                <p class="modal-card-title">{{title}}</p>
                <button class="delete" aria-label="close" @click.prevent="close"></button>
            </div>
            <div class="modal-card-body">
                <slot></slot>
            </div>
            <div class="modal-card-foot">
                <slot name="foot">
                    <button class="button" @click.prevent="close">Cancel</button>
                </slot>
            </div>
        </div>

    </div>
</template>

<script>
    export default {

        name: "Modal",

        props: {
            active: {type: Boolean, required: true, default: false},
            title: {type: String, required: false, default: 'Untitled'},
            type: {type: String, required: false, default: 'card'},
        },

        computed: {
            is_card() {
                return this.type === 'card';
            }
        },

        data() {
            return {}
        },

        methods: {
            close() {
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>

</style>