<template>
  <SectionCard id="info" title="Informações">
    <div class="space-y-1">
      <div class="flex items-center gap-3">
        <div :class="avatarRingClass(opportunity.company?.id)">
          <company-avatar
            :photo="opportunity.company?.photo"
            :business-name="opportunity.company?.business_name"
            :legal-name="opportunity.company?.legal_name"
            :company-id="opportunity.company?.id"
            size="md"
          />
        </div>
        <companies-select-editable-field
          label="Empresa"
          name="company_id"
          :modelValue="opportunity.company_id"
          @update:modelValue="$emit('update-field', 'company_id', $event)"
          class="flex-1 text-sm"
        />
      </div>
      <div class="flex items-center gap-3">
        <div :class="avatarRingClass(opportunity.lead?.id)">
          <lead-avatar
            :photo="opportunity.lead?.photo"
            :name="opportunity.lead?.name"
            :lead-id="opportunity.lead?.id"
            size="md"
          />
        </div>
        <leads-select-editable-field
          label="Cliente"
          name="lead_id"
          :modelValue="opportunity.lead_id"
          @update:modelValue="$emit('update-field', 'lead_id', $event)"
          class="flex-1 text-sm"
        />
      </div>
      <div class="flex items-center gap-3">
        <div :class="avatarRingClass(opportunity.user?.id)">
          <user-avatar
            :photo="opportunity.user?.photo"
            :name="opportunity.user?.name"
            :user-id="opportunity.user?.id"
            size="md"
          />
        </div>
        <users-select-editable-field
          label="Responsável"
          name="user_id"
          :modelValue="opportunity.user_id"
          @update:modelValue="$emit('update-field', 'user_id', $event)"
          class="flex-1 text-sm"
        />
      </div>
    </div>
  </SectionCard>
</template>

<script>
import SectionCard from "@/components/common/SectionCard.vue";
import CompaniesSelectEditableField from "../fields/selects/CompaniesSelectEditableField.vue";
import CompanyAvatar from "../common/CompanyAvatar.vue";
import LeadAvatar from "../common/LeadAvatar.vue";
import UserAvatar from "../common/UserAvatar.vue";
import LeadsSelectEditableField from "../fields/selects/LeadsSelectEditableField.vue";
import UsersSelectEditableField from "../fields/selects/UsersSelectEditableField.vue";

export default {
  name: "OpportunityInfoSection",
  components: {
    SectionCard,
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
  methods: {
    // Anel na cor primária em volta do avatar; com id, o avatar abre o modal e o hover indica o clique
    avatarRingClass(id) {
      return [
        "flex flex-shrink-0 rounded-full ring-2 ring-primary transition-all duration-200",
        id ? "hover:ring-4 hover:scale-110 hover:shadow-md" : "",
      ];
    },
  },
};
</script>