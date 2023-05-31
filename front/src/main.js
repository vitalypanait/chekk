import { createApp } from "vue";

import Index from './components/Index.vue'
import Board from './components/Board.vue'

import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'

import { createRouter, createWebHistory } from 'vue-router'

import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import App from "./App.vue";

const app = createApp(App)

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Index, name: 'index' },
        { path: '/:id', component: Board, name: 'about' },
    ]
})

const vuetify = createVuetify({
    components,
    directives,
})

app.use(router)
app.use(vuetify)
app.mount("#app");