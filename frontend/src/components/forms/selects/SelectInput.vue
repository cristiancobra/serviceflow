<template>
  <div class="label-input-container">
    <label class="form-label" :for="name">{{ label }}</label>
    <select class="form-select" :id="name" :name="name" @input="updateInput" v-model="localValue">
      <option v-if="fieldNull" :value=null>{{ fieldNull }}</option>
      <option v-if="optionLabel" disabled value="">
        {{ optionLabel }}
      </option>
      <option v-for="(item) in items" :key="item.id" :value="item.id">
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
  },
  data() {
    return {
      localValue: this.modelValue,
    };
  },
  methods: {
    updateInput(event) {
      if (event.target.value === "null" || event.target.value === this.fieldNull) {
        this.localValue = null;
      } else {
        this.localValue = event.target.value;
      }
      this.$emit("update:modelValue", this.localValue);
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
      this.localValue = newValue;
    },
  },

  mounted() {
    if (this.modelValue !== undefined) {
      this.localValue = this.modelValue;
    } else if (this.fieldNull) {
      this.localValue = null;
    } else {
      this.localValue = '';
    }
  },

  };
</script>

<style scoped>
.input-field {
  margin-bottom: 1rem;
}
</style>
