<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="page-icon" />
        <h1>FATURA</h1>
      </div>
      <div class="action-container">
        <select-status-button
          :status="invoice.status"
          @update:modelValue="updateInvoice('status', $event)"
        />
      </div>
    </div>

    <div class="section-container">
      <div class="w-1/2">
        <div class="row-simple">
          <div class="field-container">
            <label class="text-black font-bold">Proposta</label>
            <p v-if="invoice.proposal" class="text-black">
              {{ invoice.proposal.opportunity?.name || 'Sem oportunidade associada' }}
            </p>
            <p v-else class="text-black text-gray-500">
              Sem proposta associada
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="section-container">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="field-container">
          <label class="text-black font-bold">
            <font-awesome-icon icon="fa fa-calendar-alt" class="mr-2" />
            Data de Vencimento
          </label>
          <p class="text-black">{{ formatDateBr(invoice.date_due) }}</p>
        </div>

        <div class="field-container">
          <label class="text-black font-bold">
            <font-awesome-icon icon="fas fa-dollar-sign" class="mr-2" />
            Preço
          </label>
          <div class="text-black">
            <money-field name="price" v-model="invoice.price" />
          </div>
        </div>

        <div class="field-container" v-if="invoice.proposal">
          <label class="text-black font-bold">
            <font-awesome-icon icon="fas fa-credit-card" class="mr-2" />
            Parcelamento
          </label>
          <p class="text-black">{{ invoice.proposal.installment_quantity }}x</p>
        </div>

        <div class="field-container">
          <label class="text-black font-bold">
            <font-awesome-icon icon="fa fa-calendar-plus" class="mr-2" />
            Data de Criação
          </label>
          <p class="text-black">{{ formatDateBr(invoice.created_at) }}</p>
        </div>
      </div>
    </div>

    <description-section 
      :description="invoice.description"
      @update:description="updateInvoice('description', $event)"
    />

    <div class="section-container">
      <div class="section-header">
        <div class="section-title">
          <font-awesome-icon icon="fas fa-coins" class="section-icon text-xl pe-3" />
          <h2>Pagamentos Recebidos</h2>
        </div>
        <div class="section-action">
          <transaction-create-form 
            :invoice="invoice" 
            @new-transaction-event="addTransactionCreated" 
          />
        </div>
      </div>

      <div v-if="!invoice.transactions || invoice.transactions.length === 0" class="empty-state">
        <p>Nenhum pagamento recebido</p>
      </div>
      
      <div v-else class="transactions-list">
        <div 
          v-for="transaction in invoice.transactions" 
          :key="transaction.id"
          class="transaction-item"
        >
          <div class="w-2/10 text-black font-bold">
            {{ formatDateBr(transaction.transaction_date) }}
          </div>
          <div class="transaction-amount">
            <money-field name="amount" v-model="transaction.amount" />
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-start gap-4 py-4">
      <div>
        <button class="button delete me-5" @click="deleteInvoice()">
          excluir
        </button>
      </div>
      <div>
        <div class="toggle-switch">
          <input type="checkbox" id="toggle" class="toggle-checkbox" v-model="isVisibleQuantity">
          <label for="toggle" class="toggle-label">quantidades</label>
        </div>
      </div>
      <div>
        <button class="button" @click="exportPDF()">Gerar PDF</button>
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL } from "@/config/apiConfig";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import MoneyField from '../../components/fields/number/MoneyField.vue';
import TransactionCreateForm from "../../components/forms/TransactionCreateForm.vue";
import SelectStatusButton from "../../components/buttons/SelectStatusButton.vue";
import DescriptionSection from "@/components/show/DescriptionSection.vue";

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
    SelectStatusButton,
    DescriptionSection,
  },
  methods: {
    destroy,
    show,
    updateField,
    addTransactionCreated(newTransaction) {
      // Inicializa o array de transactions se não existir
      if (!this.invoice.transactions) {
        this.invoice.transactions = [];
      }
      // Adiciona a nova transação ao início da lista
      this.invoice.transactions.unshift(newTransaction);
      console.log('Nova transação adicionada:', newTransaction);
    },
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
