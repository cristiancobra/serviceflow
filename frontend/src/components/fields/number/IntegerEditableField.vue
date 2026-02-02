<template>
  <div>
    <label v-if="label" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing">
      <p class="number-editable">
          <font-awesome-icon icon="edit" class="edit-icon" />
          {{ displayValue }}
        </p>
    </div>
    <div v-else>
      <input 
        class="text-end" 
        type="number" 
        v-model.number="inputValue" 
        :placeholder="placeholder" 
        @keydown.esc="cancelEditing" 
        @blur="emitSave" 
        @keydown.enter.prevent="emitSave" 
        ref="inputField"
      />
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editing: false,
      inputValue: null,
      savedValue: null,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  computed: {
    displayValue() {
      return this.modelValue ? parseInt(this.modelValue, 10) : '';
    },
  },
  methods: {
    startEditing() {
      this.savedValue = this.modelValue ? parseInt(this.modelValue, 10) : '';
      this.inputValue = this.savedValue;
      this.editing = true;
      this.$nextTick(() => {
        if (this.$refs.inputField) {
          this.$refs.inputField.focus();
        }
      });
    },
    emitSave() {
      if (this.inputValue !== null && this.inputValue !== '') {
        this.$emit("save", parseInt(this.inputValue, 10));
      }
      this.editing = false;
    },
    cancelEditing() {
      this.editing = false;
      this.inputValue = this.savedValue;
    },
  },
  watch: {
    modelValue(newValue) {
      // Sempre mantém sincronizado com o valor do pai
      if (!this.editing) {
        this.savedValue = newValue ? parseInt(newValue, 10) : '';
      }
    },
  },
};
</script>

<style scoped>
.edit-icon {
  display: none;
  margin-left: 5px;
  color: var(--green);
}

.number-editable:hover .edit-icon {
  display: inline;
}

.number-editable {
  cursor: pointer;
  color: var(--primary);
}
</style>