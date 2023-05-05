<template>
    <v-hover v-slot="{ isHovering, props }">
        <v-icon :icon="selectedIcon" :color="selectedColor" @click.stop="changeStatus" style="cursor: pointer"></v-icon>
    </v-hover>
</template>

<script>
const STATUS_CREATED = 'created';
const STATUS_COMPLETED = 'completed';
const STATUS_PAUSED = 'paused';
const STATUS_PROCESSING = 'processing';

export default {
    name: 'TheStatus',
    props: ['modelValue'],
    emits: ['update:modelValue'],
    computed: {
        selectedIcon() {
            switch (this.modelValue) {
                case STATUS_CREATED:
                    return 'mdi-circle-outline'
                case STATUS_COMPLETED:
                    return 'mdi-checkbox-marked-circle';
                case STATUS_PAUSED:
                    return 'mdi-stop-circle'
                case STATUS_PROCESSING:
                    return 'mdi-clock-time-four'
            }
        },
        selectedColor() {
            switch (this.modelValue) {
                case STATUS_COMPLETED:
                    return 'green';
                case STATUS_CREATED:
                    return 'grey'
                case STATUS_PAUSED:
                    return 'red'
                case STATUS_PROCESSING:
                    return 'blue'
            }
        }
    },
    methods: {
        changeStatus() {
            let value = STATUS_PAUSED;

            if (this.modelValue === STATUS_CREATED) {
                value = STATUS_PROCESSING
            } else if (this.modelValue === STATUS_PAUSED) {
                value = STATUS_PROCESSING
            } else if (this.modelValue === STATUS_PROCESSING) {
                value = STATUS_COMPLETED
            }

            this.$emit('update:modelValue', value);
        }
    }
};
</script>