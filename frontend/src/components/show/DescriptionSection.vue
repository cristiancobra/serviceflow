<template>
  <div class="mt-8 mb-20 px-8">
    <section-header title="Descrição" icon="fas fa-coins" />
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
import SectionHeader from "@/components/layout/SectionHeader.vue";
export default {
  components: { SectionHeader, TextAreaEditableInput },
  props: {
    description: {
      type: String,
      required: false,
    },
  },
  emits: ['update:description']
};
</script>