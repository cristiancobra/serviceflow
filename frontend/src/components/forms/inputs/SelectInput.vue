<template>
  <div class="mb-5">
    <label class="form-label" :for="name">{{ label }}</label>
    <select class="form-select" :id="name" :name="name" @input="updateInput">
      <option v-if="fieldNull" selected value="null">{{ fieldNull }}</option>
      <option v-if="optionLabel" disabled selected value="">
        {{ optionLabel }}
      </option>
      <option
        v-for="(item, index) in items"
        :key="item.id"
        :value="item.id"
        :selected="shouldSelectFirstItem(index, item)"
      >
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
    placeholder: String,
    items: Array,
    fieldsToDisplay: [String, Array],
    fieldNull: String,
    optionLabel: String,
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
    displayItemText(item) {
      if (Array.isArray(this.fieldsToDisplay)) {
        const displayedValues = this.fieldsToDisplay.map(
          (field) => item[field]
        );

        // Filter out null values
        const nonNullValues = displayedValues.filter(
          (value) => value !== null && value !== undefined
        );

        // Join non-null values with ' - ' separator
        return nonNullValues.join(" - ");
      } else {
        return item[this.fieldsToDisplay];
      }
    },
    shouldSelectFirstItem(index, item) {
      if (index === 0 && !this.optionLabel && !this.fieldNull) {
        this.$emit("update:modelValue", item.id);
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
  