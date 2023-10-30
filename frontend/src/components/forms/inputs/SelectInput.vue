<template>
  <div class="mb-4">
    <label class="form-label" :for="name">{{ label }}</label>

    <select
      class="form-select"
      :id="name"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      @input="updateInput"
    >
      <option disabled value="">Selecione um contato</option>
      <option v-for="item in items" :key="item.id" :value="item.id">
        {{ displayItemText(item) }}
      </option>
    </select>
  </div>
</template>
    
  <script>
export default {
  props: {
    label: String,
    type: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
    size: String,
    items: Array,
    fieldToDisplay: [String, Array],
  },
  computed: {
    labelClass() {
      if (this.size === "full") {
        return `col-form-label col-sm-2`;
      } else {
        return `col-form-label col-sm-4`; // Valor padrão se size não for 'full' ou 'medium'
      }
    },
    divClass() {
      if (this.size === "full") {
        return `col-sm-9`;
      } else {
        return `col-sm-6`; // Valor padrão se size não for 'full' ou 'medium'
      }
    },
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
    displayItemText(item) {
      if (Array.isArray(this.fieldToDisplay)) {
        return this.fieldToDisplay.map((field) => item[field]).join(" - ");
      } else {
        return item[this.fieldToDisplay];
      }
    },
  },
};
</script>
    
  <style scoped>
label {
  text-align: right;
}
.input-field {
  margin-bottom: 1rem;
}
</style>
  