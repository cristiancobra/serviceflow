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
      <div class="column-50">
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

    <description-section :description="proposal.description" />

    <proposal-services-section
      :services="proposal.proposalServices"
      @update-proposal="updateProposalFromServices"
    />

    <div class="table-row">
      <div class="column-50">
        <proposal-profit-margin-section :proposal="proposal" />
      </div>
      <div class="column-50">
        <proposal-costs-section
          :proposal="proposal"
          @update-total-third-party-cost="updateTotalThirdPartyCost"
        />
      </div>
    </div>

    <installment-section :proposal="proposal" />

    <div class="table-row">
      <div class="">
        <button class="button delete me-5" @click="deleteProposal()">
          excluir
        </button>
      </div>
      <div class="">
        <div class="toggle-switch">
          <input
            type="checkbox"
            id="toggle"
            class="toggle-checkbox"
            v-model="isVisibleQuantity"
          />
          <label for="toggle" class="toggle-label">quantidades</label>
        </div>
      </div>
      <div class="">
        <button class="button" @click="exportPDF()">Gerar PDF</button>
      </div>
    </div>
  </div>
</template>


<script>
import { BACKEND_URL } from "@/config/apiConfig";
import {
  destroy,
  show,
  updateRelationshipField,
} from "@/utils/requests/httpUtils";
import DescriptionSection from "@/components/show/DescriptionSection.vue";
import OpportunitiesSelectEditableField from "../../components/fields/selects/OpportunitiesSelectEditableField.vue";
import ProposalProfitMarginSection from "@/components/show/ProposalProfitMarginSection.vue";
import ProposalServicesSection from "@/components/show/ProposalServicesSection.vue";
import ProposalCostsSection from "@/components/show/ProposalCostsSection.vue";
import SelectStatusButton from "../../components/buttons/SelectStatusButton.vue";
import InstallmentSection from "@/components/show/InstallmentSection.vue";
import TimelineProposal from "@/components/TimelineProposal.vue";

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
    InstallmentSection,
    OpportunitiesSelectEditableField,
    ProposalProfitMarginSection,
    ProposalCostsSection,
    ProposalServicesSection,
    SelectStatusButton,
    TimelineProposal,
  },
  methods: {
    destroy,
    show,
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
      console.log(
        "updateProposalFromServices",
        fieldName,
        serviceId,
        editedValue
      );
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
        console.log("Proposta atualizada com sucesso:", updatedProposal);
      } catch (error) {
        console.error("Erro ao atualizar a proposta:", error);
      }
    },
    updateTotalThirdPartyCost(newTotalThirdPartyCost) {
      this.proposal.total_third_party_cost = newTotalThirdPartyCost;
    },
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

/* switch */
.toggle-switch {
  position: relative;
  width: 60px;
  height: 34px;
  margin-right: 6px;
  margin-top: 3px;
}

.toggle-checkbox {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-label {
  position: absolute;
  cursor: pointer;
  background-color: #ccc;
  border-radius: 34px;
  width: 100%;
  height: 100%;
  transition: background-color 0.2s;
  padding-left: 60px;
  padding-top: 4px;
}

.toggle-label::before {
  content: "";
  position: absolute;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background-color: white;
  top: 4px;
  left: 4px;
  transition: transform 0.2s;
}

.toggle-checkbox:checked + .toggle-label {
  background-color: var(--primary);
}

.toggle-checkbox:checked + .toggle-label::before {
  transform: translateX(26px);
}
</style>
