<template>
  <div class="mb-5">
    <label class="form-label" :for="name">{{ label }}</label>
    <select
      class="form-select"
      :id="name"
      :name="name"
      @input="updateInput"
      v-model="internalValue"
    >
      <option v-if="fieldNull" value="null">{{ fieldNull }}</option>
      <option v-if="optionLabel" disabled value="">
        {{ optionLabel }}
      </option>
      <option
        v-for="(item) in items"
        :key="item.id"
        :value="item.id"
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
    name: String,
    items: Array,
    fieldsToDisplay: [String, Array],
    fieldNull: String,
    optionLabel: String,
    modelValue: [String, Number],
    autoSelect: [String, Object, Number],
  },
  data() {
    return {
      internalValue: this.modelValue,
    };
  },
  methods: {
    updateInput(event) {
      if (event.target.value === "null") {
        this.internalValue = null;
      } else {
        this.internalValue = event.target.value;
      }
      this.$emit("update:modelValue", this.internalValue);
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
  },
  watch: {
    modelValue(newValue) {
      this.internalValue = newValue;
    },
    autoSelect(newValue) {
      this.internalValue = newValue;
      this.$emit("update:modelValue", newValue.id);
    }
  },
  mounted() {
    if (this.autoSelect) {
      this.internalValue = this.autoSelect.id;
      this.$emit("update:modelValue", this.autoSelect.id);
    } else if (this.modelValue) {
      this.internalValue = this.modelValue;
    }
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
