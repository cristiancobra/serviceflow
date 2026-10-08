<template>
  <SectionCard title="Datas">
    <div>
      <DateEditableInput
        date-only
        name="date_start"
        label="Início:"
        :modelValue="opportunity.date_start"
        @save="$emit('update-field', 'date_start', $event)"
      />
      <DueDateEditableInput
        label="Prazo:"
        :modelValue="opportunity.date_due"
        :changes="opportunity.due_date_changes || []"
        date-only
        @save="$emit('update-fields', $event)"
      />
      <DateEditableInput
        date-only
        name="date_conclusion"
        label="Conclusão:"
        :modelValue="opportunity.date_conclusion"
        @save="$emit('update-field', 'date_conclusion', $event)"
      />
      <DateEditableInput
        date-only
        name="date_canceled"
        label="Cancelado:"
        :modelValue="opportunity.date_canceled"
        @save="$emit('update-field', 'date_canceled', $event)"
      />
    </div>
  </SectionCard>
</template>

<script>
import SectionCard from "@/components/common/SectionCard.vue";
import DateEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import DueDateEditableInput from "../fields/datetime/DueDateEditableInput.vue";

export default {
  name: "OpportunityDatesSection",
  components: {
    SectionCard,
    DateEditableInput,
    DueDateEditableInput,
  },
  props: {
    opportunity: {
      type: Object,
      required: true,
    },
  },
  // update-fields: vários campos de uma vez (prazo + motivo do adiamento)
  emits: ['update-field', 'update-fields'],
};
</script>