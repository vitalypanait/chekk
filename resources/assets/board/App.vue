<template>
    <v-layout>
        <v-main>
            <v-container>
                <v-sheet class="mx-auto" max-width="700px">
                    <the-title v-model="board.title" @updateTitle="updateTitle"></the-title>
                    <v-text-field
                        label="Type a task"
                        prepend-icon="mdi-plus-circle"
                        variant="underlined"
                        class="ml-4"
                        v-model="task"
                        @keyup.enter="addTask"
                    ></v-text-field>
                    <div v-for="(task, i) in board.tasks" :key="task.id">
                        <the-task
                            v-model="board.tasks[i]"
                            @task:update="updateTask"
                            @task:delete="deleteTask"
                            @comment:add="addComment"
                            @comment:delete="deleteComment"
                        ></the-task>
                    </div>
                    <v-btn @click="checkBoard" class="mt-10" size="small">Проверить доску</v-btn>
                    <div>{{ debug }}</div>
                </v-sheet>
            </v-container>
        </v-main>
    </v-layout>
</template>

<script>

import TheTask from './components/TheTask.vue';
import TheTitle from './components/TheTitle.vue';
import axios from "axios";

export default {
    name: "App",
    components: {TheTask, TheTitle},
    data() {
        return {
            board: {id: '', title: '', tasks: []},
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
        checkBoard() {
            this.debug = JSON.stringify(this.board)
        },
    }
};
</script>