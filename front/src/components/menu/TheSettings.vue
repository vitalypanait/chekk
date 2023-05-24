<template>
  <v-menu
      open-delay="50"
      location="top"
      class="rounded-lg"
      :open-on-hover="!isMobile()"
      :open-on-click="isMobile()"
      :close-on-content-click="false"
      :transition="false"
  >
    <template v-slot:activator="{ props }">
      <v-btn value="settings" v-bind="props" variant="text" rounded="0" class="text-body-2">Settings</v-btn>
    </template>

    <v-list class="rounded-lg" density="compact">
      <v-list-item density="compact" class="text-grey">Show as</v-list-item>
      <v-list-item density="compact">
        <div class="d-flex">
          <v-sheet
              border
              @click="updateDisplayToTask()"
              :class="isTaskDisplay(display) ? 'bg-grey-lighten-3' : ''"
              class="px-1 py-1 rounded-s-lg"
              style="cursor: pointer"
          >
            <v-icon icon="mdi-circle-outline" size="small"></v-icon>
          </v-sheet>
          <v-sheet
              border
              @click="updateDisplayToList()"
              :class="isListDisplay(display) ? 'bg-grey-lighten-3' : ''"
              class="px-1 py-1"
              style="cursor: pointer"
          >
            <v-icon icon="mdi-format-list-numbered" size="small"></v-icon>
          </v-sheet>
          <v-sheet
              border
              @click="updateDisplayToContent()"
              :class="isContentDisplay(display) ? 'bg-grey-lighten-3' : ''"
              class="px-1 py-1 rounded-e-lg"
              style="cursor: pointer"
          >
            <v-icon icon="mdi-text-long" size="small"></v-icon>
          </v-sheet>
        </div>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>

import {
  DISPLAY_TASK,
  DISPLAY_LIST,
  DISPLAY_CONTENT,
  isListDisplay,
  isTaskDisplay,
  isContentDisplay
} from '../../models';
import {mobile} from '../../mixins';

export default {
  name: 'TheSettings',
  props: ['display'],
  emits: ['update:display'],
  mixins: [mobile],
  methods: {
    isTaskDisplay,
    isListDisplay,
    isContentDisplay,
    updateDisplayToTask() {
      this.$emit('update:display', DISPLAY_TASK);
    },
    updateDisplayToList() {
      this.$emit('update:display', DISPLAY_LIST);
    },
    updateDisplayToContent() {
      this.$emit('update:display', DISPLAY_CONTENT);
    }
  }
};
</script>