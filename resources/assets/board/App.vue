<template>
    <v-app class="main-background" full-height>
        <v-main>
            <v-container class="mx-auto">
                <v-row>
                    <v-col class="offset-sm-0 v-col-sm-8 offset-sm-2 v-col-lg-8 offset-lg-2">
                        <the-title v-model="board.title" @updateTitle="updateTitle"></the-title>
                        <div class="d-flex align-center">
                            <div>
                                <v-icon color="grey" icon="mdi-plus-circle" class="mr-5 ml-3"></v-icon>
                            </div>
                            <input placeholder="Type a task" class="the-add-task" v-model="task" @keyup.enter="addTask" @blur="addTask"/>
                        </div>
                        <div class="my-5">
                            <draggable
                                :list="filteredTasks"
                                :disabled="!isDraggable"
                                @end="updatePositions"
                                @start="collapseTasks"
                                item-key="id"
                                handle=".handle"
                            >
                                <template #item="{ element}">
                                    <the-card
                                        :model-value="element"
                                        :key="element.id"
                                        :labels="labels"
                                        :collapseTask="collapseTask"
                                        :isMobile="isMobile()"
                                        @editing:update="updateEditing"
                                        @task:update="updateTask"
                                        @task:delete="deleteTask"
                                        @task:archive="archiveTask"
                                        @comment:add="addComment"
                                        @comment:delete="deleteComment"
                                        @label:add="setLabel"
                                        @label:delete="deleteTaskLabel"
                                    ></the-card>
                                </template>
                            </draggable>
                        </div>
                    </v-col>
                </v-row>
                <v-row v-show="board.archivedTasks.length > 0">
                    <v-col class="offset-sm-0 v-col-sm-8 offset-sm-2 v-col-lg-8 offset-lg-2">
                        <div class="text-h5 text-grey font-weight-medium d-flex align-center" style="cursor: pointer" @click="toggleCollapseArchived">
                            <div>Archive {{ board.archivedTasks.length }}</div>
                            <v-icon :icon="this.collapseArchived ? 'mdi-chevron-up' : 'mdi-chevron-down'" class="ml-2"></v-icon>
                        </div>
                        <div class="my-5" v-show="collapseArchived">
                            <the-archived-task
                                v-for="(archivedTask, i) in filteredArchivedTasks"
                                :key="archivedTask.id"
                                v-model="filteredArchivedTasks[i]"
                                :labels="labels"
                                :isMobile="isMobile()"
                                @task:delete="deleteArchivedTask"
                                @task:restore="restoreTask"
                            ></the-archived-task>
                        </div>
                    </v-col>
                </v-row>
                <div class="text-center">
                    <v-dialog
                        v-model="labelDialog"
                        width="auto"
                    >
                        <v-card class="pa-5" min-width="250">
                            <div v-for="(label, i) in labels" :key="label.id">
                                <the-label
                                    v-model="labels[i]"
                                    @delete:label="deleteLabel"
                                    @update:label="updateLabel"
                                ></the-label>
                            </div>

                            <v-divider class="mt-2" v-show="isShowAddLabels"></v-divider>
                            <div class="mt-2" style="cursor: pointer" @click="addLabel" v-show="isShowAddLabels">Add label</div>
                        </v-card>
                    </v-dialog>
                </div>
            </v-container>
        </v-main>
        <v-bottom-navigation class="main-background" border="false" density="compact" elevation="0">
            <v-menu location="top" class="rounded-xl" :open-on-hover="!isMobile()" :open-on-click="isMobile()" :close-on-content-click="false" :transition="false">
                <template v-slot:activator="{ props }">
                    <v-btn value="statuses" v-bind="props">Statuses</v-btn>
                </template>

                <v-list class="rounded-lg" density="compact">
                    <v-list-item
                        v-for="(status, i) in allStatuses"
                        :key="i"
                        :value="status.value"
                        @click="updateStatusFilter(status.value)"
                    >
                        <v-sheet class="d-flex rounded-lg pa-1" :class="getFilterBackground(status)">
                            <v-icon :color="getFilterColor(status)" :icon="status.icon" class="mr-1"></v-icon>
                            <span>{{ getStatusCount(status.value) }}</span>
                        </v-sheet>
                    </v-list-item>
                    <v-list-item value="Reset" @click="resetStatusFilter()">Reset</v-list-item>
                </v-list>
            </v-menu>
            <v-menu location="top" class="rounded-lg" :open-on-hover="!isMobile()" :open-on-click="isMobile()" :close-on-content-click="false" :transition="false">
                <template v-slot:activator="{ props }">
                    <v-btn value="labels" v-bind="props" class="me-auto">Labels</v-btn>
                </template>

                <v-list class="rounded-lg" density="compact">
                    <v-list-item
                        v-for="(label, i) in labels"
                        :key="i"
                        :value="label.title"
                        @click="updateLabelFilter(label)"
                    >
                        <v-chip
                            label
                            class="font-weight-black mx-1"
                            size="x-small"
                            :color="label.color"
                            :value="label.id"
                            :text-color="getLabelTextColor(label)"
                            :variant="getLabelVariant(label)"
                        >{{ label.title }} {{ getLabelCount(label) }}</v-chip>
                    </v-list-item>
                    <v-list-item value="Add" @click="showLabelsEditor()" v-show="!haveLabels">Add label</v-list-item>
                    <v-list-item value="Edit" @click="showLabelsEditor()" v-show="haveLabels">Edit</v-list-item>
                    <v-list-item value="Reset" @click="resetLabelFilter()" v-show="haveLabels">Reset</v-list-item>
                </v-list>
            </v-menu>
            <v-btn value="access">Access</v-btn>
        </v-bottom-navigation>
    </v-app>
</template>

<script>

import TheTitle from './components/TheTitle.vue';
import TheCard from './components/TheCard.vue';
import TheArchivedTask from './components/TheArchivedTask.vue';
import TheLabel from './components/TheLabel.vue';
import axios from "axios";
import draggable from "vuedraggable";

import { getStatuses } from './utils';
import TheComment from "./components/TheComment.vue";

export default {
    name: "App",
    components: {TheComment, TheTitle, TheCard, TheLabel, TheArchivedTask, draggable},
    data() {
        return {
            board: {id: '', title: '', tasks: [], archivedTasks: []},
            labelDialog: false,
            labels: [],
            labelColors: [
                'indigo-accent-4',
                'deep-purple-accent-4',
                'red-darken-1',
                'amber-darken-4',
                'yellow-darken-2',
                'green-accent-4',
                'light-blue-accent-3',
            ],
            debug: '',
            task: '',
            filteredStatuses: [],
            filteredLabels: [],
            editing: false,
            collapseTask: false,
            collapseArchived: false,
        };
    },
    mounted() {
        this.syncTasks()
    },
    computed: {
        allStatuses() {
            return getStatuses();
        },
        statusesCount() {
            let statusesCount = [];

            this.allStatuses.forEach(status => {
                statusesCount[status.value] = 0;
            })

            this.board.tasks.forEach(currentTask => {
                statusesCount[currentTask.status] += 1;
            })

            return statusesCount
        },
        labelsCount() {
            let labelsCount = [];

            this.labels.forEach(label => {
                labelsCount[label.id] = 0;
            })

            this.board.tasks.forEach(currentTask => {
                currentTask.labels.forEach(label => {
                    labelsCount[label.label.id] += 1;
                })

            })

            return labelsCount
        },
        filteredTasks() {
            return this.filterTasks(this.board.tasks)
        },
        filteredArchivedTasks() {
            let filteredTasks = this.filterTasks(this.board.archivedTasks);

            if (filteredTasks.length !== 0 && filteredTasks.length < this.board.archivedTasks.length) {
                this.collapseArchived = true
            }

            return filteredTasks
        },
        isDraggable() {
            return this.filteredTasks.length === this.board.tasks.length && !this.editing;
        },
        haveLabels() {
            return this.labels.length > 0
        },
        isShowAddLabels() {
            return this.labels.length < this.labelColors.length
        }
    },
    methods: {
        syncTasks() {
            axios
                .get('/api/v1/board/' + window.location.pathname.substring(1))
                .then(response => {
                    this.board.id = response.data.id;
                    this.board.title = response.data.title;
                    this.board.tasks = response.data.tasks
                    this.board.archivedTasks = response.data.archivedTasks
                });

            axios
                .get('/api/v1/label/byBoard/' + window.location.pathname.substring(1))
                .then(response => {
                    this.labels = response.data
                });
        },
        filterTasks(tasks) {
            if (this.filteredStatuses.length > 0) {
                tasks = tasks.filter((task) => this.filteredStatuses.includes(task.status))
            }

            if (this.filteredLabels.length === 0) {
                return tasks
            }

            let result = [];

            tasks.forEach(task => {
                task.labels.every(label => {
                    if (this.filteredLabels.includes(label.label.id)) {
                        result.push(task)

                        return false
                    }

                    return true
                })
            })

            return result
        },
        updateTitle(title) {
            axios.put('/api/v1/board/' + this.board.id, {title: title});
        },
        addTask(event) {
            const value = event.target.value.trim()

            if (!value) {
                return
            }

            axios
                .post('/api/v1/task/', {boardId: this.board.id, title: value})
                .then(response => {
                    this.board.tasks.unshift(response.data)
                });

            this.task = '';
        },
        updateTask(task) {
            const value = task.title.trim()

            if (value.length === 0) {
                this.deleteTask(task)
            }

            axios.put('/api/v1/task/' + task.id, {title: task.title, state: task.status});
        },
        deleteTask(task) {
            axios
                .delete('/api/v1/task/' + task.id)
                .then(response => {
                    this.board.tasks = this.board.tasks.filter((item) => item.id !== task.id)
                });
        },
        deleteArchivedTask(task) {
            axios
                .delete('/api/v1/task/' + task.id)
                .then(response => {
                    this.board.archivedTasks = this.board.archivedTasks.filter((item) => item.id !== task.id)
                });
        },
        archiveTask(task) {
            axios
                .put('/api/v1/task/archive/' + task.id)
                .then(response => {
                    this.board.tasks = this.board.tasks.filter((item) => item.id !== task.id)
                    this.board.archivedTasks.unshift(task)
                });
        },
        restoreTask(task) {
            axios
                .delete('/api/v1/task/archive/' + task.id)
                .then(response => {
                    this.board.archivedTasks = this.board.archivedTasks.filter((item) => item.id !== task.id)
                    this.board.tasks.unshift(task)

                    this.updatePositions()
                });
        },
        addComment(comment) {
            const value = comment.content.trim()

            if (!value) {
                return
            }

            axios
                .post('/api/v1/comment/', {taskId: comment.taskId, content: value})
                .then(response => {
                    this.board.tasks.forEach(currentTask => {
                        if (currentTask.id === comment.taskId) {
                            currentTask.comments.push(response.data)
                        }
                    })
                });
        },
        deleteComment(comment) {
            axios
                .delete('/api/v1/comment/' + comment.id)
                .then(response => {
                    this.board.tasks.forEach(currentTask => {
                        if (currentTask.id === comment.taskId) {
                            currentTask.comments = currentTask.comments.filter((item) => item.id !== comment.id)
                        }
                    })
                });
        },
        setLabel(label) {
            axios
                .post('/api/v1/task-label/', {taskId: label.taskId, labelId: label.id})
                .then(response => {
                    this.board.tasks.forEach(currentTask => {
                        if (currentTask.id === label.taskId) {
                            currentTask.labels.unshift(response.data)
                        }
                    })
                });
        },
        deleteTaskLabel(label) {
            axios
                .delete('/api/v1/task-label/' + label.id)
                .then(response => {
                    this.board.tasks.forEach(currentTask => {
                        if (currentTask.id === label.taskId) {
                            currentTask.labels = currentTask.labels.filter((item) => item.id !== label.id)
                        }
                    })
                });
        },
        getStatusCount(status) {
            return this.statusesCount[status]
        },
        getLabelCount(label) {
            return this.labelsCount[label.id]
        },
        updateLabelFilter(label) {
            if (this.filteredLabels.includes(label.id)) {
                const index = this.filteredLabels.indexOf(label.id);
                if (index > -1) {
                    this.filteredLabels.splice(index, 1);
                }
            } else {
                this.filteredLabels.push(label.id)
            }
        },
        resetLabelFilter() {
            this.filteredLabels = [];
        },
        getLabelVariant(label) {
            if (this.filteredLabels.includes(label.id)) {
                return 'elevated'
            }

            return 'tonal'
        },
        getLabelTextColor(label) {
            if (this.filteredLabels.includes(label.id)) {
                return ''
            }

            return 'white'
        },
        updateStatusFilter(status) {
            if (this.filteredStatuses.includes(status)) {
                const index = this.filteredStatuses.indexOf(status);
                if (index > -1) {
                    this.filteredStatuses.splice(index, 1);
                }
            } else {
                this.filteredStatuses.push(status)
            }
        },
        resetStatusFilter() {
            this.filteredStatuses = [];
        },
        getFilterBackground(status) {
            if (this.filteredStatuses.includes(status.value)) {
                return 'bg-' + status.color
            }

            return ''
        },
        getFilterColor(status) {
            if (this.filteredStatuses.includes(status.value)) {
                return ''
            }

            return status.color
        },
        updatePositions() {
            this.collapseTask = false

            let positions = [];

            this.board.tasks.forEach((task, index) => {
                positions.push({taskId: task.id, position: this.board.tasks.length - index})
            })

            axios
                .put('/api/v1/task/position/', {
                    boardId: this.board.id,
                    positions: positions
                });
        },
        deleteLabel(label) {
            axios
                .delete('/api/v1/label/' + label.id)
                .then(response => {
                    this.labels = this.labels.filter((item) => item.id !== label.id)

                    this.board.tasks.forEach(currentTask => {
                        currentTask.labels = currentTask.labels.filter((item) => item.label.id !== label.id)
                    })

                    if (!this.haveLabels) {
                        this.labelDialog = false
                    }
                });
        },
        collapseTasks() {
            this.collapseTask = true
        },
        isMobile() {
            const toMatch = [
                /Android/i,
                /webOS/i,
                /iPhone/i,
                /iPad/i,
                /iPod/i,
                /BlackBerry/i,
                /Windows Phone/i
            ];

            return toMatch.some((toMatchItem) => {
                return navigator.userAgent.match(toMatchItem);
            });
        },
        updateEditing(value) {
            this.editing = value
        },
        showLabelsEditor() {
            if (!this.haveLabels) {
                this.addLabel()
            }

            this.labelDialog = true
        },
        addLabel() {
            let newLabelColor = '';

            if (!this.isShowAddLabels) {
                return
            }

            if (!this.haveLabels) {
                newLabelColor = this.labelColors[0]
            } else {
                const currentLabelColors = this.labels.map(label => label.color)

                this.labelColors.every((color) => {
                    if (!currentLabelColors.includes(color)) {
                        newLabelColor = color

                        return false
                    }

                    return true
                })
            }

            axios
                .post(
                    '/api/v1/label/',
                    {boardId: this.board.id, color: newLabelColor, title: 'label'}
                )
                .then(response => {
                    this.labels.push(response.data)
                });
        },
        updateLabel(label) {
            axios.put(
                '/api/v1/label/',
                { id: label.id, title: label.title }
            );

            this.syncTasks()
        },
        toggleCollapseArchived() {
            this.collapseArchived = !this.collapseArchived
        }
    }
};
</script>
<style>
    .main-background {
        background: #f0f0f0!important;
    }
    .the-add-task {
        background: #f0f0f0;
        outline: none;
        width: 100%;
    }
</style>