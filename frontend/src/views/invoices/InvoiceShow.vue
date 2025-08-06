<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-file-invoice" class="icon" />
        <h1>FATURA</h1>
      </div>
      <div class="action-container">
        {{ invoice.id }}
      </div>
    </div>

    <div class="row mt-1 mb-5">
      <div class="col-6">
        <p v-if="invoice.proposal && invoice.proposal.opportunity">
          {{ invoice.proposal.opportunity.name }}
        </p>
      </div>
    </div>

    <div class="row pt-0 ">
      <div class="col-3 d-flex justify-content-start">
        <p>
          <font-awesome-icon icon="fa fa-calendar-alt" />
          <span class="label"> Data de criação: </span>
        </p>
      </div>
      <div class="col-1 text-end">
        {{ formatDateBr(invoice.created_at) }}
      </div>
    </div>

    <div class="row">
      <div class="col-2 d-flex justify-content-start">
        <font-awesome-icon icon="fas fa-credit-card" />
        <span class="label"> Parcelamento: </span>
      </div>
      <div v-if="invoice && invoice.proposal" class="col-2 text-end">
        {{ invoice.proposal.installment_quantity }}
      </div>
    </div>

    <div class="row">
      <div class="col-2 d-flex justify-content-start">
        <p>
          <font-awesome-icon icon="fas fa-dollar-sign" />
          <span class="label"> Preço: </span>
        </p>
      </div>
      <div class="col-2 text-end">
        <money-field name="price" v-model="invoice.price" />
      </div>
    </div>

    <div class="section-container">
      <div class="section-header">
        <div class="section-title">
          <font-awesome-icon icon="fas fa-coins" class="icon" />
          <h2>
            Pagamentos recebidos
          </h2>
        </div>
        <div class="action-container">
          <transaction-create-form :invoice="invoice" @new-transaction-event="addTransactionCreated" />
        </div>
      </div>

      <div v-if="!invoice.transactions" class="row pt-1 pb-1">
        <div class="col-12">
          <p>
            Nenhum pagamento recebido
          </p>
        </div>
      </div>
      <div class="row service-item pt-1 pb-1" v-for="transaction in invoice.transactions" :key="transaction.id">
        <router-link :to="{ name: 'transactionShow', params: { id: transaction.id } }" class="col-12">
          <div class="row">
            <div class="col-2">
              {{ transaction.date_due }}
            </div>
            <div class="col-2">
              <money-field name="price" v-model="transaction.price" />
            </div>
          </div>
        </router-link>
      </div>
    </div>

    <div class="row mt-5 mb-5">
      <div class="col-2 d-flex justify-content-start">
        <button class="button delete me-5" @click="deleteInvoice()">
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
import MoneyField from '../../components/fields/number/MoneyField.vue';
import TransactionCreateForm from "../../components/forms/TransactionCreateForm.vue";

export default {
  data() {
    return {
      invoice: [],
      invoiceId: "",
      isVisibleQuantity: false,
    };
  },
  components: {
    MoneyField,
    TransactionCreateForm,
  },
  methods: {
    destroy,
    show,
    updateField,
    async deleteInvoice() {
      this.response = await destroy('invoices', this.invoiceId);
      this.$router.push({
        name: "proposalShow",
        params: { id: this.invoice.proposal_id },
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
      const url = `${BACKEND_URL}invoices/${this.invoice.id}/pdf?isVisibleQuantity=${this.isVisibleQuantity}`;
      window.open(url, '_blank');
    },
    async getInvoice() {
      this.invoice = await show('invoices', this.invoiceId);
      console.log('getinoice', this.invoice);
    },
    setInvoiceId(invoiceId) {
      this.invoiceId = invoiceId;
    },
    async updateInvoice(fieldName, editedValue) {
      this.invoice = await updateField("invoices", this.invoiceId, fieldName, editedValue);
    },
  },
  mounted() {
    this.setInvoiceId(this.$route.params.id);
    this.getInvoice();
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
