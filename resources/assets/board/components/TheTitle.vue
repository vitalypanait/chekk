<template>
    <div
        class="text-h3 mt-8 mb-12"
        v-show="!isEditable"
        @click="makeEditable"
        style="cursor: pointer"
    >{{ modelValue }}</div>
    <v-text-field
        autofocus
        variant="underlined"
        v-show="isEditable"
        :value="modelValue"
        @input="changeTitle"
        @keyup.enter="update"
        class="mb-3 title-font-size"
        placeholder="Type a title"
    ></v-text-field>
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
        },
        update() {
            this.editable = false;
            this.$emit('updateTitle', this.modelValue);
        }
    }
};
</script>
<style>
    .title-font-size .v-field--active input, .title-font-size .v-field input {
        font-size: 3rem !important;
    }
</style>