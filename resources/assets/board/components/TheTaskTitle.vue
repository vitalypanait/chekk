<template>
    <div class="me-auto" v-show="!isEditable">{{ title }}</div>
    <v-text-field
        variant="underlined"
        v-show="isEditable"
        :value="title"
        @input="$emit('update:title', $event.target.value)"
        @click.stop=""
        @keyup.enter="update"
    ></v-text-field>
</template>

<script>
export default {
    name: 'TheTaskTitle',
    props: ['title', 'editable'],
    emits: ['update:title', 'update:editable', 'update'],
    computed: {
        isEditable() {
            return this.editable;
        }
    },
    methods: {
        update() {
            this.$emit('update', this.title);
            this.$emit('update:editable', false);
        }
    }
};
</script>