<template>
  <div>
    <label v-if="label" class="block text-sm font-semibold text-base-content mb-2" :for="name">{{ label }}</label>
    <textarea
      class="textarea w-full"
      :id="name"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
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
    rows: Number,
  },
};
</script>