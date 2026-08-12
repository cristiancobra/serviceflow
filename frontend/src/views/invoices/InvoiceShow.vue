<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon
          icon="fa-solid fa-file-invoice-dollar"
          class="page-icon"
        />
        <h1>FATURA</h1>
      </div>
      <div class="action-container">
        <select-status-button
          :status="invoice.status"
          @update:modelValue="updateInvoice('status', $event)"
        />
      </div>
    </div>

    <!-- Mensagem de erro -->
    <div v-if="errorMessage" class="section-container">
      <div class="bg-red-50 border-2 border-red-500 rounded-lg p-4 mb-6">
        <div class="flex gap-4">
          <div class="flex-shrink-0 pt-0.5">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="text-2xl text-red-600" />
          </div>
          <div class="flex-1">
            <h3 class="text-red-800 font-bold text-lg mb-2">Erro ao atualizar fatura</h3>
            <p class="text-red-700 text-sm">{{ errorMessage }}</p>
          </div>
          <button @click="errorMessage = null" class="text-red-500 hover:text-red-700">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>
      </div>
    </div>

    <div class="section-container">
      <div class="flex align-items-center justify-end mb-6">
        <!-- Nome da conta (para invoices standalone) -->
        <div v-if="invoice.name" class="flex-1 mr-6">
          <h2 class="text-2xl font-bold text-gray-900">{{ invoice.name }}</h2>
          <p class="text-sm text-gray-500 mt-0.5">
            <span
              v-if="invoice.category"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mr-2"
            >
              {{ getCategoryLabel(invoice.category) }}
            </span>
            Conta a pagar
          </p>
        </div>
        <div
          class="relative rounded-2xl border-2 border-primary bg-primary-50/60 p-4 shadow-sm"
        >
          <div
            class="absolute -top-3 left-4 inline-flex items-center gap-2 rounded-md bg-primary px-2 py-0.5 text-xs font-semibold text-white shadow"
          >
            <font-awesome-icon icon="fas fa-badge-dollar" class="hidden" />
            <span>VALOR DA FATURA</span>
          </div>
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div
                class="grid h-10 w-10 place-items-center rounded-lg bg-primary text-white shadow"
              >
                <font-awesome-icon icon="fas fa-dollar-sign" />
              </div>
              <span class="text-sm font-medium text-primary">Preço</span>
            </div>
            <div class="min-w-[180px] text-right text-primary">
              <div
                class="inline-flex items-center rounded-lg bg-white px-3 py-2 ring-1 ring-emerald-200 shadow-sm ml-auto"
              >
                <money-editable-field
                  name="price"
                  v-model="invoice.price"
                  @update:modelValue="updateInvoice('price', $event)"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-lg border border-gray-200 p-6 mb-6">
        <div class="space-y-4">
          <!-- Empresa -->
          <div class="flex items-center gap-3">
            <company-avatar
              :photo="invoice.company?.photo"
              :business-name="invoice.company?.business_name"
              :legal-name="invoice.company?.legal_name"
              :company-id="invoice.company?.id"
              size="md"
            />
            <companies-select-editable-field
              label="Empresa"
              name="company_id"
              :modelValue="invoice.company_id"
              @update:modelValue="updateInvoice('company_id', $event)"
              class="flex-1"
            />
          </div>

          <!-- Lead -->
          <div class="flex items-center gap-3">
            <lead-avatar
              :photo="invoice.lead?.photo"
              :name="invoice.lead?.name"
              :lead-id="invoice.lead?.id"
              size="md"
            />
            <leads-select-editable-field
              label="Cliente"
              name="lead_id"
              :modelValue="invoice.lead_id"
              @update:modelValue="updateInvoice('lead_id', $event)"
              class="flex-1"
            />
          </div>

          <!-- Oportunidade -->
          <div v-if="invoice.proposal?.opportunity">
            <label class="text-gray-700 font-semibold text-sm mb-2 block">Oportunidade</label>
            <router-link
              :to="{
                name: 'opportunityShow',
                params: { id: invoice.proposal.opportunity.id },
              }"
              class="text-blue-600 hover:text-blue-800 flex items-center gap-2"
            >
              <font-awesome-icon icon="fa-solid fa-magnifying-glass" class="text-sm" />
              {{ invoice.proposal.opportunity.name }}
            </router-link>
          </div>
        </div>
      </div>

      <div v-if="invoice.proposal_id" class="rounded-lg border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Proposta</h3>
        <div>
          <label class="text-gray-700 font-semibold text-sm mb-2 block">Proposta Associada</label>
          <router-link
            v-if="invoice.proposal"
            :to="{ name: 'proposalShow', params: { id: invoice.proposal.id } }"
            class="text-blue-600 hover:text-blue-800 flex items-center gap-2"
          >
            <font-awesome-icon
              icon="fa-solid fa-magnifying-glass"
              class="text-sm"
            />
            Proposta {{ invoice.proposal.id }} - {{ invoice.proposal.description }}
          </router-link>
          <p v-else class="text-gray-500">Sem proposta associada</p>
        </div>
      </div>

      <!-- Tarefa Financeira vinculada -->
      <div v-if="invoice.tasks && invoice.tasks.length > 0" class="rounded-lg border border-green-200 bg-green-50 p-6 mb-6">
        <h3 class="text-lg font-bold text-green-800 mb-3 flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-check-circle" class="text-green-600" />
          Tarefas Vinculadas
        </h3>
        <div class="space-y-2">
          <div
            v-for="task in invoice.tasks"
            :key="task.id"
            class="flex items-center justify-between p-3 bg-white rounded-lg border border-green-200"
          >
            <div class="flex items-center gap-3">
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                :class="task.status === 'done' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ task.status === 'done' ? 'Concluída' : 'Pendente' }}
              </span>
              <span class="text-sm font-medium text-gray-800">{{ task.name }}</span>
            </div>
            <span v-if="task.date_due" class="text-xs text-gray-500">{{ formatDateBr(task.date_due) }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="section-container">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mt-5 mb-5">
          <label class="text-black font-bold">
            <font-awesome-icon icon="fa fa-calendar-alt" class="mr-2" />
            Data de Vencimento
          </label>
          <p class="text-black">{{ formatDateBr(invoice.date_due) }}</p>
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
          <font-awesome-icon icon="fas fa-coins" class="icon text-xl pe-3" />
          <h2>Pagamentos Recebidos</h2>
        </div>
        <div class="section-action">
          <button
            type="button"
            class="btn btn-primary"
            @click="isCreateTransactionModalVisible = true"
          >
            <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
          </button>
          <transaction-create-form
            v-model="isCreateTransactionModalVisible"
            :invoice="invoice"
            @new-transaction-event="addTransactionCreated"
          />
        </div>
      </div>

      <div
        v-if="!invoice.transactions || invoice.transactions.length === 0"
        class="w-full rounded-xl border border-dashed border-indigo-200 bg-gradient-to-r from-indigo-50 to-sky-50 py-8 text-center text-indigo-700 shadow-sm"
      >
        <p class="text-sm font-medium">Nenhum pagamento recebido</p>
      </div>

      <!-- <div
        v-else
        class="mt-4 space-y-2 rounded-xl border border-gray-200 bg-white p-2 border-t-4 border-t-indigo-500 shadow-sm"
      >
        <div
          v-for="transaction in invoice.transactions"
          :key="transaction.id"
          class="group flex items-center justify-between px-4 py-3 rounded-md bg-white even:bg-sky-50/40 hover:bg-sky-100/60 border-l-4 border-transparent hover:border-sky-400 transition-colors"
        >
          <div class="min-w-[160px]">
            <div
              class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1"
            >
              <span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>
              <date-editable-input
                name="transaction_date"
                :modelValue="transaction.transaction_date"
                @save="
                  updateTransaction('transaction_date', $event, transaction.id)
                "
                class-text="text-sm font-semibold text-indigo-700"
              />
            </div>
          </div>
          <div class="flex-1"></div>
          <div
            class="text-right inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
          >
            <money-editable-field
              name="amount"
              :modelValue="transaction.amount"
              @save="updateTransaction('amount', $event, transaction.id)"
            />
          </div>
        </div>
      </div> -->

      <!-- Totais da fatura -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <div class="text-xs font-semibold text-gray-500">Total da Fatura</div>
          <div class="mt-1 text-1xl font-bold text-gray-800">
            <money-field name="total" :modelValue="invoiceTotal" readonly />
          </div>
        </div>
        <div
          class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"
        >
          <div class="text-xs font-semibold text-emerald-700">
            Total Recebido
          </div>
          <div class="mt-1 text-1xl font-bold text-emerald-800">
            <money-field name="paid" :modelValue="transactionsTotal" readonly />
          </div>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
          <div class="text-xs font-semibold text-sky-700">Saldo</div>
          <div
            class="mt-1 text-1xl font-bold"
            :class="balance >= 0 ? 'text-sky-800' : 'text-red-700'"
          >
            <money-field name="balance" :modelValue="balance" readonly />
          </div>
        </div>
      </div>
    </div>

    <div
      class="flex flex-wrap items-center justify-between px-10 gap-6 py-6 mt-8 border-t border-gray-200"
    >
      <button class="btn btn-error" @click="deleteInvoice()">Excluir</button>

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
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import MoneyField from "../../components/fields/number/MoneyField.vue";
import TransactionCreateForm from "../../components/forms/TransactionCreateForm.vue";
import SelectStatusButton from "../../components/buttons/SelectStatusButton.vue";
import DescriptionSection from "@/components/show/DescriptionSection.vue";
import MoneyEditableField from "../../components/fields/number/MoneyEditableField.vue";
import CompanyAvatar from "@/components/common/CompanyAvatar.vue";
import CompaniesSelectEditableField from "@/components/fields/selects/CompaniesSelectEditableField.vue";
import LeadAvatar from "@/components/common/LeadAvatar.vue";
import LeadsSelectEditableField from "@/components/fields/selects/LeadsSelectEditableField.vue";
// import DateEditableInput from "../../components/fields/date/DateEditableInput.vue";

export default {
  data() {
    return {
      invoice: [],
      invoiceId: "",
      isVisibleQuantity: false,
      isCreateTransactionModalVisible: false,
      errorMessage: null,
    };
  },
  components: {
    MoneyField,
    TransactionCreateForm,
    SelectStatusButton,
    DescriptionSection,
    MoneyEditableField,
    CompanyAvatar,
    CompaniesSelectEditableField,
    LeadAvatar,
    LeadsSelectEditableField,
  },
  computed: {
    invoiceTotal() {
      const v = Number(this.invoice?.price ?? 0);
      return isNaN(v) ? 0 : v;
    },
    transactionsTotal() {
      const list = this.invoice?.transactions ?? [];
      const sum = list.reduce((acc, t) => acc + Number(t?.amount ?? 0), 0);
      return isNaN(sum) ? 0 : sum;
    },
    balance() {
      return this.invoiceTotal - this.transactionsTotal;
    },
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
      this.isCreateTransactionModalVisible = false;
    },
    async deleteInvoice() {
      // Confirmação antes de excluir
      if (!confirm('Tem certeza que deseja excluir esta fatura? Esta ação não pode ser desfeita.')) {
        return;
      }

      try {
        this.errorMessage = null;
        await this.destroy("invoices", this.invoiceId);
        
        // Redireciona após excluir com sucesso
        if (this.invoice.proposal_id) {
          this.$router.push({
            name: "proposalShow",
            params: { id: this.invoice.proposal_id },
          });
        } else {
          this.$router.push({ name: "accountsPayable" });
        }
      } catch (error) {
        // Trata erros de validação do backend
        if (error.response && error.response.status === 422) {
          const errorData = error.response.data;
          this.errorMessage = errorData.message || 'Não foi possível excluir a fatura.';
        } else {
          this.errorMessage = 'Erro ao excluir fatura. Tente novamente.';
        }
        console.error("Erro ao excluir fatura:", error);
      }
    },
    formatDateBr(date) {
      if (!date) return "";

      const dateObj = new Date(date);
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1;
      const year = dateObj.getFullYear();

      return `${day}/${month}/${year}`;
    },
    getCategoryLabel(category) {
      const map = {
        fixed_cost: 'Custo Fixo',
        recurring: 'Recorrente',
        supplier: 'Fornecedor',
        operational: 'Operacional',
        other: 'Outro',
      };
      return map[category] || category;
    },
    exportPDF() {
      const url = `${BACKEND_URL}invoices/${this.invoice.id}/pdf?isVisibleQuantity=${this.isVisibleQuantity}`;
      window.open(url, "_blank");
    },
    async getInvoice() {
      this.invoice = await show("invoices", this.invoiceId);
    },
    setInvoiceId(invoiceId) {
      this.invoiceId = invoiceId;
    },
    async updateInvoice(fieldName, editedValue) {
      try {
        console.log("updateInvoice called:", fieldName, editedValue);
        this.errorMessage = null; // Limpa erro anterior
        await updateField(
          "invoices",
          this.invoiceId,
          fieldName,
          editedValue
        );
        console.log("updateField success");
        // Recarrega a fatura completa com todas as relações
        await this.getInvoice();
      } catch (error) {
        // Captura erro de validação do backend
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors;
          if (errors && errors.price) {
            this.errorMessage = errors.price[0];
          } else {
            this.errorMessage = error.response.data.message || 'Erro ao atualizar fatura';
          }
        } else {
          this.errorMessage = 'Erro ao atualizar fatura. Tente novamente.';
        }
        console.error("Erro ao atualizar fatura:", error);
        
        // Recarrega a fatura para restaurar o valor anterior
        await this.getInvoice();
      }
    },
    async updateTransaction(fieldName, editedValue, transactionId) {
      const updatedTransaction = await updateField(
        "transactions",
        transactionId,
        fieldName,
        editedValue
      );
      // Update local transaction
      const index = this.invoice.transactions.findIndex(
        (t) => t.id === transactionId
      );
      if (index !== -1) {
        this.invoice.transactions[index] = updatedTransaction;
      }
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

.toggle-checkbox:checked + .toggle-label {
  background-color: var(--primary);
}

.toggle-checkbox:checked + .toggle-label::before {
  transform: translateX(26px);
}
</style>
