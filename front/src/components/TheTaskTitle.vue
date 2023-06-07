<template>
  <div>
    <div v-show="!isEditable" class="me-auto pr-3">{{ title }}</div>
    <input placeholder="Add a task"
           class="the-task-title-edit"
           v-show="isEditable"
           :value="title"
           @click.stop="trySomething()"
           @input="$emit('update:title', $event.target.value)"
           @keyup.enter="update"
           @blur="update"
           ref="taskTitle"
    />
  </div>
</template>

<script>
export default {
  name: 'TheTaskTitle',
  props: ['title', 'editable', 'readOnly'],
  emits: ['update:title', 'update:editable', 'update'],
  computed: {
    isEditable() {
      if (this.readOnly) {
        return;
      }

      if (this.editable) {
        this.$nextTick(() => {
          this.$refs.taskTitle.focus()
        })
      }

      return this.editable;
    }
  },
  methods: {
    update() {
      this.$emit('update', this.title);
      this.$emit('update:editable', false);
    },
    trySomething() {

    }
  }
};
</script>
<style>
.the-task-title-edit {
  font-size: 16px !important;
  line-height: 20px !important;
  outline: none;
  width: 100%;
}

</style>