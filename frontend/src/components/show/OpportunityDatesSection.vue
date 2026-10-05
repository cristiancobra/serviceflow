<template>
  <SectionCard title="Datas">
    <div>
      <DateEditableInput
        name="date_start"
        label="Início:"
        :modelValue="opportunity.date_start"
        @save="$emit('update-field', 'date_start', $event)"
      />
      <DateEditableInput
        name="date_due"
        label="Prazo:"
        :modelValue="opportunity.date_due"
        @save="$emit('update-field', 'date_due', $event)"
      />
      <DateEditableInput
        name="date_conclusion"
        label="Conclusão:"
        :modelValue="opportunity.date_conclusion"
        @save="$emit('update-field', 'date_conclusion', $event)"
      />
      <DateEditableInput
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

export default {
  name: "OpportunityDatesSection",
  components: {
    SectionCard,
    DateEditableInput,
  },
  props: {
    opportunity: {
      type: Object,
      required: true,
    },
  },
  emits: ['update-field'],
};
</script>