<template>
  <section
    id="info"
    class="section-container flex-1 ml-10 mb-6  p-10 border border-primary rounded-lg"
  >
    <div class="section-title flex items-center mb-4">
      <font-awesome-icon
        icon="fas fa-file-invoice"
        class="text-primary text-xl mr-2"
      />
      <h2 class="text-xl font-semibold">Informações</h2>
    </div>
    <div class="grid grid-cols-3 gap-4">
      <div class="col-span-2 space-y-4">
        <companies-select-editable-field
          label="Empresa"
          name="company_id"
          :modelValue="opportunity.company_id"
          @update:modelValue="$emit('update-field', 'company_id', $event)"
        />
        <leads-select-editable-field
          label="Cliente"
          name="lead_id"
          :modelValue="opportunity.lead_id"
          @update:modelValue="$emit('update-field', 'lead_id', $event)"
        />
        <users-select-editable-field
          label="Responsável"
          name="user_id"
          :modelValue="opportunity.user_id"
          @update:modelValue="$emit('update-field', 'user_id', $event)"
        />
      </div>
    </div>
  </section>
</template>

<script>
import CompaniesSelectEditableField from "../fields/selects/CompaniesSelectEditableField.vue";
import LeadsSelectEditableField from "../fields/selects/LeadsSelectEditableField.vue";
import UsersSelectEditableField from "../fields/selects/UsersSelectEditableField.vue";

export default {
  name: "OpportunityInfoSection",
  components: {
    CompaniesSelectEditableField,
    LeadsSelectEditableField,
    UsersSelectEditableField,
  },
  props: {
    opportunity: {
      type: Object,
      required: true,
    },
  },
  emits: ["update-field"],
};
</script>