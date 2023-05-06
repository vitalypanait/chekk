<template>
    <v-app class="bg-grey-lighten-5" full-height>
        <v-main>
            <v-container class="mx-auto">
                <v-row>
                    <v-col class="offset-sm-0 v-col-md-8 offset-md-2 v-col-lg-8 offset-lg-2">
                        <the-title v-model="board.title" @updateTitle="updateTitle"></the-title>
                        <v-text-field
                          placeholder="Type a task"
                          prepend-icon="mdi-plus-circle"
                          variant="solo"
                          flat
                          class="ml-3 the-title"
                          v-model="task"
                          @keyup.enter="addTask"
                        ></v-text-field>
                        <div class="my-n5">
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
    </v-app>
</template>

<script>

import TheTitle from './components/TheTitle.vue';
import TheCard from './components/TheCard.vue';
import axios from "axios";

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
    }
};
</script>
<style>
.the-title .v-field--active input, .the-title .v-field input {
    background: #fafafa;
}
</style>