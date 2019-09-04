<template>
    <div class="copy-to-clipboard">
        <input type="text" class="copy-to-clipboard__text" :value="value" @click="copyToClipboard"/>
        <span class="copy-to-clipboard__icon" @click="copyToClipboard">
            <span class="copy-to-clipboard__tooltip">Copy to clipboard</span>
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <path d="M12.656 14v-9.344h-7.313v9.344h7.313zM12.656 3.344c0.719 0 1.344 0.594 1.344 1.313v9.344c0 0.719-0.625 1.344-1.344 1.344h-7.313c-0.719 0-1.344-0.625-1.344-1.344v-9.344c0-0.719 0.625-1.313 1.344-1.313h7.313zM10.656 0.656v1.344h-8v9.344h-1.313v-9.344c0-0.719 0.594-1.344 1.313-1.344h8z"></path>
            </svg>
        </span>
    </div>
</template>

<script>
    export default {
        name: "CopyToClipboard",
        props: {
            value: null,
        },
        data() {
            return {
                tooltipText: 'Copy to clipboard',
                copiedText: 'Copied',
            }
        },
        methods: {
            copyToClipboard() {
                let copyText = this.$el.querySelector('.copy-to-clipboard__text'),
                    tooltip = this.$el.querySelector('.copy-to-clipboard__tooltip');
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");

                tooltip.innerHTML = this.copiedText;
                setTimeout(() => {
                    tooltip.innerHTML = this.tooltipText;
                }, 3000);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .copy-to-clipboard {
        border: 1px solid rgba(0, 0, 0, 0.12);
        font-size: 16px;
        letter-spacing: 1px;
        position: relative;
        width: 100%;
        max-width: 20em;
        margin: 0;
        height: 2em;
        padding: 0 30px 0 0;

        &__text {
            width: 100%;
            display: flex;
            margin: 0;
            border: none;
            padding: 6px;
        }

        &__icon {
            border-left: 1px solid rgba(0, 0, 0, 0.12);
            display: inline-block;
            padding: 7px;
            position: absolute;
            top: 0;
            right: 0;
            height: calc(2em - 2px);
            width: calc(2em - 2px);

            .copy-to-clipboard__tooltip {
                font-size: .875em;
                visibility: hidden;
                width: 140px;
                background-color: #555;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 150%;
                left: 50%;
                margin-left: -75px;
                opacity: 0;
                transition: opacity 0.3s;

                &::after {
                    content: "";
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    margin-left: -5px;
                    border-width: 5px;
                    border-style: solid;
                    border-color: #555 transparent transparent transparent;
                }
            }

            &:hover .copy-to-clipboard__tooltip {
                visibility: visible;
                opacity: 1;
            }

            svg {
                overflow: hidden;
                display: block;
            }
        }
    }
</style>