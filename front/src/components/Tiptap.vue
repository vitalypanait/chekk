<template>
    <editor-content :editor="editor" />
</template>

<script>

import { Editor, EditorContent } from '@tiptap/vue-3'
import Link from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'
import BulletList from "@tiptap/extension-bullet-list";
import Paragraph from "@tiptap/extension-paragraph";
import Text from "@tiptap/extension-text";
import Document from "@tiptap/extension-document";
import ListItem from "@tiptap/extension-list-item";
import HardBreak from "@tiptap/extension-hard-break";

export default {
    name: 'TipTap',
    components: {EditorContent, Link, Paragraph, Text, Document, ListItem, HardBreak},
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
                HardBreak,
                ListItem,
                Document,
                Paragraph,
                Text,
                Link.configure({
                    openOnClick: false,
                }),
                Placeholder.configure({
                    placeholder: 'Add a comment...',
                }),
                BulletList.extend({
                    addKeyboardShortcuts() {
                        return {
                            'Enter': () => this.editor.commands.blur(),
                        }
                    },
                })
            ],
            editorProps: {
                attributes: {
                    class: 'focus:outline-none',
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
