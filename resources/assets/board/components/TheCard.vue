<template>
    <v-card class="my-2 py-3 rounded-lg elevation-5">
        <v-sheet class="d-flex align-center" @click="toggleCollapse">
            <div class="ml-3">
                <the-status v-model="modelValue.status" @update:modelValue="updateTask"></the-status>
            </div>
            <the-task-title
                v-model:title="modelValue.title"
                v-model:editable="editable"
                @update="updateTask"
            ></the-task-title>
            <v-badge
                v-if="modelValue.comments.length > 0"
                color="grey-lighten-3 mr-1"
                :content="modelValue.comments.length"
                inline
            ></v-badge>
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2"></v-icon>
                </template>

                <v-list>
                    <v-list-item value="collapse">
                        <v-list-item-title @click="toggleCollapse">Развернуть</v-list-item-title>
                    </v-list-item>
                    <v-list-item value="edit">
                        <v-list-item-title @click="makeEditable">Редактировать</v-list-item-title>
                    </v-list-item>
                    <v-list-item value="delete">
                        <v-list-item-title @click="deleteTask">Удалить</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-sheet>
        <v-sheet class="mt-1 ml-13" v-if="false">
            <div>
                <v-chip variant="outlined" label class="font-weight-black mx-1" size="x-small" color="red" text-color="white">Test</v-chip>
                <v-chip variant="outlined" label class="font-weight-black mx-1" size="x-small" color="green" text-color="white">Back</v-chip>
            </div>
        </v-sheet>
        <v-sheet v-show="collapse">
            <v-textarea
                v-model="comment"
                variant="solo"
                flat
                placeholder="Add a comment..."
                rows="1"
                row-height="20"
                @keydown.enter.exact.prevent="addComment"
                class="mx-10 mt-2 mb-n6"
            ></v-textarea>
            <v-divider v-if="modelValue.comments.length > 0"></v-divider>
            <v-sheet class="d-flex align-center ml-14 mt-4 mr-3" v-for="comment in modelValue.comments">
                <div class="me-auto">{{ comment.content }}</div>
                <v-icon icon="mdi-delete" size="small" color="grey-lighten-1" @click="deleteComment(comment.id)"></v-icon>
            </v-sheet>
        </v-sheet>
    </v-card>
</template>

<script>

import TheStatus from './TheStatus.vue';
import TheTaskTitle from './TheTaskTitle.vue';
export default {
    name: 'TheTask',
    components: {TheStatus, TheTaskTitle},
    data() {
        return {
            collapse: false,
            editable: false,
            comment: ''
        }
    },
    props: ['modelValue'],
    emits: ['update:modelValue', 'task:update', 'task:delete', 'comment:add', 'comment:delete'],
    methods: {
        toggleCollapse() {
            this.collapse = !this.collapse;
        },
        makeEditable() {
            this.editable = true;
        },
        updateTask() {
            this.$emit('task:update', this.modelValue)
        },
        deleteTask() {
            this.$emit('task:delete', this.modelValue)
        },
        addComment(event) {
            const value = event.target.value.trim()

            if (!value) {
                return
            }

            this.$emit('comment:add', {
                taskId: this.modelValue.id,
                content: value
            })

            this.comment = '';
        },
        deleteComment(id) {
            this.$emit('comment:delete', {
                id: id,
                taskId: this.modelValue.id
            })
        }
    }
};
</script>
