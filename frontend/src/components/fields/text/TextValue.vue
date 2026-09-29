<template>
  <div v-if="modelValue" class="text-base-content">
    {{ modelValue }}
  </div>
  <div v-else class="text-base-content">
    não informado
  </div>
</template>
    
<script>
// import { ref, watch } from 'vue';

export default {
  data() {
    return {
      localValue: null,
    };
  },
  props: {
    name: String,
    modelValue: String,
    placeholder: String,
  },
mounted() {
  this.localValue = this.modelValue;
}
};
</script>