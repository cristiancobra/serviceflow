<template>
  <div>
    <p v-if="modelValue" class="mb-1">{{ internalValue }}</p>
    <p v-else class="not-informed">Não informado</p>
  </div>
</template>

    
<script>
import { ref, watch } from 'vue';

export default {
  props: {
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  setup(props) {
    // Cria uma referência reativa para o modelValue
    const internalValue = ref(props.modelValue);

    // Assista a alterações em props.modelValue e atualize internalValue
    watch(() => {
      internalValue.value = props.modelValue;
    });

    // Retorna o contexto de renderização
    return {
      internalValue,
    };
  },
};
</script>

    
  <style scoped>
label {
  text-align: right;
}
</style>
  