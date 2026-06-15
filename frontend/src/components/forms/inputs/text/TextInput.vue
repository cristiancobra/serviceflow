<template>
  <div class="label-input-container">
    <label v-if="label" class="form-label text-gray-700 font-semibold" :for="name">{{ label }}</label>
    <input class="text-input text-gray-800 bg-white" type="text" :name="name" :value="modelValue" :placeholder="placeholder"
    @input="$emit('update:modelValue', $event.target.value)"
   />
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
};
</script>

<style scoped>
.text-input {
  width: 100%;
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid var(--gray);
  border-radius: 4px;
  margin-top: 0.5rem;
}

</style>