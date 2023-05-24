<template>
  <v-card class="my-2 py-2 rounded-lg the-card" elevation="0" style="cursor: pointer;">
    <v-sheet class="d-flex align-start" :class="isMobile() ? '' : 'handle'" @click="toggleCollapse">
      <the-status
          v-if="isTaskDisplay(display)"
          :class="getMarginDisplay()"
          v-model="modelValue.status"
          @update:modelValue="updateTask"
      ></the-status>
      <v-sheet v-if="isListDisplay(display)" :class="getMarginDisplay()" class="font-weight-bold" min-width="28">{{ index + 1 }}</v-sheet>
      <the-task-title
          :class="getTitleMarginDisplay()"
          class="the-task-title"
          v-model:title="modelValue.title"
          v-model:editable="editable"
          @update="updateTask"
      ></the-task-title>
      <v-badge
          v-if="modelValue.comments.length > 0"
          color="grey-lighten-3"
          class="mr-1"
          :content="modelValue.comments.length"
          inline
          style="margin-top: 2px!important"
      ></v-badge>
      <v-menu
          :open-on-hover="!isMobile()"
          :open-on-click="isMobile()"
          open-delay="50"
          :transition="false"
          :close-on-content-click="false"
      >
        <template v-slot:activator="{ props }">
          <v-icon icon="mdi-dots-vertical" v-bind="props" class="mr-2 handle" @click="showDelete"></v-icon>
        </template>

        <v-list class="rounded-lg">
          <v-list-item value="edit" density="compact">
            <div @click="makeEditable">Edit</div>
          </v-list-item>
          <v-list-item value="archive" density="compact">
            <div @click="archive">Archive</div>
          </v-list-item>
          <v-list-item value="delete" density="compact" v-show="!isConfirmingDelete">
            <div @click="showConfirm">Delete</div>
          </v-list-item>
          <v-list-item value="confirm" density="compact" v-show="isConfirmingDelete" @mouseleave="showDelete">
            <div @click="deleteTask" class="text-red">Confirm</div>
          </v-list-item>
          <v-divider v-show="labels.length > 0"></v-divider>
          <v-list-item density="compact" v-show="labels.length > 0">
            <div class="my-2">Labels</div>
            <v-chip
                v-for="label in labels"
                label
                class="font-weight-black mr-2"
                size="x-small"
                :color="label.color"
                text-color="white"
                @click.stop="mutateLabel(label)"
                :variant="getLabelVariant(label)"
            >{{ label.title }}
            </v-chip>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-sheet>
    <v-sheet :class="getLabelMarginDisplay()" class="mt-1" v-if="modelValue.labels.length > 0" @click="toggleCollapse">
      <div>
        <v-chip
            v-for="label in modelValue.labels"
            label
            class="font-weight-black mx-1"
            size="x-small"
            :color="label.label.color"
            text-color="white"
            @click.stop="deleteLabel(label)"
        >{{ label.label.title }}
        </v-chip>
      </div>
    </v-sheet>
    <v-sheet v-show="collapse">
      <tiptap
          v-model="comment"
          :class="getEditorMarginDisplay()"
          class="my-4 mr-10 text-body-2"
          @update:content="addComment"
      />
      <v-divider v-if="modelValue.comments.length > 0"></v-divider>
      <div v-for="(comment, i) in modelValue.comments" :key="comment.id">
        <the-comment
            v-model="modelValue.comments[i]"
            :cancel-delete="!collapse"
            :is-mobile="isMobile()"
            @comment:delete="deleteComment"
            @comment:make-as-task="makeAsTask"
            :class="getCommentMarginDisplay()"
            class="d-flex align-center mt-2 mr-3"
        ></the-comment>
      </div>
    </v-sheet>
  </v-card>
</template>

<script>

import TheComment from './TheComment.vue';
import TheStatus from './TheStatus.vue';
import TheTaskTitle from './TheTaskTitle.vue';
import Tiptap from './Tiptap.vue'
import {mobile} from '../mixins';
import {
  isListDisplay,
  isTaskDisplay,
  isContentDisplay
} from '../models';

export default {
  name: 'TheTask',
  components: {TheStatus, TheTaskTitle, Tiptap, TheComment},
  data() {
    return {
      collapse: false,
      editable: false,
      comment: '',
      isConfirmingDelete: false
    }
  },
  props: ['modelValue', 'labels', 'collapseTask', 'display', 'index'],
  emits: [
    'update:modelValue',
    'editing:update',
    'task:update',
    'task:delete',
    'task:archive',
    'comment:add',
    'comment:delete',
    'comment:makeAsTask',
    'label:add',
    'label:delete'
  ],
  mixins: [mobile],
  watch: {
    editable(newValue, oldValue) {
      this.$emit('editing:update', newValue)
    },
    collapseTask(newValue, oldValue) {
      if (newValue) {
        this.collapse = false
      }
    }
  },
  computed: {
    currentLabelColors() {
      return this.modelValue.labels.map(label => label.label.color)
    }
  },
  methods: {
    isListDisplay,
    isTaskDisplay,
    isContentDisplay,
    toggleCollapse() {
      this.collapse = !this.collapse;
    },
    makeEditable() {
      this.editable = true;
    },
    updateTask() {
      this.$emit('task:update', this.modelValue)
    },
    deleteTask() {
      this.$emit('task:delete', this.modelValue)
    },
    addComment(value) {
      value = value.trim()

      if (!value) {
        return
      }

      this.$emit('comment:add', {
        taskId: this.modelValue.id,
        content: value
      })

      this.comment = '';
    },
    deleteComment(id) {
      this.$emit('comment:delete', {
        id: id,
        taskId: this.modelValue.id
      })
    },
    makeAsTask(comment) {
      this.$emit('comment:makeAsTask', {
        id: comment.id,
        content: comment.content,
        taskId: this.modelValue.id
      })
    },
    mutateLabel(label) {
      let labelToDelete = null;

      this.modelValue.labels.forEach(taskLabel => {
        if (taskLabel.label.id === label.id) {
          labelToDelete = taskLabel;
        }
      });

      if (labelToDelete === null) {
        this.$emit('label:add', {
          id: label.id,
          taskId: this.modelValue.id
        })
      } else {
        this.deleteLabel(labelToDelete)
      }
    },
    deleteLabel(label) {
      this.$emit('label:delete', label)
    },
    getMenuCollapseText() {
      return this.collapse ? 'Collapse' : 'Expand'
    },
    showConfirm() {
      this.isConfirmingDelete = true
    },
    showDelete() {
      this.isConfirmingDelete = false
    },
    archive() {
      this.$emit('task:archive', this.modelValue)
    },
    getLabelVariant(label) {
      return this.currentLabelColors.includes(label.color) ? 'elevated' : 'tonal'
    },
    getMarginDisplay() {
      if (isTaskDisplay(this.display) || isContentDisplay(this.display)) {
        return 'ml-3'
      } else if (isListDisplay(this.display)) {
        return 'ml-4'
      }
    },
    getTitleMarginDisplay() {
      if (isTaskDisplay(this.display)) {
        return 'ml-5'
      } else if (isListDisplay(this.display) || isContentDisplay(this.display)) {
        return 'ml-3'
      }
    },
    getLabelMarginDisplay() {
      if (isTaskDisplay(this.display) || isListDisplay(this.display)) {
        return 'ml-13'
      } else if (isContentDisplay(this.display)) {
        return 'ml-2'
      }
    },
    getEditorMarginDisplay() {
      if (isTaskDisplay(this.display) || isListDisplay(this.display)) {
        return 'ml-14'
      } else if (isContentDisplay(this.display)) {
        return 'ml-3'
      }
    },
    getCommentMarginDisplay() {
      if (isTaskDisplay(this.display) || isListDisplay(this.display)) {
        return 'ml-14'
      } else if (isContentDisplay(this.display)) {
        return 'ml-3'
      }
    }
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
.the-task-title {
  outline: none;
  width: 100%;
  font-size: 16px;
  line-height: 20px;
  margin-top: 2px;
}
</style>
