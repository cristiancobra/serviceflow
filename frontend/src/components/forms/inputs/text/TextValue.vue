<template>
  <div v-if="localValue" class="">
    {{ localValue }}
  </div>
  <div v-else>
    não informado
  </div>
</template>
    
<script>
// import { ref, watch } from 'vue';

export default {
  data() {
    return {
      // modelValue: String,
    };
  },
  props: {
    name: String,
    modelValue: String,
    placeholder: String,
  },
  computed: {
    localValue: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    }
  }
};
</script>

    
  <style scoped>
label {
  text-align: right;
}
</style>
  