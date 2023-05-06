<template>
    <div
        class="task-title mt-7 mb-12 ml-1"
        v-show="!isEditable"
        @click="makeEditable"
        style="cursor: pointer"
    >{{ modelValue }}</div>
    <input placeholder="Type a task"
       class="task-title mt-7 mb-12 ml-1"
       v-show="isEditable"
       :value="modelValue"
       @input="changeTitle"
       @keyup.enter="update"
       @blur="update"
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
        },
        update() {
            this.editable = false;
            this.$emit('updateTitle', this.modelValue);
        }
    }
};
</script>
<style>
    .task-title {
        font-size: 3rem !important;
        background: #fafafa;
        outline: none;
    }
</style>