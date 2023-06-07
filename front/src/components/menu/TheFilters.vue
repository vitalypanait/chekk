<template>
  <v-menu open-delay="50" location="top" class="rounded-xl" :open-on-hover="!isMobile()" :open-on-click="isMobile()" :close-on-content-click="false" :transition="false">
    <template v-slot:activator="{ props }">
      <v-btn value="statuses" v-bind="props" variant="text" rounded="0" class="text-body-2">
        <span>Statuses</span>
        <v-icon v-for="status in modelValue"
                icon="mdi-circle-medium"
                :color="getChosenColor(status)"
                class="mr-n2"
        ></v-icon>
      </v-btn>
    </template>

    <v-list class="rounded-lg" density="compact">
      <v-list-item
          density="compact"
          v-for="(status, i) in allStatuses"
          :key="i"
          :value="status.value"
          @click="update(status.value)"
      >
        <v-sheet class="d-flex rounded-lg py-1 px-2 mr-2" :class="getBackground(status)">
          <v-icon :color="getColor(status)" :icon="status.icon" class="mr-1"></v-icon>
          <span>{{ getCount(status.value) }}</span>
        </v-sheet>
      </v-list-item>
      <v-list-item density="compact" value="Reset" @click="reset()">Reset</v-list-item>
    </v-list>
  </v-menu>
</template>

<script>

import {mobile} from '../../mixins';
import {getStatuses, getStatusColor} from '../../utils';

export default {
  name: 'TheFilters',
  props: ['modelValue', 'tasks'],
  emits: ['update:modelValue'],
  mixins: [mobile],
  computed: {
    allStatuses() {
      return getStatuses();
    },
    statusesCount() {
      let statusesCount = [];

      this.allStatuses.forEach(status => {
        statusesCount[status.value] = 0;
      })

      this.tasks.forEach(currentTask => {
        statusesCount[currentTask.status] += 1;
      })

      return statusesCount
    },
  },
  methods: {
    getChosenColor(status) {
      return getStatusColor(status);
    },
    getBackground(status) {
      if (this.modelValue.includes(status.value)) {
        return 'bg-' + status.color
      }

      return ''
    },
    getColor(status) {
      if (this.modelValue.includes(status.value)) {
        return ''
      }

      return status.color
    },
    getCount(status) {
      return this.statusesCount[status]
    },
    reset() {
      this.$emit('update:modelValue', [])
    },
    update(status) {
      let statuses = this.modelValue;

      if (statuses.includes(status)) {
        const index = statuses.indexOf(status);
        if (index > -1) {
          statuses.splice(index, 1);
        }
      } else {
        statuses.push(status)
      }

      this.$emit('update:modelValue', statuses)
    },
  }
};
</script>