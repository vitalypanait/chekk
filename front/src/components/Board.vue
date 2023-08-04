<template>
  <div class="text-center my-auto" v-if="!isDataReady">
    <v-progress-circular indeterminate color="grey" :size="47"></v-progress-circular>
  </div>
  <div class="text-center">
    <v-dialog
        v-model="openPinCodeDialog"
        width="auto"
    >
      <v-card class="pa-5" min-width="250" max-width="300">
        <div>To access the board, enter the pin code</div>
        <v-form @submit.prevent="pinCodeSubmit" validate-on="submit lazy" ref="form">
          <v-text-field :rules="pinCodeRules" v-model="pinCode" type="string" placeholder="" variant="underlined"></v-text-field>
          <v-btn :loading="pinCodeLoading" type="submit" variant="tonal" width="100%" class="mt-2 text-body-1 font-weight-medium">Enter</v-btn>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
  <v-container class="mx-auto" v-if="isDataReady">
    <v-row>
      <v-col class="offset-sm-0 v-col-lg-8 offset-lg-2">
        <the-title v-model="board.title" @updateTitle="updateTitle" :readOnly="board.readOnly"></the-title>
        <div class="d-flex align-center" v-if="!board.readOnly">
          <div>
            <v-icon color="grey" icon="mdi-plus-circle" class="mr-5 ml-3"></v-icon>
          </div>
          <input placeholder="Type a task" class="the-add-task" v-model="task" @keyup.enter="addTask($event.target.value)" @blur="addTask($event.target.value)"/>
        </div>
        <div class="my-5">
          <draggable
              :list="filteredTasks"
              :disabled="!isDraggable || board.readOnly"
              @end="updatePositions"
              @start="collapseTasks"
              item-key="id"
              handle=".handle"
          >
            <template #item="{index, element}">
              <the-card
                  :model-value="element"
                  :key="element.id"
                  :labels="labels"
                  :collapseTask="collapseTask"
                  :display="board.display"
                  :index="index"
                  :readOnly="board.readOnly"
                  @editing:update="updateEditing"
                  @task:update="updateTask"
                  @task:delete="deleteTask"
                  @task:archive="archiveTask"
                  @comment:add="addComment"
                  @comment:delete="deleteComment"
                  @comment:make-as-task="makeAsTask"
                  @label:add="setLabel"
                  @label:delete="deleteTaskLabel"
              ></the-card>
            </template>
          </draggable>
        </div>
      </v-col>
    </v-row>
    <v-row v-show="board.archivedTasks.length > 0" class="pb-16">
      <v-col class="offset-sm-0 v-col-lg-8 offset-lg-2">
        <div class="text-h5 text-grey font-weight-medium d-flex align-center" style="cursor: pointer"
             @click="toggleCollapseArchived">
          <div>Archive {{ board.archivedTasks.length }}</div>
          <v-icon size="small" :icon="this.collapseArchived ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                  class="ml-2"></v-icon>
        </div>
        <div class="my-5" v-show="collapseArchived">
          <the-archived-task
              v-for="(archivedTask, i) in filteredArchivedTasks"
              :key="archivedTask.id"
              v-model="filteredArchivedTasks[i]"
              :labels="labels"
              :display="board.display"
              :index="i"
              :readOnly="board.readOnly"
              @task:delete="deleteArchivedTask"
              @task:restore="restoreTask"
          ></the-archived-task>
        </div>
      </v-col>
    </v-row>
    <div class="text-center">
      <v-dialog
          v-model="labelDialog"
          width="auto"
      >
        <v-card class="pa-5" min-width="250">
          <div v-for="(label, i) in labels" :key="label.id">
            <the-label
                v-model="labels[i]"
                @delete:label="deleteLabel"
                @update:label="updateLabel"
            ></the-label>
          </div>

          <v-divider class="mt-2" v-show="isShowAddLabels"></v-divider>
          <div class="mt-2" style="cursor: pointer" @click="addLabel" v-show="isShowAddLabels">Add label</div>
        </v-card>
      </v-dialog>
    </div>
  </v-container>
  <div class="the-home">
    <v-icon icon="mdi-home-circle" color="black" class="ml-4 mt-4" size="large" style="cursor: pointer" @click="$router.push('/')"></v-icon>
  </div>
  <div class="the-footer" v-if="isDataReady">
    <div class="the-footer__content">
      <the-filters v-show="isBoardHasTypeTask" v-model="this.filteredStatuses" :tasks="board.tasks"></the-filters>
      <the-labels
          v-show="isBoardHasTypeTask"
          v-model="this.filteredLabels"
          :tasks="board.tasks"
          :labels="labels"
          :read-only="board.readOnly"
          @mutate="showLabelsEditor()"
      ></the-labels>
      <the-settings v-if="!board.readOnly" v-model:display="board.display" @update:display="updateDisplay"></the-settings>
      <the-access
          v-if="!board.readOnly"
          :read-only-url="board.readOnlyUrl"
          :ownership="board.ownership"
          :is-owner="board.isOwner"
          :has-pin-code="board.hasPinCode"
          @take-ownership="takeOwnership"
          @pin-code:set-up="setPinCode"
          @pin-code:remove="removePinCode"
      ></the-access>
    </div>
  </div>
</template>

<script>

import TheTitle from './TheTitle.vue';
import TheCard from './TheCard.vue';
import TheArchivedTask from './TheArchivedTask.vue';
import TheLabel from './TheLabel.vue';
import draggable from "vuedraggable";
import {mobile} from "../mixins";

import {DISPLAY_TASK, isTaskDisplay, LABEL_COLORS} from '../models';
import TheComment from "./TheComment.vue";
import TheSettings from "./menu/TheSettings.vue";
import TheAccess from "./menu/TheAccess.vue";
import TheFilters from "./menu/TheFilters.vue";
import TheLabels from "./menu/TheLabels.vue";
import { EntityApi, BoardApi } from "../api";

const api = new EntityApi();
const boardApi = new BoardApi();

export default {
  name: "Board",
  components: {TheSettings, TheAccess, TheFilters, TheLabels, TheComment, TheTitle, TheCard, TheLabel, TheArchivedTask, draggable},
  data() {
    return {
      board: {
        id: '',
        title: '',
        display: '',
        tasks: [],
        archivedTasks: [],
        readOnly: false,
        readOnlyUrl: null,
        ownership: false,
        isOwner: false,
        hasPinCode: false
      },
      invalidPinCode: false,
      labelDialog: false,
      labels: [],
      debug: '',
      task: '',
      filteredStatuses: [],
      filteredLabels: [],
      editing: false,
      collapseTask: false,
      collapseArchived: false,
      isDataReady: false,
      openPinCodeDialog: false,
      pinCodeLoading: false,
      pinCode: null,
      pinCodeRules: [
        v => !v || (v.length === 6 && !this.invalidPinCode) || 'Pin code is invalid'
      ]
    };
  },
  async mounted() {
    let access = await boardApi.checkAccess(window.location.pathname.substring(1));

    if (access.access) {
      await this.syncTasks()
    } else {
      this.openPinCodeDialog = true
    }
  },
  computed: {
    filteredTasks() {
      return this.filterTasks(this.board.tasks)
    },
    filteredArchivedTasks() {
      let filteredTasks = this.filterTasks(this.board.archivedTasks);

      if (filteredTasks.length !== 0 && filteredTasks.length < this.board.archivedTasks.length) {
        this.collapseArchived = true
      }

      return filteredTasks
    },
    isDraggable() {
      return this.filteredTasks.length === this.board.tasks.length && !this.editing;
    },
    haveLabels() {
      return this.labels.length > 0
    },
    isShowAddLabels() {
      return this.labels.length < LABEL_COLORS.length
    },
    isBoardHasTypeTask() {
      return this.board.display === DISPLAY_TASK;
    },
  },
  mixins: [mobile],
  methods: {
    async syncTasks() {
      const id = window.location.pathname.substring(1);

      this.board = await api.getBoard(id);
      this.labels = await api.getLabelsByBoard(id);

      this.isDataReady = true;
    },
    filterTasks(tasks) {
      if (this.filteredStatuses.length > 0) {
        tasks = tasks.filter((task) => this.filteredStatuses.includes(task.status))
      }

      if (this.filteredLabels.length === 0) {
        return tasks
      }

      let result = [];

      tasks.forEach(task => {
        task.labels.every(label => {
          if (this.filteredLabels.includes(label.label.id)) {
            result.push(task)

            return false
          }

          return true
        })
      })

      return result
    },
    async updateTitle(title) {
      await api.updateTitle(this.board.id, {title: title, display: this.board.display});
    },
    async addTask(value) {
      let title = value.trim()

      if (!title) {
        return
      }

      const task = await api.addTask(this.board.id, {boardId: this.board.id, title: title})

      this.board.tasks.unshift(task)
      this.task = '';
    },
    async updateTask(task) {
      const value = task.title.trim()

      if (value.length === 0) {
        await this.deleteTask(task)
      }

      await api.updateTask(this.board.id, task.id, {title: task.title, state: task.status})
    },
    async deleteTask(task) {
      await api.deleteTask(task.id)

      this.board.tasks = this.board.tasks.filter((item) => item.id !== task.id)
    },
    async deleteArchivedTask(task) {
      await api.deleteTask(this.board.id, task.id)

      this.board.archivedTasks = this.board.archivedTasks.filter((item) => item.id !== task.id)
    },
    async archiveTask(task) {
      await api.archiveTask(this.board.id, task.id)

      this.board.tasks = this.board.tasks.filter((item) => item.id !== task.id)
      this.board.archivedTasks.unshift(task)
    },
    async restoreTask(task) {
      await api.restoreTask(this.board.id, task.id)

      this.board.archivedTasks = this.board.archivedTasks.filter((item) => item.id !== task.id)
      this.board.tasks.unshift(task)

      await this.updatePositions()
    },
    async addComment(comment) {
      const value = comment.content.trim()

      if (!value) {
        return
      }

      const newComment = await api.addComment(this.board.id,{taskId: comment.taskId, content: value});

      this.board.tasks.forEach(currentTask => {
        if (currentTask.id === comment.taskId) {
          currentTask.comments.push(newComment)
        }
      })
    },
    async deleteComment(comment) {
      await api.deleteComment(this.board.id, comment.id)

      this.board.tasks.forEach(currentTask => {
        if (currentTask.id === comment.taskId) {
          currentTask.comments = currentTask.comments.filter((item) => item.id !== comment.id)
        }
      })
    },
    async makeAsTask(comment) {
      await api.deleteComment(this.board.id, comment.id)

      this.board.tasks.forEach(currentTask => {
        if (currentTask.id === comment.taskId) {
          currentTask.comments = currentTask.comments.filter((item) => item.id !== comment.id)

          this.addTask(comment.content.replace(/<[^>]*>?/gm, ''))
        }
      })
    },
    async setLabel(label) {
      const newLabel = await api.setLabel(this.board.id, {taskId: label.taskId, labelId: label.id})

      this.board.tasks.forEach(currentTask => {
        if (currentTask.id === label.taskId) {
          currentTask.labels.unshift(newLabel)
        }
      })
    },
    deleteTaskLabel(label) {
      api.deleteTaskLabel(this.board.id, label.id)

      this.board.tasks.forEach(currentTask => {
        if (currentTask.id === label.taskId) {
          currentTask.labels = currentTask.labels.filter((item) => item.id !== label.id)
        }
      })
    },
    async updatePositions() {
      this.collapseTask = false

      let positions = [];

      this.board.tasks.forEach((task, index) => {
        positions.push(this.board.id, {taskId: task.id, position: this.board.tasks.length - index})
      })

      await api.updatePositions({boardId: this.board.id, positions: positions})
    },
    async deleteLabel(label) {
      await api.deleteLabel(this.board.id, label.id)

      this.labels = this.labels.filter((item) => item.id !== label.id)

      this.board.tasks.forEach(currentTask => {
        currentTask.labels = currentTask.labels.filter((item) => item.label.id !== label.id)
      })

      if (!this.haveLabels) {
        this.labelDialog = false
      }
    },
    collapseTasks() {
      this.collapseTask = true
    },
    updateEditing(value) {
      this.editing = value
    },
    showLabelsEditor() {
      if (!this.haveLabels) {
        this.addLabel()
      }

      this.labelDialog = true
    },
    async addLabel() {
      let newLabelColor = '';

      if (!this.isShowAddLabels) {
        return
      }

      if (!this.haveLabels) {
        newLabelColor = LABEL_COLORS[0]
      } else {
        const currentLabelColors = this.labels.map(label => label.color)

        LABEL_COLORS.every((color) => {
          if (!currentLabelColors.includes(color)) {
            newLabelColor = color

            return false
          }

          return true
        })
      }

      const newLabel = await api.addLabel(this.board.id, {boardId: this.board.id, color: newLabelColor, title: 'label'})
      this.labels.push(newLabel)
    },
    async updateLabel(label) {
      await api.updateLabel(this.board.id, {id: label.id, title: label.title})
      await this.syncTasks()
    },
    toggleCollapseArchived() {
      this.collapseArchived = !this.collapseArchived
    },
    async updateDisplay(display) {
      this.board.display = display;

      if (!isTaskDisplay(display)) {
        this.filteredStatuses = [];
        this.filteredLabels = [];
      }

      await api.updateTitle(this.board.id, {title: this.board.title, display: display});
    },
    async takeOwnership() {
      await boardApi.takeOwnership(this.board.id)
      this.board.ownership = true
      this.board.isOwner = true
    },
    async setPinCode(pinCode) {
      await boardApi.setPinCode(this.board.id, pinCode)
      this.board.hasPinCode = true
    },
    async removePinCode() {
      await boardApi.removePinCode(this.board.id)
      this.board.hasPinCode = false
    },
    async pinCodeSubmit () {
      this.pinCodeLoading = true
      this.invalidPinCode = false;

      const { valid } = await this.$refs.form.validate()

      if (valid) {
        let result = await boardApi.authBoard(window.location.pathname.substring(1), this.pinCode);

        if (result.authorized) {
          this.openPinCodeDialog = false

          await this.syncTasks()
        } else {
          this.invalidPinCode = true;

          await this.$refs.form.validate()
        }
      }

      this.pinCodeLoading = false
    }
  }
};
</script>
<style scoped>
.main-background {
  background: #f0f0f0 !important;
}

.the-add-task {
  background: #f0f0f0;
  outline: none;
  width: 100%;
  font-size: 16px;
  line-height: 20px;
}

.the-footer {
  background: #f0f0f0;
  bottom: 0px;
  z-index: 1004;
  transform: translateY(0%);
  position: fixed;
  height: 40px;
  left: 0px;
  width: calc((100% - 0px) - 0px);
}

.the-footer__content {
  display: flex;
  flex: none;
  font-size: 0.75rem;
  justify-content: center;
  transition: inherit;
  width: 100%;
}

.v-menu > .v-overlay__content {
  flex-direction: row;
}

.the-home {
  top: 0px;
  z-index: 1004;
  position: fixed;
  left: 0px;
}
</style>