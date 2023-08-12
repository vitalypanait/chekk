<template>
  <v-sheet>
    <the-comment-content
        class="me-auto text-body-2"
        style="max-width: 95%"
        :readOnly="readOnly"
        v-model:content="modelValue.content"
        v-model:editable="editable"
        @update="updateComment"
    ></the-comment-content>
    <v-menu
        :open-on-hover="!isMobile"
        :open-on-click="isMobile"
        open-delay="50"
        :transition="false"
        :close-on-content-click="false"
    >
      <template v-slot:activator="{ props }">
        <v-icon v-if="!readOnly" size="small" color="grey-lighten-1" icon="mdi-dots-vertical" v-bind="props"
                class="ml-3 handle" @click="showDelete"></v-icon>
      </template>

      <v-list class="rounded-lg">
        <v-list-item value="edit" density="compact">
          <div @click="makeEditable">Edit</div>
        </v-list-item>
        <v-list-item density="compact" value="makeAsTask">
          <div @click="makeAsTask">Make as a task</div>
        </v-list-item>
        <v-list-item density="compact" value="delete" v-show="!isConfirmingDelete">
          <div @click="showConfirm">Delete</div>
        </v-list-item>
        <v-list-item density="compact" value="confirm" v-show="isConfirmingDelete" @mouseleave="showDelete">
          <div @click="deleteComment()" class="text-red">Confirm</div>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-sheet>
</template>

<script>

import TheCommentContent from "./TheCommentContent.vue";

export default {
  name: 'TheComment',
  components: {TheCommentContent},
  data() {
    return {
      isConfirmingDelete: false,
      editable: false,
    }
  },
  props: ['modelValue', 'cancelDelete', 'isMobile', 'readOnly'],
  emits: [
    'update:modelValue',
    'comment:delete',
    'comment:makeAsTask',
    'comment:update'
  ],
  watch: {
    cancelDelete(newValue) {
      if (newValue) {
        this.isConfirmingDelete = false
      }
    }
  },
  methods: {
    deleteComment(id) {
      this.$emit('comment:delete', this.modelValue.id)
    },
    showConfirm() {
      this.isConfirmingDelete = true
    },
    showDelete() {
      this.isConfirmingDelete = false
    },
    makeAsTask() {
      this.$emit('comment:makeAsTask', this.modelValue)
    },
    makeEditable() {
      this.editable = true;
    },
    updateComment(content) {
      this.modelValue.content = content

      this.$emit('comment:update', this.modelValue)
    },
  }
};
</script>
