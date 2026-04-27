<template>
  <section class="section-container flex-1 mr-10 mb-6 p-10 border border-primary rounded-lg">
    <div class="section-title items-center mb-4">
      <font-awesome-icon
        icon="fas fa-calendar"
        class="text-primary text-xl mr-2"
      />
      <h2 class="text-xl font-semibold">Datas</h2>
    </div>
    <div class="">
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
      
      <div class="mt-6 pt-4 border-t border-gray-300">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-700 flex items-center">
            <font-awesome-icon icon="fas fa-clock" class="text-primary mr-2" />
            Duração Total:
          </span>
          <span class="text-lg font-bold text-primary">
            {{ formatDuration(opportunity.duration_time) }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import DateEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import { formatDuration } from "@/utils/date/dateUtils";

export default {
  name: "OpportunityDatesSection",
  components: {
    DateEditableInput,
  },
  props: {
    opportunity: {
      type: Object,
      required: true,
    },
  },
  methods: {
    formatDuration,
  },
  emits: ['update-field'],
};
</script>