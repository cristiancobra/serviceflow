<template>
  <div class="section-container">
    <div class="section-title">
      <font-awesome-icon icon="fas fa-coins" class="icon" />
      <h2>Descrição</h2>
    </div>
    <div v-if="description" class="table-row">
      <text-area-editable-input
        label="Descrição"
        name="description"
        :model-value="description"
        placeholder="Adicione uma descrição para esta proposta..."
        @save="$emit('update:description', $event)"
      />
    </div>
    <div v-else class="table-row">
      <text-area-editable-input
        label="Descrição"
        name="description"
        :model-value="''"
        placeholder="Adicione uma descrição para esta proposta..."
        @save="$emit('update:description', $event)"
      />
    </div>
  </div>
</template>

<script>
import TextAreaEditableInput from '../forms/inputs/textarea/TextAreaEditableInput.vue';
export default {
  components: { TextAreaEditableInput },
  props: {
    description: {
      type: String,
      required: false,
    },
  },
  emits: ['update:description']
};
</script>