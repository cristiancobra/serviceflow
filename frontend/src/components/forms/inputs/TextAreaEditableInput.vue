<template>
  <div class="mb-5 main-container">
    <label class="show-label" :for="name">{{ label }}:</label>

    <textarea
      v-if="!editing"
      class="editable-content w-100"
      :value="modelValue"
      @click="startEditing"
      ref="editableContent"
      :contenteditable="true"
      :placeholder="placeholder"
    >
    </textarea>

    <div v-else class="form-control d-inline">
      <textarea
        class="form-control w-100 me-3"
        :value="modelValue"
        :id="name"
        :name="name"
        ref="editableInput"
        @blur="saveChanges"
        @input="updateInput"
        @keydown.esc="cancelEditing"
        :contenteditable="true"
      >
      </textarea>
      <button @click="cancelEditing">Cancelar</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      editedValue: null,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    startEditing() {
      this.editing = true;
      this.editedValue = this.modelValue;
      this.$nextTick(() => {
        this.$refs.editableInput.focus();
      });
    },
    saveChanges() {
      console.log(this.editedValue);
      // Salvar as alterações apenas se o valor editado for diferente do original
      if (this.editedValue !== this.modelValue) {
        this.$emit("save", this.editedValue);
      }
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
    },
    updateInput(event) {
      this.editedValue = event.target.value;
    },
  },
};
</script>

<style scoped>
.main-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.show-label {
  text-align: left;
  font-weight: 800;
  margin-bottom: 10px;
}
.editable-content {
  border: none;
  padding: 5px;
  cursor: pointer;
  background: transparent;
}
.editable-content[contenteditable="true"]:empty:before {
  content: attr(placeholder);
  color: #888;
}
</style>