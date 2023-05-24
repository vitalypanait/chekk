<template>
    <v-card class="my-2 py-2 rounded-lg the-card" elevation="0" style="cursor: pointer">
        <v-sheet class="d-flex align-start" @click="toggleCollapse">
            <v-icon v-show="display === 'task'" class="ml-3" :icon="selectedIcon" color="grey"></v-icon>
            <div v-show="display === 'list'" class="ml-4 font-weight-bold">{{ index + 1}}</div>
            <div class="me-auto ml-5 pr-3 text-grey">{{ modelValue.title }}</div>
            <v-badge
                v-if="modelValue.comments.length > 0"
                color="grey-lighten-3 mr-1"
                text-color="grey"
                :content="modelValue.comments.length"
                inline
                style="margin-top: 2px!important"
            ></v-badge>
            <v-menu
                :open-on-hover="!isMobile()"
                :open-on-click="isMobile()"
                open-delay="50"
                :transition="false"
                :close-on-content-click="false"
            >
                <template v-slot:activator="{ props }">
                    <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2 handle" @click="showDelete"></v-icon>
                </template>

                <v-list class="rounded-lg">
                    <v-list-item value="restore" density="compact">
                        <div @click="restore">Restore</div>
                    </v-list-item>
                    <v-list-item value="delete" v-show="!isConfirmingDelete" density="compact">
                        <div @click="showConfirm">Delete</div>
                    </v-list-item>
                    <v-list-item value="confirm" v-show="isConfirmingDelete" @mouseleave="showDelete" density="compact">
                        <div @click="deleteTask" class="text-red">Confirm</div>
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
                    color="grey"
                    text-color="white"
                >{{ label.label.title }}</v-chip>
            </div>
        </v-sheet>
        <v-sheet v-show="collapse">
            <v-divider v-if="modelValue.comments.length > 0" class="mt-4"></v-divider>
            <v-sheet class="d-flex align-center ml-14 mt-2 mr-3" v-for="(comment) in modelValue.comments" :key="comment.id">
                <div class="me-auto text-body-2 text-grey" v-html="comment.content"></div>
            </v-sheet>
        </v-sheet>
    </v-card>
</template>

<script>

import { getStatusIcon } from '../utils';
import { mobile } from "../mixins";
import TheStatus from "./TheStatus.vue";

export default {
    name: 'TheArchivedTask',
  components: {TheStatus},
    data() {
        return {
            collapse: false,
            isConfirmingDelete: false
        }
    },
    props: ['modelValue', 'labels', 'display', 'index'],
    emits: [
        'update:modelValue',
        'task:delete',
        'task:restore',
    ],
    mixins: [mobile],
    computed: {
        selectedIcon() {
            return getStatusIcon(this.modelValue.status);
        },
    },
    methods: {
        toggleCollapse() {
            this.collapse = !this.collapse;
        },
        deleteTask() {
            this.$emit('task:delete', this.modelValue)
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
        restore() {
            this.$emit('task:restore', this.modelValue)
        }
    }
};
</script>
