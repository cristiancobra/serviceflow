<template>
  <div class="w-full mb-5 flex flex-col items-start">
    <label class="text-left font-bold mb-2" :for="name">{{ label }}:</label>

    <div
      v-if="!editing"
      class="w-full border-none p-1 cursor-pointer bg-transparent min-h-[60px] whitespace-pre-wrap"
      @click="startEditing"
      ref="editableContent"
    >
      {{ modelValue || placeholder }}
    </div>

    <div v-else class="w-full">
      <textarea
        class="w-full border border-gray-300 rounded-md p-3 mr-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none overflow-hidden"
        :value="modelValue"
        :id="name"
        :name="name"
        ref="editableInput"
        @blur="handleBlur"
        @input="updateInput"
        @keydown.esc="cancelEditing"
        :placeholder="placeholder"
        rows="3"
      ></textarea>
      <div class="flex gap-2 mt-2">
        <button 
          class="bg-primary-500 hover:bg-primary-600 text-white font-medium py-1 px-3 rounded text-sm mr-2" 
          @click="saveChanges"
        >
          Salvar
        </button>
        <button 
          class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-1 px-3 rounded text-sm" 
          @click="cancelEditing"
        >
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      editedValue: null,
      blurTimeout: null,
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
        this.adjustTextareaHeight();
      });
    },
    handleBlur() {
      // Adiciona um pequeno delay para permitir que o clique nos botões funcione
      this.blurTimeout = setTimeout(() => {
        if (this.editing) {
          this.saveChanges();
        }
      }, 150);
    },
    saveChanges() {
      if (this.blurTimeout) {
        clearTimeout(this.blurTimeout);
      }
      
      console.log(this.editedValue);
      // Salvar as alterações apenas se o valor editado for diferente do original
      if (this.editedValue !== this.modelValue) {
        this.$emit("save", this.editedValue);
      }
      this.editing = false;
    },
    cancelEditing() {
      if (this.blurTimeout) {
        clearTimeout(this.blurTimeout);
      }
      
      this.editing = false;
      this.editedValue = null;
    },
    updateInput(event) {
      this.editedValue = event.target.value;
      this.adjustTextareaHeight();
    },
    adjustTextareaHeight() {
      const textarea = this.$refs.editableInput;
      if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.max(textarea.scrollHeight, 80) + 'px';
      }
    },
  },
  beforeUnmount() {
    if (this.blurTimeout) {
      clearTimeout(this.blurTimeout);
    }
  },
};
</script>

<style scoped>
/* Remove the old placeholder style since we're using a div now */
</style>