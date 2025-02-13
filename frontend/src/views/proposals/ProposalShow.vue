<template>
  <div class="page-container">
    <div class="page-header">
      <div class="title-container">
        <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
        <h1>PROPOSTA</h1>
      </div>
      <div class="action-container">
        <select-status-button :status="proposal.status" @update:modelValue="updateProposal('status', $event)" />
      </div>
    </div>

    <div class="row mt-1 mb-3">
      <div class="col-6">
        <opportunities-select-editable-field label="Oportunidade" v-model="proposal.opportunity_id"
          @update:modelValue="updateTask('opportunity_id', $event)" fieldNull="Nenhum" />
      </div>
      <div class="col-6">
        <p class="mt-1 mb-1 text-end">
          Rascunhada: {{ proposal.draft_at }}
        </p>
        <p class="mt-1 mb-1 text-end">
          Enviada: {{ proposal.submitted_at }}
        </p>
        <p class="mt-1 mb-1 text-end">
          Aceita: {{ proposal.accepted_at }}
        </p>
        <p class="mt-1 mb-1 text-end">
          Rejeitada: {{ proposal.rejected_at }}
        </p>
        <p class="mt-1 mb-1 text-end">
          Cancelada: {{ proposal.canceled_at }}
        </p>
      </div>
    </div>

    <div class="subtitle-container">
      <font-awesome-icon icon="fas fa-coins" class="icon" />
      <h2>
        Descrição
      </h2>
    </div>
    <div v-if="proposal.opportunity" class="row">
      <p>
        {{ proposal.opportunity.name }}
      </p>
    </div>

    <div class="section-container">
      <div class="subtitle-container">
        <font-awesome-icon icon="fas fa-file-invoice" class="icon" />
        <h2>
          Itens da proposta
        </h2>
      </div>
      <div class="row service-item pt-1 pb-1" v-for="proposalService in proposal.proposalServices"
        v-bind:key="proposalService.id">
        <div class="col-1 d-flex align-items-center justify-content-center">
          <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
        </div>
        <div class="col-6">
          <p class="name ps-2">
            {{ proposalService.name }}
          </p>
        </div>
        <div class="col-1">
          {{ proposalService.quantity }} x
        </div>
        <div class="col-2">
          <money-field name="price" v-model="proposalService.price" />
        </div>
        <div class="col-2">
          <money-field name="total_price" v-model="proposalService.total_price" />
        </div>
      </div>
    </div>

    <div class="section-container">
      <div class="section-header">
        <div class="subtitle-container">
          <font-awesome-icon icon="fas fa-dollar" class="icon" />
          <h2>
            Custos e margem de lucro
          </h2>
        </div>
      </div>

      <div class="row">
        <div class="col-3 d-flex justify-content-start">
          <p>
            <font-awesome-icon icon="fa fa-clock" />
            <span class="label"> Custo operacional</span>
          </p>
        </div>
        <div class="col-1  d-flex justify-content-end">
          <hours-decimal-editable-field name="total_hours" v-model="proposal.total_hours"
            placeholder="quantidade total de horas" @save="updateProposal('total_hours', $event)" />
          h
        </div>
        <div class="col-1 text-end">
          <money-field name="total_operational_cost" v-model="proposal.total_operational_cost" />
        </div>
      </div>

      <div class="row">
        <div class="col-3 d-flex justify-content-start">
          <p>
            <font-awesome-icon icon="fa fa-clock" />
            <span class="label"> Custos de propdução</span>
          </p>
        </div>
        <div class="col-2 text-end">
          <money-field name="total_third_party_cost" v-model="proposal.total_third_party_cost" />
        </div>
      </div>

      <div class="row">
        <div class="col-3 d-flex justify-content-start">
          <p>
            <font-awesome-icon icon="fas fa-percent" />
            <span class="label"> Lucro: </span>
          </p>
        </div>
        <div class="col-1 d-flex justify-content-end">
          <decimal-editable-field name="total_profit_percentage" v-model="proposal.total_profit_percentage"
            placeholder="percentual do lucro" @save="updateProposal('total_profit_percentage', $event)" />
          %
        </div>
        <div class="col-1 text-end">
          <money-field name="total_profit" v-model="proposal.total_profit" />
        </div>
      </div>

      <div class="row">
        <div class="col-4 d-flex justify-content-start">
          <p>
            <font-awesome-icon icon="fas fa-dollar-sign" />
            <span class="label"> Preço: </span>
          </p>
        </div>
        <div class="col-1 text-end">
          <money-field name="price" v-model="proposal.total_price" />
        </div>
      </div>

      <div class="row">
        <div class="col-3 d-flex justify-content-start">
          <p>
            <font-awesome-icon icon="fas fa-credit-card" />
            <span class="label"> Parcelamento: </span>
          </p>
        </div>
        <div class="col-2 text-end">
          <integer-editable-field v-model="proposal.installment_quantity"
            @save="updateProposal('installment_quantity', $event)" placeholder="quantidade de parcelas" />
        </div>
      </div>


      <div class="row pt-5 mb-5 ">
        <div class="col-3">
          <p>
            <font-awesome-icon icon="fa fa-calendar-alt" />
            <span class="label"> Data de criação: </span>
          </p>
        </div>
        <div class="col-2 text-end">
          {{ formatDateBr(proposal.created_at) }}
        </div>
      </div>

      <div class="section-container">
        <div class="subtitle-container">
          <font-awesome-icon icon="fas fa-dollar" class="icon" />
          <h2>
            Custos de produção
          </h2>
        </div>
        <div class="row service-item pt-1 pb-1" v-for="proposalCost in proposal.proposalCosts"
          v-bind:key="proposalCost.id">
          <div class="col-1 d-flex align-items-center justify-content-center">
            <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
          </div>
          <div class="col-6">
            <p class="name ps-2">
              {{ proposalCost.name }}
            </p>
          </div>
          <div class="col-1">
            {{ proposalCost.quantity }} x
          </div>
          <div class="col-2">
            <money-field name="price" v-model="proposalCost.price" />
          </div>
          <div class="col-2">
            <money-field name="total_price" v-model="proposalCost.total_price" />
          </div>
        </div>
      </div>
    </div>

      <div class="section-container">
        <div class="section-header">
        <div class="subtitle-container">
          <font-awesome-icon icon="fas fa-dollar" class="icon" />
          <h2>
            Faturamento
          </h2>
        </div>
        <div class="action-container">
          <invoice-create-form @new-invoice-event="addInvoiceCreated" :proposal="proposal" />
        </div>
        </div>

        <div class="row service-item pt-1 pb-1" v-for="invoice in proposal.invoices" :key="invoice.id">
          <router-link :to="{ name: 'invoiceShow', params: { id: invoice.id } }" class="col-12">
            <div class="row">
              <div class="col-2">
                {{ invoice.date_due }}
              </div>
              <div class="col-2">
                <money-field name="price" v-model="invoice.price" />
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <div class="row mt-5 mb-5">
        <div class="col-2 d-flex justify-content-start">
          <button class="button delete me-5" @click="deleteProposal()">
            excluir
          </button>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <div class="toggle-switch">
            <input type="checkbox" id="toggle" class="toggle-checkbox" v-model="isVisibleQuantity">
            <label for="toggle" class="toggle-label">quantidades</label>
          </div>
        </div>
        <div class="col-4 d-flex justify-content-end">
          <button class="button" @click="exportPDF()">
            Gerar PDF
          </button>
        </div>
      </div>
    </div>
</template>


<script>
import { BACKEND_URL } from "@/config/apiConfig";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import DecimalEditableField from "@/components/fields/number/DecimalEditableField";
import MoneyField from '../../components/fields/number/MoneyField.vue';
import HoursDecimalEditableField from '../../components/fields/number/HoursDecimalEditableField.vue';
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import InvoiceCreateForm from "../../components/forms/InvoiceCreateForm.vue";
import OpportunitiesSelectEditableField from '../../components/fields/selects/OpportunitiesSelectEditableField.vue';
import SelectStatusButton from '../../components/buttons/SelectStatusButton.vue';

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
    DecimalEditableField,
    HoursDecimalEditableField,
    IntegerEditableField,
    InvoiceCreateForm,
    MoneyField,
    OpportunitiesSelectEditableField,
    SelectStatusButton,
  },
  methods: {
    destroy,
    show,
    updateField,
    addInvoiceCreated(newInvoices) {
      console.log(newInvoices);
      this.proposal.invoices.push(...newInvoices);
    },
    async deleteProposal() {
      this.response = await destroy('proposals', this.proposalId);
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
      window.open(url, '_blank');
    },
    async getProposal() {
      this.proposal = await show('proposals', this.proposalId);
    },
    setProposalId(proposalId) {
      this.proposalId = proposalId;
    },
    async updateProposal(fieldName, editedValue) {
      this.proposal = await updateField("proposals", this.proposalId, fieldName, editedValue);
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

.toggle-checkbox:checked+.toggle-label {
  background-color: var(--primary);
}

.toggle-checkbox:checked+.toggle-label::before {
  transform: translateX(26px);
}
</style>
