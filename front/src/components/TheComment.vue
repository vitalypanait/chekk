<template>
    <v-sheet>
        <div class="me-auto text-body-2" v-html="modelValue.content"></div>
        <v-menu
            :open-on-hover="!isMobile"
            :open-on-click="isMobile"
            open-delay="50"
            :transition="false"
            :close-on-content-click="false"
        >
            <template v-slot:activator="{ props }">
                <v-icon size="small" color="grey-lighten-1" icon="mdi-dots-vertical" v-bind="props" class="ml-3 handle" @click="showDelete"></v-icon>
            </template>

            <v-list class="rounded-lg">
                <v-list-item density="compact" value="makeAsTask">
                    <div @click="makeAsTask">Make as a task</div>
                </v-list-item>
                <v-list-item density="compact" value="delete" v-show="!isConfirmingDelete">
                    <div @click="showConfirm">Delete</div>
                </v-list-item>
                <v-list-item density="compact" value="confirm" v-show="isConfirmingDelete" @mouseleave="showDelete">
                    <div @click="deleteComment()" class="text-red">Confirm</div>
                </v-list-item>
            </v-list>
        </v-menu>
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
    props: ['modelValue', 'cancelDelete', 'isMobile'],
    emits: [
        'update:modelValue',
        'comment:delete',
        'comment:makeAsTask',
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
            this.$emit('comment:delete', this.modelValue.id)
        },
        showConfirm() {
            this.isConfirmingDelete = true
        },
        showDelete() {
            this.isConfirmingDelete = false
        },
        makeAsTask() {
            this.$emit('comment:makeAsTask', this.modelValue)
        }
    }
};
</script>
