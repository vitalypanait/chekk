<template>
    <v-hover>
        <template v-slot:default="{ isHovering, props }">
            <v-sheet>
                <v-sheet
                    class="d-flex align-center"
                    v-bind="props"
                    :color="isHovering ? 'grey-lighten-5' : undefined"
                    @click="toggleCollapse"
                    style="cursor: pointer"
                >
                    <the-status v-model="modelValue.status" @update:modelValue="updateTask"></the-status>
                    <the-task-title
                        v-model:title="modelValue.title"
                        v-model:editable="editable"
                        @update="updateTask"
                    ></the-task-title>
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn v-bind="props" variant="plain" icon>
                                <v-icon icon="mdi-dots-vertical"></v-icon>
                            </v-btn>
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
                <v-timeline
                    density="compact"
                    side="end"
                    class="ml-4 mb-4"
                    v-show="collapse"
                    style="overflow-y: scroll; max-height: 300px;"
                >
                    <v-timeline-item
                        dot-color="white"
                        fill-dot
                        size="x-small"
                        min-width="300"
                    >
                        <template v-slot:icon>
                            <v-icon icon="mdi-circle-medium" color="grey"></v-icon>
                        </template>
                        <v-textarea
                            v-model="comment"
                            variant="solo"
                            flat
                            auto-grow
                            placeholder="Leave a comment..."
                            rows="1"
                            row-height="20"
                            class="my-n5 ml-n4"
                            @keydown.enter.exact.prevent="addComment"
                            @keydown.ctrl.enter.prevent="newLine"
                        ></v-textarea>
                    </v-timeline-item>
                    <v-timeline-item
                        v-for="comment in modelValue.comments"
                        dot-color="white"
                        fill-dot
                        size="x-small"
                        :key="comment.id"
                    >
                        <template v-slot:icon>
                            <v-icon icon="mdi-circle-medium" color="green"></v-icon>
                        </template>
                        <div class="d-flex align-center">
                            <div class="me-auto">{{ comment.content }}</div>
                            <v-btn variant="plain" icon size="small" @click="deleteComment(comment.id)">
                                <v-icon icon="mdi-delete"></v-icon>
                            </v-btn>
                        </div>
                    </v-timeline-item>
                </v-timeline>
                <v-divider></v-divider>
            </v-sheet>
        </template>
    </v-hover>
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
        },
        newLine(e) {
            e.target.value = e.target.value + "\n";
        }
    }
};
</script>
