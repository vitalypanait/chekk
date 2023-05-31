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
</template>

<script>
import axios from "axios";
import TheBoardItem from "./TheBoardItem.vue";
import {mobile} from "../mixins/index.js";

export default {
  name: 'Index',
  components: {TheBoardItem},
  data() {
    return {
      boards: {id: '', title: ''},
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
            this.boards = response.data
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
    }
  }
};
</script>