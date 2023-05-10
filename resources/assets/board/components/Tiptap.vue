<template>
    <editor-content :editor="editor" />
</template>

<script>

import { Editor, EditorContent } from '@tiptap/vue-3'
import Link from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'
import StarterKit from '@tiptap/starter-kit'

export default {
    name: 'TipTap',
    components: {EditorContent, Link},
    data() {
        return {
            editor: null,
        }
    },
    props: {
        modelValue: {
            type: String,
            default: '',
        },
    },
    emits: ['update:modelValue', 'update:content'],
    watch: {
        modelValue(value) {
            const isSame = this.editor.getHTML() === value

            if (isSame) {
                return
            }

            this.editor.commands.setContent(value, false)
        },
    },
    mounted() {
        this.editor = new Editor({
            extensions: [
                StarterKit,
                Link.configure({
                    openOnClick: false,
                }),
                Placeholder.configure({
                    placeholder: 'Add a comment...',
                }),
            ],
            editorProps: {
                attributes: {
                    class: 'focus:outline-none',
                },
                handleDOMEvents: {
                    keydown: (view, event) => {
                        if (event.key === 'Enter' && this.editor.getText().length > 0) {
                            this.editor.commands.blur()

                            return false
                        }
                    }
                },
            },
            content: this.modelValue,
            onUpdate: () => {
                this.$emit('update:modelValue', this.editor.getHTML())
            },
            onBlur: () => {
                if (this.editor.getText().length > 0) {
                    this.$emit('update:content', this.editor.getHTML())

                    return false
                }
            },
        })
    },
    beforeUnmount() {
        this.editor.destroy()
    },
};
</script>
<style>
    .ProseMirror:focus {
        outline: none;
    }
    .ProseMirror a {
        color: #68CEF8;
    }
    .ProseMirror p.is-editor-empty:first-child::before {
        content: attr(data-placeholder);
        float: left;
        color: #adb5bd;
        pointer-events: none;
        height: 0;
    }
</style>
