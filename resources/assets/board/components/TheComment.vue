<template>
    <v-sheet class="d-flex align-center ml-14 mt-2 mr-3">
        <div class="me-auto text-body-2" v-html="modelValue.content"></div>
        <v-icon icon="mdi-delete" size="x-small" color="grey-lighten-1" v-show="!isConfirmingDelete" @click="showConfirm"></v-icon>
        <v-icon icon="mdi-delete-forever" size="x-small" color="red" v-show="isConfirmingDelete" @click="deleteComment(modelValue.id)"></v-icon>
    </v-sheet>
</template>

<script>

export default {
    name: 'TheComment',
    data() {
        return {
            isConfirmingDelete: false,
        }
    },
    props: ['modelValue', 'cancelDelete'],
    emits: [
        'update:modelValue',
        'comment:delete',
    ],
    watch: {
        cancelDelete(newValue) {
            if (newValue) {
                this.isConfirmingDelete = false
            }
        }
    },
    methods: {
        deleteComment(id) {
            this.$emit('comment:delete', id)
        },
        showConfirm() {
            this.isConfirmingDelete = true
        },
    }
};
</script>
