<template>
    <div v-show="!isEditable" class="me-auto ml-5 pr-3">{{ title }}</div>
    <input placeholder="Add a task"
           class="the-task-title ml-5"
           v-show="isEditable"
           :value="title"
           @click.stop="trySomething()"
           @input="$emit('update:title', $event.target.value)"
           @keyup.enter="update"
           @blur="update"
           ref="taskTitle"
    />
</template>

<script>
export default {
    name: 'TheTaskTitle',
    props: ['title', 'editable'],
    emits: ['update:title', 'update:editable', 'update'],
    computed: {
        isEditable() {
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
.the-task-title {
    outline: none;
    width: 100%;
}
</style>