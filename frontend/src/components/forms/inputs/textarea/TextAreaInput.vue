<template>
   <div class="label-input-container">
    <label class="form-label" :for="name">{{ label }}</label>
    <textarea
      class="form-control"
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