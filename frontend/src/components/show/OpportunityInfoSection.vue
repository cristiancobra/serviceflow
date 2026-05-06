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
        <div class="flex items-center gap-3">
          <company-avatar
            :photo="opportunity.company?.photo"
            :business-name="opportunity.company?.business_name"
            :legal-name="opportunity.company?.legal_name"
            :company-id="opportunity.company?.id"
            size="md"
          />
          <companies-select-editable-field
            label="Empresa"
            name="company_id"
            :modelValue="opportunity.company_id"
            @update:modelValue="$emit('update-field', 'company_id', $event)"
            class="flex-1"
          />
        </div>
        <div class="flex items-center gap-3">
          <lead-avatar
            :photo="opportunity.lead?.photo"
            :name="opportunity.lead?.name"
            :lead-id="opportunity.lead?.id"
            size="md"
          />
          <leads-select-editable-field
            label="Cliente"
            name="lead_id"
            :modelValue="opportunity.lead_id"
            @update:modelValue="$emit('update-field', 'lead_id', $event)"
            class="flex-1"
          />
        </div>
        <div class="flex items-center gap-3">
          <user-avatar
            :photo="opportunity.user?.photo"
            :name="opportunity.user?.name"
            :user-id="opportunity.user?.id"
            size="md"
          />
          <users-select-editable-field
            label="Responsável"
            name="user_id"
            :modelValue="opportunity.user_id"
            @update:modelValue="$emit('update-field', 'user_id', $event)"
            class="flex-1"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import CompaniesSelectEditableField from "../fields/selects/CompaniesSelectEditableField.vue";
import CompanyAvatar from "../common/CompanyAvatar.vue";
import LeadAvatar from "../common/LeadAvatar.vue";
import UserAvatar from "../common/UserAvatar.vue";
import LeadsSelectEditableField from "../fields/selects/LeadsSelectEditableField.vue";
import UsersSelectEditableField from "../fields/selects/UsersSelectEditableField.vue";

export default {
  name: "OpportunityInfoSection",
  components: {
    CompaniesSelectEditableField,
    CompanyAvatar,
    LeadAvatar,
    UserAvatar,
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