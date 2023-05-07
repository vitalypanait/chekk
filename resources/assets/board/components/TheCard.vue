<template>
    <v-card class="my-2 py-3 rounded-lg" elevation="0" style="cursor: pointer">
        <v-sheet class="d-flex align-start" @click="toggleCollapse">
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
            <v-menu open-on-hover open-delay="50" :transition="false">
                <template v-slot:activator="{ props }">
                    <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2" @click.stop="false"></v-icon>
                </template>

                <v-list class="rounded-lg">
                    <v-list-item value="collapse">
                        <v-list-item-title @click="toggleCollapse">Collapse</v-list-item-title>
                    </v-list-item>
                    <v-list-item value="edit">
                        <v-list-item-title @click="makeEditable">Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item value="delete">
                        <v-list-item-title @click="deleteTask">Delete</v-list-item-title>
                    </v-list-item>
                    <v-divider v-show=labels.length > 0"></v-divider>
                    <v-list-item v-show="labels.length > 0">
                        <div class="my-2">Labels</div>
                        <v-chip
                            v-for="label in labels"
                            variant="outlined"
                            label
                            class="font-weight-black mr-2"
                            size="x-small"
                            :color="label.color"
                            text-color="white"
                            @click.stop="mutateLabel(label)"
                        >{{ label.title }}</v-chip>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-sheet>
        <v-sheet class="mt-1 ml-13" v-if="modelValue.labels.length > 0"  @click="toggleCollapse">
            <div>
                <v-chip
                    v-for="label in modelValue.labels"
                    variant="outlined"
                    label
                    class="font-weight-black mx-1"
                    size="x-small"
                    :color="label.label.color"
                    text-color="white"
                    @click.stop="deleteLabel(label)"
                >{{ label.label.title }}</v-chip>
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
            <v-sheet class="d-flex align-center ml-14 mt-2 mr-3" v-for="comment in modelValue.comments">
                <div class="me-auto text-body-2">{{ comment.content }}</div>
                <v-icon icon="mdi-delete" size="x-small" color="grey-lighten-1" @click="deleteComment(comment.id)"></v-icon>
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
    props: ['modelValue', 'labels'],
    emits: [
        'update:modelValue',
        'task:update',
        'task:delete',
        'comment:add',
        'comment:delete',
        'label:add',
        'label:delete'
    ],
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
        mutateLabel(label) {
            let labelToDelete = null;

            this.modelValue.labels.forEach(taskLabel => {
                if (taskLabel.label.id === label.id) {
                    labelToDelete = taskLabel;
                }
            });

            if (labelToDelete === null) {
                this.$emit('label:add', {
                    id: label.id,
                    taskId: this.modelValue.id
                })
            } else {
                this.deleteLabel(labelToDelete)
            }
        },
        deleteLabel(label) {
            this.$emit('label:delete', label)
        },
    }
};
</script>
