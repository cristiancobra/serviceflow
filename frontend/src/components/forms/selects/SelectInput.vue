<template>
  <div class="mb-4">
    <label class="block text-sm font-semibold text-gray-900 mb-2" :for="name">{{ label }}</label>
    <select 
      class="w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 ease-in-out hover:border-gray-400" 
      :id="name" 
      :name="name" 
      @input="updateInput" 
      v-model="localValue"
    >
      <option v-if="fieldNull" :value="null" class="text-gray-600">{{ fieldNull }}</option>
      <option v-if="placeholder" disabled value="" class="text-gray-400">
        {{ placeholder }}
      </option>
      <option v-for="(item) in items" :key="item.id" :value="item.id" class="text-gray-900">
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
    modelValue: [String, Number],
    placeholder: String,
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
/* Removido CSS customizado - usando apenas Tailwind CSS */
</style>
