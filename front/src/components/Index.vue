<template>
  <v-container class="mx-auto">
    <v-row>
      <v-col class="offset-sm-0 v-col-sm-8 offset-sm-2 v-col-lg-8 offset-lg-2">
        <div
            :class="isMobile() ? 'the-title-mobile' : 'the-title'"
            class="mt-7 mb-8 ml-1"
        >Previously viewed
        </div>
        <div class="d-flex align-center">
          <v-icon color="blue" icon="mdi-plus-circle" class="mr-5 ml-3" @click="moveToCreate()"
                  style="cursor: pointer"></v-icon>
          <div class="text-blue" @click="moveToCreate()" style="cursor: pointer">New board</div>
        </div>
        <div class="my-5">
          <the-board-item
              v-for="(board, i) in boards" :key="board.id"
              v-model="boards[i]"
              class="mt-2 mr-3"
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
    <v-icon icon="mdi-open-in-app" color="black" class="ml-4 mt-4" size="large" style="cursor: pointer" @click="openInApp"></v-icon>
  </div>
</template>

<script>
import axios from "axios";
import TheBoardItem from "./TheBoardItem.vue";
import {mobile} from "../mixins/index.js";
import TheLabel from "./TheLabel.vue";

export default {
  name: 'Index',
  components: {TheLabel, TheBoardItem},
  data() {
    return {
      boards: {id: '', title: ''},
      openInAppDialog: false
    };
  },
  mixins: [mobile],
  mounted() {
    this.fetchAll();
  },
  methods: {
    fetchAll() {
      axios
          .get('/api/v1/boards/')
          .then(response => {
            this.boards = response.data.boards
          });
    },
    moveToCreate() {
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
    }
  }
};
</script>
<style>
.the-user {
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