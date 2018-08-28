<template>
    <div class="shapla-list-item">
        <span class="shapla-list-item-label">{{label}}</span>
        <span class="shapla-list-item-separator">:</span>
        <span class="shapla-list-item-value">{{value}}</span>
    </div>
</template>

<script>
    export default {
        name: "ListItem",
        props: ['label', 'value']
    }
</script>

<style lang="scss">

</style>