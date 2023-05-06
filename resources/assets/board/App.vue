<template>
    <v-app class="bg-grey-lighten-4" full-height>
        <v-main>
            <v-container class="mx-auto">
                <v-row>
                    <v-col class="offset-sm-0 v-col-md-8 offset-md-2 v-col-lg-8 offset-lg-2">
                        <the-title v-model="board.title" @updateTitle="updateTitle"></the-title>
                        <div class="d-flex align-center">
                            <div>
                                <v-icon color="grey" icon="mdi-plus-circle" class="mr-5 ml-3"></v-icon>
                            </div>
                            <input placeholder="Type a task" class="the-title" v-model="task" @keyup.enter="addTask" @blur="addTask"/>
                        </div>
                        <div class="my-5">
                            <div v-for="(task, i) in board.tasks" :key="task.id">
                                <the-card
                                  v-model="board.tasks[i]"
                                  :labels="labels"
                                  @task:update="updateTask"
                                  @task:delete="deleteTask"
                                  @comment:add="addComment"
                                  @comment:delete="deleteComment"
                                  @label:add="setLabel"
                                  @label:delete="deleteLabel"
                                ></the-card>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
        <v-bottom-navigation bg-color="grey-lighten-4" border="false" density="compact" elevation="0">
            <v-menu location="top" class="rounded-xl">
                <template v-slot:activator="{ props }">
                    <v-btn value="statuses" v-bind="props">Statuses</v-btn>
                </template>

                <v-list class="rounded-xl">
                    <v-list-item
                        v-for="(status, i) in allStatuses"
                        :key="i"
                        :value="status.value"
                    >
                        <v-sheet class="d-flex">
                            <v-icon :color="status.color" :icon="status.icon" class="mr-1"></v-icon>
                            <span>{{ getStatusCount(status.value) }}</span>
                        </v-sheet>
                    </v-list-item>
                    <v-list-item value="Reset">Reset</v-list-item>
                </v-list>
            </v-menu>

            <v-btn value="labels" class="me-auto">Labels</v-btn>
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
        }
    }
};
</script>
<style>
    .the-title {
        background: #f5f5f5;
        outline: none;
    }
</style>