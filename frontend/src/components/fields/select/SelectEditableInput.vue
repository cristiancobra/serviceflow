<template>
  <div class="flex items-center">
    <label v-if="label" class="form-label me-3" :for="name">{{ label }}</label>
    <div v-if="!editing" @click="startEditing" class="cursor-pointer hover:bg-gray-50 px-3 py-1 rounded">
      <span :class="classText || 'text-gray-700'">
        {{ displayValue }}
      </span>
      <font-awesome-icon icon="fa-solid fa-pen" class="ms-2 text-gray-400 text-xs" />
    </div>
    <select
      v-else
      v-model="localValue"
      @blur="emitSave"
      @change="emitSave"
      class="form-control px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
      :id="name"
      :name="name"
      ref="selectInput"
    >
      <option v-if="allowEmpty" value="">{{ emptyLabel || 'Selecione...' }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'SelectEditableInput',
  data() {
    return {
      editing: false,
      localValue: this.modelValue,
    };
  },
  props: {
    classText: String,
    label: String,
    name: String,
    modelValue: [String, Number],
    options: {
      type: Array,
      required: true,
      // Format: [{ value: 'client', label: 'Cliente' }, ...]
    },
    allowEmpty: {
      type: Boolean,
      default: false,
    },
    emptyLabel: String,
  },
  computed: {
    displayValue() {
      const option = this.options.find(opt => opt.value === this.localValue);
      return option ? option.label : (this.localValue || '---');
    },
  },
  methods: {
    startEditing() {
      this.editing = true;
      this.$nextTick(() => {
        this.$refs.selectInput?.focus();
      });
    },
    emitSave() {
      if (this.modelValue !== this.localValue) {
        this.$emit("save", this.localValue);
      }
      this.editing = false;
    },
    cancelEditing(event) {
      if (event.key === 'Escape') {
        this.localValue = this.modelValue;
        this.editing = false;
      }
    },
  },
  mounted() {
    document.addEventListener("keydown", this.cancelEditing);
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.cancelEditing);
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
};
</script>

<style scoped>
.form-label {
  font-weight: 900;
  color: var(--primary);
}
</style>
