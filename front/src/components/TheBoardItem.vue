<template>
  <v-card class="my-2 py-2 rounded-lg the-card" elevation="0" style="cursor: pointer;">
    <v-sheet class="d-flex align-start">
      <v-icon icon="mdi-link" color="grey" class="ml-3" style="cursor: pointer" @click="moveToBoard()"></v-icon>
      <div class="me-auto pr-3 the-board-title ml-5" @click="moveToBoard()">{{ modelValue.title === null ? 'No title' : modelValue.title }}</div>
      <v-chip v-if="modelValue.readOnly" size="x-small" class="pl-3 pr-5 mr-2" style="margin-top: 2px!important" color="grey">read-only</v-chip>
      <v-menu
          :open-on-hover="!isMobile()"
          :open-on-click="isMobile()"
          open-delay="50"
          :transition="false"
          :close-on-content-click="false"
      >
        <template v-slot:activator="{ props }">
          <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2"></v-icon>
        </template>

        <v-list class="rounded-lg">
          <v-list-item value="delete" density="compact" v-show="!isConfirmingDelete">
            <div @click="showConfirm">Delete</div>
          </v-list-item>
          <v-list-item value="confirm" density="compact" v-show="isConfirmingDelete" @mouseleave="showDelete">
            <div @click="deleteBoard" class="text-red">Confirm</div>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-sheet>
  </v-card>
</template>

<script>

import { mobile } from '../mixins';

export default {
  name: 'TheBoardItem',
  props: ['modelValue'],
  emits: ['board:delete'],
  mixins: [mobile],
  data() {
    return {
      isConfirmingDelete: false
    }
  },
  methods: {
    moveToBoard() {
      this.$router.push('/' + this.modelValue.id)
    },
    showConfirm() {
      this.isConfirmingDelete = true
    },
    showDelete() {
      this.isConfirmingDelete = false
    },
    deleteBoard() {
      this.$emit('board:delete', this.modelValue.id)
    },
  }
};
</script>
<style>
.the-card {
  border: 1px solid white;
}

.the-card:hover {
  border: 1px solid rgba(0, 0, 0, 0.12)
}

.the-board-title {
  outline: none;
  width: 100%;
  font-size: 16px;
  line-height: 20px;
  margin-top: 2px;
}
</style>
