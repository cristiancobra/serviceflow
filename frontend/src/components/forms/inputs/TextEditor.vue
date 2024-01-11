<template>
  <div>
    <label class="show-label mb-3" :for="name">{{ label }}:</label>
    <div class="tiptap">
      <div
        v-if="!editing && modelValue === null"
        @click="startEditing"
        class="editable-content w-100"
      >
        Clique para adicionar uma descrição...
      </div>

      <div
        v-else-if="!editing"
        @click="startEditing"
        class="editable-content w-100"
        v-html="modelValue"
      ></div>

      <div v-else
      class="editable-content"
       @blur="cancelEditing"
        @keydown.esc="cancelEditing"
        >
        <TiptapButtons v-if="editor" :editor="editor" />

        <EditorContent :editor="editor" @input="updateContent" />
      </div>
    </div>
  </div>
</template>

<script>
import { Editor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Placeholder from "@tiptap/extension-placeholder";
import TiptapButtons from "../../buttons/TiptapButtons.vue";

export default {
  components: {
    EditorContent,
    TiptapButtons,
  },
  props: {
    label: String,
    name: String,
    modelValue: String,
  },
  data() {
    return {
      editor: null,
      editing: false,
      content: this.modelValue,
    };
  },
  methods: {
    cancelEditing() {
      this.editing = false;
    },
    startEditing() {
      this.editing = true;
      // this.editedValue = this.modelValue;
      // this.$nextTick(() => {
      //   this.$refs.editableInput.focus();
      // });
    },
    updateContent(content) {
      this.content = content;
    },
  },
  mounted() {
    this.editor = new Editor({
      content: this.modelValue,
      extensions: [
        StarterKit,
        Placeholder.configure({
          emptyEditorClass: "editor-placeholder",
          emptyNodeClass: "node-placeholder",
          placeholder: "descrição detalhada da tarefa",
        }),
      ],
      onBlur: () => {
        let sanitizedContent = this.editor.getHTML();

        if (sanitizedContent === "<p></p>") {
          sanitizedContent = "";
        }

        this.$emit("save", sanitizedContent);
      },
      onKeydown: (event) => {
        if (event.key === "Escape") {
          this.cancelEditing();
        }
      },
    });
  },
  beforeUnmount() {
    this.editor.destroy();
  },
  watch: {
    modelValue(value) {
      // HTML
      const isSame = this.editor.getHTML() === value;

      // JSON
      // const isSame = JSON.stringify(this.editor.getJSON()) === JSON.stringify(value)

      if (isSame) {
        return;
      }

      this.editor.commands.setContent(value, false);
    },
  },
};
</script>

<style scoped>
.editable-content {
  border-style: solid;
  border-width: 1px;
  border-color: lightgray;
  border-radius: 10px;
  text-align: left;
  padding: 1rem;
}

.show-label {
  display: block;
  text-align: left;
  font-size: 20px;
  font-weight: 800;
  margin-bottom: 10px;
}
.tiptap p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
  text-align: left;
}
</style>