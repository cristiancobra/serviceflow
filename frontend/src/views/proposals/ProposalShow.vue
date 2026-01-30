<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tools" class="page-icon" />
        <h1>PROPOSTA</h1>
      </div>
      <div class="action-container">
        <select-status-button
          :status="proposal.status"
          @update:modelValue="updateProposal('status', $event)"
        />
      </div>
    </div>

    <div class="section-container">
      <div class="w-1/2">
        <div class="row-simple">
          <opportunities-select-editable-field
            label="Oportunidade"
            v-model="proposal.opportunity_id"
            @update:modelValue="updateTask('opportunity_id', $event)"
            fieldNull="Nenhum"
          />
        </div>
      </div>

      <timeline-proposal :proposal="proposal" />
    </div>

    <description-section
      :description="proposal.description"
      @update:description="updateProposal('description', $event)"
    />

    <proposal-services-section
      :proposalServices="proposal.proposalServices"
      @update-proposal="updateProposalFromServices"
    />

    <proposal-costs-section
      :proposal="proposal"
      @update-proposal-cost="updateProposalFromCosts"
      @update-total-third-party-cost="updateTotalThirdPartyCost"
    />

    <proposal-profit-margin-section
      :proposal="proposal"
      @update-proposal="updateProposal"
    />

    <invoices-list-section :proposal="proposal" @reload-proposal="getProposal" />

    <div
      class="flex flex-wrap items-center justify-between px-10 gap-6 py-6 mt-8 border-t border-gray-200"
    >
      <button
        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm"
        @click="deleteProposal()"
      >
        Excluir
      </button>

      <div class="flex items-center gap-4">
        <!-- Toggle Switch usando apenas Tailwind -->
        <label class="flex items-center gap-2 cursor-pointer">
          <div class="relative">
            <input
              type="checkbox"
              class="sr-only"
              v-model="isVisibleQuantity"
            />
            <div
              class="w-11 h-6 bg-gray-200 rounded-full shadow-inner transition-colors duration-200 ease-in-out"
              :class="isVisibleQuantity ? 'bg-blue-600' : 'bg-gray-300'"
            ></div>
            <div
              class="absolute w-4 h-4 bg-white rounded-full shadow top-1 transition-transform duration-200 ease-in-out transform"
              :class="isVisibleQuantity ? 'translate-x-6' : 'translate-x-1'"
            ></div>
          </div>
          <span class="text-sm text-gray-700 font-medium">quantidades</span>
        </label>

        <button
          class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm"
          @click="exportPDF()"
        >
          Gerar PDF
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { BACKEND_URL } from "@/config/apiConfig";
import {
  destroy,
  show,
  updateField,
  updateRelationshipField,
} from "@/utils/requests/httpUtils";
import DescriptionSection from "@/components/show/DescriptionSection.vue";
import OpportunitiesSelectEditableField from "../../components/fields/selects/OpportunitiesSelectEditableField.vue";
import ProposalProfitMarginSection from "@/components/show/ProposalProfitMarginSection.vue";
import ProposalServicesSection from "@/components/show/ProposalServicesSection.vue";
import ProposalCostsSection from "@/components/show/ProposalCostsSection.vue";
import SelectStatusButton from "../../components/buttons/SelectStatusButton.vue";
import TimelineProposal from "@/components/TimelineProposal.vue";
import InvoicesListSection from '../../components/lists/InvoicesListSection.vue';

export default {
  data() {
    return {
      proposal: {
        invoices: [],
      },
      proposalId: "",
      isVisibleQuantity: false,
    };
  },
  components: {
    DescriptionSection,
    OpportunitiesSelectEditableField,
    ProposalProfitMarginSection,
    ProposalCostsSection,
    ProposalServicesSection,
    SelectStatusButton,
    TimelineProposal,
    InvoicesListSection,
  },
  methods: {
    destroy,
    show,
    updateField,
    updateRelationshipField,
    addInvoiceCreated(newInvoices) {
      console.log(newInvoices);
      this.proposal.invoices.push(...newInvoices);
    },
    async deleteProposal() {
      this.response = await destroy("proposals", this.proposalId);
      this.$router.push({
        name: "opportunityShow",
        params: { id: this.proposal.opportunity_id },
      });
    },
    formatDateBr(date) {
      if (!date) return "";

      const dateObj = new Date(date);
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1; // Os meses em JavaScript começam em 0, então adicionamos 1
      const year = dateObj.getFullYear();

      // Formate a data no formato desejado (exemplo: dd/mm/aaaa)
      const dateBr = `${day}/${month}/${year}`;

      return dateBr;
    },
    exportPDF() {
      console.log(this.isVisibleQuantity);
      const url = `${BACKEND_URL}proposals/${this.proposal.id}/pdf?isVisibleQuantity=${this.isVisibleQuantity}`;
      window.open(url, "_blank");
    },
    async getProposal() {
      this.proposal = await show("proposals", this.proposalId);
    },
    setProposalId(proposalId) {
      this.proposalId = proposalId;
    },
    async updateProposalFromServices(fieldName, serviceId, editedValue) {
      const payload = {
        proposalServices: [
          {
            service_id: serviceId,
            [fieldName]: editedValue,
          },
        ],
      };

      try {
        const updatedProposal = await updateRelationshipField(
          "proposals",
          this.proposalId,
          payload
        );

        this.proposal = updatedProposal;
      } catch (error) {
        console.error("Erro ao atualizar a proposta:", error);
      }
    },
    async updateProposalFromCosts(fieldName, costId, editedValue) {
      const payload = {
        proposalCosts: [
          {
            cost_id: costId,
            [fieldName]: editedValue,
          },
        ],
      };

      try {
        const updatedProposal = await updateRelationshipField(
          "proposals",
          this.proposalId,
          payload
        );

        this.proposal = updatedProposal;
      } catch (error) {
        console.error("Erro ao atualizar os custos da proposta:", error);
      }
    },
    async updateProposal(fieldName, editedValue) {
      try {
        const updatedProposal = await updateField(
          "proposals",
          this.proposalId,
          fieldName,
          editedValue
        );
        this.proposal = updatedProposal;
      } catch (error) {
        console.error("Erro ao atualizar a proposta:", error);
      }
    },
  },
  updateTotalThirdPartyCost(newTotalThirdPartyCost) {
      this.proposal.total_third_party_cost = newTotalThirdPartyCost;
    },
  async mounted() {
    this.setProposalId(this.$route.params.id);
    this.getProposal();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
p {
  text-align: left;
  font-weight: 400;
}

h3 {
  margin: 40px 0 0;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: rgb(61, 61, 61);
}

a:link {
  text-decoration: none;
}

a:visited {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

a:active {
  text-decoration: none;
}

.label {
  font-weight: 800;
}

.title {
  font-size: 32px;
  font-weight: 900;
  padding-top: 10px;
  padding-bottom: 10px;
}

.container-card {
  margin-left: 180px;
  margin-right: 180px;
  margin-bottom: 60px;
  margin-top: 60px;
}
</style>
