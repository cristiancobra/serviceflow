<template>
  <div v-if="modelValue" class="">
    {{ modelValue }}
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

    
  <style scoped>
label {
  text-align: right;
}
</style>
  