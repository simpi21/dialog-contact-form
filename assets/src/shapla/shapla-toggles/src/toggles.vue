<template>
    <div class="shapla-toggles">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "toggles",
        data() {
            return {
                toggles: [],
            }
        },
        created() {
            this.toggles = this.$children;
        }
    }
</script>

<style scoped>

</style>