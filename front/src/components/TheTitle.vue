<template>
    <div
        :class="getTitleClass()"
        class="mt-7 mb-8 ml-1"
        v-show="!isEditable"
        @click="makeEditable"
        style="cursor: pointer"
    >{{ modelValue }}</div>
    <input placeholder="Title"
       :class="getTitleClass()"
       class="mt-7 mb-8 ml-1"
       v-show="isEditable"
       :value="modelValue"
       @input="changeTitle"
       @keyup.enter="update"
       @blur="update"
       ref="theTitle"
    />
</template>

<script>

import { mobile } from "../mixins";

export default {
    name: 'TheTitle',
    data() {
        return {
            editable: false,
        }
    },
    props: ['modelValue', 'readOnly'],
    emits: ['update:modelValue', 'updateTitle'],
    mixins: [mobile],
    computed: {
        isEditable() {
            return this.modelValue.length === 0 || this.editable;
        }
    },
    methods: {
        changeTitle(event) {
            this.editable = true;
            this.$emit('update:modelValue', event.target.value)
        },
        makeEditable() {
            if (this.readOnly) {
              return;
            }

            this.editable = true;

            this.$nextTick(() => {
                this.$refs.theTitle.focus()
            })
        },
        update() {
            this.editable = false;
            this.$emit('updateTitle', this.modelValue);
        },
        getTitleClass() {
            return this.isMobile() ? 'the-title-mobile' : 'the-title'
        }
    }
};
</script>
<style>
    .the-title {
        font-size: 36px !important;
        line-height: 40px !important;
        background: #f0f0f0;
        outline: none;
        width: 100%;
    }

    .the-title-mobile {
        font-size: 28px !important;
        line-height: 32px !important;
        background: #f0f0f0;
        outline: none;
        width: 100%;
    }
</style>