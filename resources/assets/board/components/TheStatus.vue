<template>
    <v-icon class="ml-3" :icon="selectedIcon" :color="selectedColor" @click.stop="changeStatus" style="cursor: pointer"></v-icon>
</template>

<script>
import { getStatusColor, getStatusIcon, getNextStatus } from '../utils';

export default {
    name: 'TheStatus',
    props: ['modelValue'],
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
            this.$emit('update:modelValue', getNextStatus(this.modelValue));
        }
    }
};
</script>