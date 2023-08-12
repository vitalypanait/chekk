<template>
  <div>
    <div v-show="!isEditable" v-html="content"></div>
    <tiptap
        v-show="isEditable"
        :model-value="content"
        :editable="isEditable"
        class="text-body-2"
        @update:content="update"
        v-if="!readOnly"
        ref="commentContent"
    />
  </div>
</template>

<script>
import Tiptap from "./Tiptap.vue";

export default {
  name: 'TheCommentContent',
  components: {Tiptap},
  props: ['content', 'editable', 'readOnly'],
  emits: ['content:update', 'editable:update', 'update'],
  computed: {
    isEditable() {
      if (this.readOnly) {
        return;
      }

      return this.editable;
    }
  },
  methods: {
    update(content) {
      this.$emit('update', content);
      this.$emit('editable:update', false);
    },
    trySomething() {

    }
  }
};
</script>