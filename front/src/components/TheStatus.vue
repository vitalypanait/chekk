<template>
    <v-icon :icon="selectedIcon" :color="selectedColor" @click.stop="changeStatus" style="cursor: pointer"></v-icon>
</template>

<script>
import { getStatusColor, getStatusIcon, getNextStatus } from '../utils';

export default {
    name: 'TheStatus',
    props: ['modelValue', 'readOnly'],
    emits: ['update:modelValue'],
    computed: {
        selectedIcon() {
            return getStatusIcon(this.modelValue);
        },
        selectedColor() {
            return getStatusColor(this.modelValue);
        }
    },
    methods: {
        changeStatus() {
          if (this.readOnly) {
            return;
          }

          this.$emit('update:modelValue', getNextStatus(this.modelValue));
        }
    }
};
</script>