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
                color="grey-lighten-3"
                class=" mr-1"
                :content="modelValue.comments.length"
                inline
            ></v-badge>
            <v-menu
                :open-on-hover="!isMobile"
                :open-on-click="isMobile"
                open-delay="50"
                :transition="false"
                :close-on-content-click="false"
            >
                <template v-slot:activator="{ props }">
                    <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2 handle" @click="showDelete"></v-icon>
                </template>

                <v-list class="rounded-lg">
<!--                    <v-list-item value="collapse">-->
<!--                        <div @click="toggleCollapse">{{ getMenuCollapseText() }}</div>-->
<!--                    </v-list-item>-->
                    <v-list-item value="edit">
                        <div @click="makeEditable">Edit</div>
                    </v-list-item>
                    <v-list-item value="archive">
                        <div @click="archive">Archive</div>
                    </v-list-item>
                    <v-list-item value="delete" v-show="!isConfirmingDelete">
                        <div @click="showConfirm">Delete</div>
                    </v-list-item>
                    <v-list-item value="confirm" v-show="isConfirmingDelete" @mouseleave="showDelete">
                        <div @click="deleteTask" class="text-red">Confirm</div>
                    </v-list-item>
                    <v-divider v-show="labels.length > 0"></v-divider>
                    <v-list-item v-show="labels.length > 0">
                        <div class="my-2">Labels</div>
                        <v-chip
                            v-for="label in labels"
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
        <v-sheet class="mt-1 ml-13" v-if="modelValue.labels.length > 0" @click="toggleCollapse">
            <div>
                <v-chip
                    v-for="label in modelValue.labels"
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
            <tiptap
                v-model="comment"
                class="ml-14 my-4 mr-10"
                @update:content="addComment"
            />
            <v-divider v-if="modelValue.comments.length > 0"></v-divider>
            <div v-for="(comment, i) in modelValue.comments" :key="comment.id">
                <the-comment
                    v-model="modelValue.comments[i]"
                    :cancel-delete="!collapse"
                    @comment:delete="deleteComment"
                ></the-comment>
            </div>
        </v-sheet>
    </v-card>
</template>

<script>

import TheComment from './TheComment.vue';
import TheStatus from './TheStatus.vue';
import TheTaskTitle from './TheTaskTitle.vue';
import Tiptap from './Tiptap.vue'

export default {
    name: 'TheTask',
    components: {TheStatus, TheTaskTitle, Tiptap, TheComment},
    data() {
        return {
            collapse: false,
            editable: false,
            comment: '',
            isConfirmingDelete: false
        }
    },
    props: ['modelValue', 'labels', 'collapseTask', 'isMobile'],
    emits: [
        'update:modelValue',
        'editing:update',
        'task:update',
        'task:delete',
        'task:archive',
        'comment:add',
        'comment:delete',
        'label:add',
        'label:delete'
    ],
    watch: {
        editable(newValue, oldValue) {
            this.$emit('editing:update', newValue)
        },
        collapseTask(newValue, oldValue) {
            if (newValue) {
                this.collapse = false
            }
        }
    },
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
        addComment(value) {
            value = value.trim()

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
        getMenuCollapseText() {
            return this.collapse ? 'Collapse' : 'Expand'
        },
        showConfirm() {
            this.isConfirmingDelete = true
        },
        showDelete() {
            this.isConfirmingDelete = false
        },
        archive() {
            this.$emit('task:archive', this.modelValue)
        }
    }
};
</script>
