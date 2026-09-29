<template>
  <div class="mb-4">
    <label class="block text-sm font-semibold text-base-content mb-2" :for="name">{{ label }}</label>
    <select 
      class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-base-content/20 disabled:bg-base-200 disabled:text-base-content/60 disabled:cursor-not-allowed" 
      :id="name" 
      :name="name" 
      :disabled="disabled"
      @input="updateInput" 
      v-model="localValue"
    >
      <option v-if="fieldNull" :value="null" class="text-base-content/70">{{ fieldNull }}</option>
      <option v-if="placeholder" disabled value="" class="text-base-content/50">
        {{ placeholder }}
      </option>
      <option v-for="(item) in items" :key="item.id" :value="item.id" class="text-base-content">
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
    disabled: {
      type: Boolean,
      default: false,
    },
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
