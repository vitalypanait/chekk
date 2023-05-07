<template>
    <div
        class="the-title mt-7 mb-12 ml-1"
        v-show="!isEditable"
        @click="makeEditable"
        style="cursor: pointer"
    >{{ modelValue }}</div>
    <input placeholder="Type a board title"
       class="the-title mt-7 mb-12 ml-1"
       v-show="isEditable"
       :value="modelValue"
       @input="changeTitle"
       @keyup.enter="update"
       @blur="update"
       ref="theTitle"
    />
</template>

<script>

export default {
    name: 'TheTitle',
    data() {
        return {
            editable: false,
        }
    },
    props: ['modelValue'],
    emits: ['update:modelValue', 'updateTitle'],
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
            this.editable = true;

            this.$nextTick(() => {
                this.$refs.theTitle.focus()
            })
        },
        update() {
            this.editable = false;
            this.$emit('updateTitle', this.modelValue);
        }
    }
};
</script>
<style>
    .the-title {
        font-size: 3rem !important;
        background: #f0f0f0;
        outline: none;
        width: 100%;
    }
</style>