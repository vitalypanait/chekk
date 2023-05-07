<template>
    <v-app class="main-background" full-height>
        <v-main>
            <v-container class="mx-auto">
                <v-row>
                    <v-col class="offset-sm-0 v-col-md-8 offset-md-2 v-col-lg-8 offset-lg-2">
                        <the-title v-model="board.title" @updateTitle="updateTitle"></the-title>
                        <div class="d-flex align-center">
                            <div>
                                <v-icon color="grey" icon="mdi-plus-circle" class="mr-5 ml-3"></v-icon>
                            </div>
                            <input placeholder="Type a task" class="the-add-task" v-model="task" @keyup.enter="addTask" @blur="addTask"/>
                        </div>
                        <div class="my-5">
                            <the-card
                                v-for="(task, i) in filteredTasks"
                                v-model="filteredTasks[i]"
                                :key="task.id"
                                :labels="labels"
                                @task:update="updateTask"
                                @task:delete="deleteTask"
                                @comment:add="addComment"
                                @comment:delete="deleteComment"
                                @label:add="setLabel"
                                @label:delete="deleteLabel"
                            ></the-card>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
        <v-bottom-navigation class="main-background" border="false" density="compact" elevation="0">
            <v-menu location="top" class="rounded-xl" open-on-hover :close-on-content-click="false">
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
            <v-menu location="top" class="rounded-lg" open-on-hover :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn value="labels" v-bind="props" class="me-auto">Labels</v-btn>
                </template>

                <v-list class="rounded-xl" density="compact">
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
                        >{{ label.title }}</v-chip>
                    </v-list-item>
                    <v-list-item value="Reset" @click="resetLabelFilter()">Reset</v-list-item>
                </v-list>
            </v-menu>
            <v-btn value="access">Access</v-btn>
        </v-bottom-navigation>
    </v-app>
</template>

<script>

import TheTitle from './components/TheTitle.vue';
import TheCard from './components/TheCard.vue';
import axios from "axios";
import { getStatuses } from './utils';

export default {
    name: "App",
    components: {TheTitle, TheCard},
    data() {
        return {
            board: {id: '', title: '', tasks: []},
            labels: [],
            debug: '',
            task: '',
            filteredStatuses: [],
            filteredLabels: [],
        };
    },
    mounted() {
        axios
            .get('/api/v1/board/' + window.location.pathname.substring(1))
            .then(response => {
                this.board.id = response.data.id;
                this.board.title = response.data.title;
                this.board.tasks = response.data.tasks
            });

        axios
            .get('/api/v1/label/byBoard/' + window.location.pathname.substring(1))
            .then(response => {
                this.labels = response.data
            });
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
        filteredTasks() {
            let filtered = this.board.tasks

            if (this.filteredStatuses.length > 0) {
                filtered = filtered.filter((task) => this.filteredStatuses.includes(task.status))
            }

            if (this.filteredLabels.length > 0) {
                filtered = filtered.filter((task) => task.labels.filter((label) => this.filteredStatuses.includes(label.label.id)))
            }

            return filtered
        }
    },
    methods: {
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
                            currentTask.comments.unshift(response.data)
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
        deleteLabel(label) {
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
                return 'default'
            }

            return 'outlined'
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
    }
</style>