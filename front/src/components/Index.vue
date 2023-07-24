<template>
  <div class="text-center my-auto" v-if="!isDataReady">
    <v-progress-circular indeterminate color="grey" :size="47"></v-progress-circular>
  </div>
  <v-container class="mx-auto pt-12" v-if="isDataReady">
    <v-row class="mb-2" v-if="hasMyBoards">
      <v-col class="offset-sm-0 v-col-lg-8 offset-lg-2">
        <div :class="isMobile() ? 'the-title-mobile' : 'the-title'" class="mb-3 ml-1">My boards</div>
        <div class="my-5">
          <the-board-item
              v-for="(board, i) in my" :key="board.id"
              v-model="my[i]"
              class="mt-2"
              @board:delete="deleteBoard"
          ></the-board-item>
        </div>
      </v-col>
    </v-row>
    <v-row>
      <v-col class="offset-sm-0 v-col-lg-8 offset-lg-2">
        <div :class="isMobile() ? 'the-title-mobile' : 'the-title'" class="mb-3 ml-1">Visited</div>
        <div v-if="!user.authorized">
          Only stored on this device. <a href="/auth">Log in</a> to access this list from any device.
        </div>
        <div class="my-5">
          <the-board-item
              v-for="(board, i) in visited" :key="board.id"
              v-model="visited[i]"
              class="mt-2"
              @board:delete="deleteBoard"
          ></the-board-item>
        </div>
      </v-col>
    </v-row>
  </v-container>
  <div class="text-center">
    <v-dialog
        v-model="openInAppDialog"
        width="auto"
    >
      <v-card class="pa-5" min-width="250">
        <v-text-field variant="underlined" @keyup.enter="openBoard"></v-text-field>
      </v-card>
    </v-dialog>
  </div>
  <div class="the-open-in-app">
    <v-icon icon="mdi-open-in-app" color="black" class="ml-4 mt-4" size="large" style="cursor: pointer" @click="openInApp()"></v-icon>
  </div>
  <div class="toolbar-right d-flex mt-4">
      <v-icon icon="mdi-account-circle" color="black" class="mr-2" size="large" @click="auth()" v-if="!user.authorized && isDataReady"></v-icon>
      <v-icon icon="mdi-logout-variant" color="black" class="mr-2" size="large" @click="logout()" v-if="user.authorized && isDataReady"></v-icon>
      <v-icon icon="mdi-plus-circle" color="black" class="mr-4" size="large" @click="createNewBoard()"></v-icon>
  </div>
</template>

<script>
import axios from "axios";
import TheBoardItem from "./TheBoardItem.vue";
import {mobile, user} from "../mixins/index.js";
import TheLabel from "./TheLabel.vue";

export default {
  name: 'Index',
  components: {TheLabel, TheBoardItem},
  data() {
    return {
      visited: [],
      my: [],
      openInAppDialog: false,
      isDataReady: false
    };
  },
  mixins: [mobile, user],
  mounted() {
    this.fetchAll();
  },
  computed: {
    hasMyBoards() {
      return this.my.length > 0;
    }
  },
  methods: {
    async fetchAll() {
      axios
          .get('/api/v1/boards/')
          .then(response => {
            this.visited = response.data.visited
            this.my = response.data.my

            this.isDataReady = true
          });
    },
    createNewBoard() {
      window.location.href = '/create';
    },
    deleteBoard(id) {
      axios
          .delete('/api/v1/board/' + id)
          .then(response => {
            this.fetchAll()
          });
    },
    openInApp() {
      this.openInAppDialog = true;
    },
    openBoard(event) {
      this.openInAppDialog = false;

      this.$router.push('/' + event.target.value)
    },
    auth() {
      this.$router.push('/auth')
    },
    logout() {
      window.location.href = '/logout';
    }
  }
};
</script>
<style>
.toolbar-right {
  top: 0px;
  z-index: 1004;
  position: fixed;
  right: 0px;
}
.the-open-in-app {
  top: 0px;
  z-index: 1004;
  position: fixed;
  left: 0px;
}
</style>