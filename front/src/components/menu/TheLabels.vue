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
      <v-btn value="labels" v-bind="props" class="me-auto text-body-2" variant="text" rounded="0">
        Labels
        <v-icon v-for="color in this.filteredLabelColors"
                icon="mdi-circle-medium"
                :color="color"
                class="mr-n2"
        ></v-icon>
      </v-btn>
    </template>

    <v-list class="rounded-lg" density="compact">
      <v-list-item
          density="compact"
          v-for="(label, i) in labels"
          :key="i"
          :value="label.title"
          @click="update(label)"
      >
        <v-chip
            label
            class="font-weight-black mx-1"
            size="x-small"
            :color="label.color"
            :value="label.id"
            :text-color="getColor(label)"
            :variant="getVariant(label)"
        >{{ label.title }} {{ getCount(label) }}
        </v-chip>
      </v-list-item>
      <v-list-item v-if="!readOnly" density="compact" value="Add" @click="mutate()"
                   v-show="this.labels.length === 0">Add label
      </v-list-item>
      <v-list-item v-if="!readOnly" density="compact" value="Edit" @click="mutate()"
                   v-show="this.labels.length > 0">Edit
      </v-list-item>
      <v-list-item density="compact" value="Reset" @click="reset()" v-show="this.labels.length > 0">Reset
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>

import {mobile} from '../../mixins';

export default {
  name: 'TheLabels',
  props: ['modelValue', 'labels', 'tasks', 'readOnly'],
  emits: ['update:modelValue', 'mutate'],
  mixins: [mobile],
  computed: {
    filteredLabelColors() {
      let colors = []

      this.labels.forEach(label => {
        if (this.modelValue.includes(label.id)) {
          colors.push(label.color)
        }
      })

      return colors
    },
    labelsCount() {
      let labelsCount = [];

      this.labels.forEach(label => {
        labelsCount[label.id] = 0;
      })

      this.tasks.forEach(currentTask => {
        currentTask.labels.forEach(label => {
          labelsCount[label.label.id] += 1;
        })

      })

      return labelsCount
    },
  },
  methods: {
    getColor(label) {
      if (this.modelValue.includes(label.id)) {
        return ''
      }

      return 'white'
    },
    getVariant(label) {
      if (this.modelValue.includes(label.id)) {
        return 'tonal'
      }

      return 'outlined'
    },
    getCount(label) {
      return this.labelsCount[label.id]
    },
    reset() {
      this.$emit('update:modelValue', [])
    },
    update(label) {
      let labels = this.modelValue;

      if (labels.includes(label.id)) {
        const index = labels.indexOf(label.id);
        if (index > -1) {
          labels.splice(index, 1);
        }
      } else {
        labels.push(label.id)
      }

      this.$emit('update:modelValue', labels)
    },
    mutate() {
      this.$emit('mutate')
    }
  }
};
</script>