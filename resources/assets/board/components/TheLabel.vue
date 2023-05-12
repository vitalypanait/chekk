<template>
    <v-sheet class="d-flex align-center mb-2">
        <div v-show="!isEditable" @click="makeEditable" class="me-auto" :class="'text-' + modelValue.color">{{ modelValue.title }}</div>
        <input placeholder="Add a label"
               class="the-label-title me-auto"
               :class="'text-' + modelValue.color"
               v-show="isEditable"
               :value="modelValue.title"
               @input="changeLabel"
               @keyup.enter="update"
               @blur="update"
               ref="theLabel"
        />
        <v-icon icon="mdi-delete" size="x-small" color="grey-lighten-1" v-show="!isConfirmingDelete" @click="showConfirm"></v-icon>
        <v-icon icon="mdi-delete-forever" size="x-small" color="red" v-show="isConfirmingDelete" @click="deleteLabel(modelValue)" @mouseleave="showDelete"></v-icon>
    </v-sheet>

</template>

<script>

export default {
    name: 'TheLabel',
    props: ['modelValue'],
    data() {
        return {
            editable: false,
            isConfirmingDelete: false,
        }
    },
    emits: ['update:modelValue', 'delete:label', 'update:label'],
    computed: {
        isEditable() {
            return this.editable;
        }
    },
    methods: {
        makeEditable() {
            this.editable = true

            this.$nextTick(() => {
                this.$refs.theLabel.focus()
            })
        },
        changeLabel(event) {
            this.modelValue.title = event.target.value

            this.editable = true;
            this.$emit('update:modelValue', this.modelValue)
        },
        update() {
            this.editable = false;
            this.$emit('update:label', this.modelValue);
        },
        deleteLabel(label) {
            this.$emit('delete:label', label)
        },
        showConfirm() {
            this.isConfirmingDelete = true
        },
        showDelete() {
            this.isConfirmingDelete = false
        }
    }
};
</script>
<style>
.the-label-title {
    outline: none;
    width: 100%;
}
</style>