<template>
  <div>
    <div class="section-container">
      <div class="section-header">
        <div class="section-title">
          <font-awesome-icon icon="fas fa-dollar" class="icon" />
          <h2>Faturamento</h2>
        </div>
        <div class="action-container">
          <invoice-create-form
            @new-invoice-event="addInvoiceCreated"
            :proposal="proposal"
          />
        </div>
      </div>

      <!-- Mensagem quando não há faturas -->
      <div
        v-if="localInvoices.length === 0"
        class="flex flex-col items-center justify-center py-8 px-4 text-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
      >
        <div
          class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4"
        >
          <font-awesome-icon
            icon="fa-solid fa-file-invoice"
            class="text-2xl text-gray-400"
          />
        </div>
        <h3 class="text-lg font-medium text-gray-600 mb-2">
          Nenhuma fatura criada
        </h3>
        <p class="text-sm text-gray-500 max-w-sm">
          Use o botão acima para gerar as faturas desta proposta
          automaticamente.
        </p>
      </div>

      <!-- Lista de faturas existentes -->
      <div v-else class="space-y-2">
        <div
          v-for="invoice in localInvoices"
          :key="invoice.id"
          class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200"
        >
          <router-link
            :to="{ name: 'invoiceShow', params: { id: invoice.id } }"
            class="flex items-center justify-between p-2 text-gray-800 hover:text-blue-600 transition-colors duration-200 no-underline"
          >
            <div class="flex items-center gap-4">
              <div
                :class="calculateInvoiceBalance(invoice) === 0 ? 'bg-success' : 'bg-blue-500'"
                class="flex items-center justify-center w-10 h-10 rounded-full"
              >
                <font-awesome-icon
                  icon="fa fa-receipt"
                  class="text-white"
                />
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-600"
                  >Vencimento</span
                >
                <span class="text-base font-semibold">{{
                  invoice.date_due
                }}</span>
              </div>
            </div>

            <div class="flex items-center gap-6">
              <div class="flex items-center">
                <font-awesome-icon
                  icon="fas fa-dollar-sign"
                  class="text-gray-400 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Valor:</span>
                <span class="text-blue-600 font-bold">
                  {{ formatCurrency(invoice.price) }}
                </span>
              </div>

              <div class="flex items-center">
                <font-awesome-icon
                  icon="fas fa-check-circle"
                  class="text-gray-400 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Pago:</span>
                <span class="text-success font-bold">
                  {{ formatCurrency(invoice.total_paid || 0) }}
                </span>
              </div>

              <div class="flex items-center">
                <font-awesome-icon
                  icon="fas fa-balance-scale"
                  class="text-gray-400 mr-2 w-4"
                />
                <span class="font-medium mr-1 text-sm">Saldo:</span>
                <span
                  :class="
                    calculateInvoiceBalance(invoice) === 0
                      ? 'text-gray-600'
                      : 'text-orange-600'
                  "
                  class="font-bold"
                >
                  {{ formatCurrency(calculateInvoiceBalance(invoice)) }}
                </span>
              </div>

              <button
                v-if="invoice.balance > 0"
                @click.prevent.stop="openTransactionModal(invoice)"
                class="btn btn-primary"
                title="Adicionar Transação"
              >
                <font-awesome-icon icon="fas fa-plus" class="text-sm" />
              </button>

              <div class="w-6 h-6 flex items-center justify-center">
                <font-awesome-icon
                  icon="fa-solid fa-chevron-right"
                  class="text-gray-400 text-sm"
                />
              </div>
            </div>
          </router-link>

          <!-- Pagamentos Recebidos -->
          <div
            v-if="invoice.transactions && invoice.transactions.length > 0"
            class="mt-0 space-y-1 rounded-xl border border-gray-200 bg-white p-2 border-t-4 shadow-sm"
          >
            <div
              v-for="transaction in invoice.transactions"
              :key="transaction.id"
              class="group flex items-center justify-between ms-36 px-1 py-1 rounded-md bg-white even:bg-sky-50/40 hover:bg-sky-100/60 border-l-4 border-transparent hover:border-sky-400 transition-colors"
            >
              <div class="flex items-center justify-center w-6 h-6 me-2 bg-primary rounded-full">
                <font-awesome-icon
                  icon="fas fa-coins"
                  class="text-white text-xs"
                />
              </div>
              <div class="min-w-[160px]">
                <div
                  class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1"
                >
                  <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                  <date-time-editable-input
                    name="transaction_date"
                    :modelValue="transaction.transaction_date"
                    @save="
                      updateTransaction(
                        'transaction_date',
                        $event,
                        transaction.id
                      )
                    "
                    class-text="text-sm font-semibold text-indigo-700"
                  />
                </div>
              </div>
              <div class="flex-1"></div>
              <div
                class="text-right inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
              >
                <money-field
                  name="amount"
                  v-model="transaction.amount"
                  readonly
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Totais das faturas -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold text-gray-500">
              Total das Faturas
            </div>
            <div class="mt-1 text-1xl font-bold text-gray-800">
              <money-field name="total" :modelValue="totalInvoices" readonly />
            </div>
          </div>
          <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"
          >
            <div class="text-xs font-semibold text-emerald-700">
              Total Recebido
            </div>
            <div class="mt-1 text-1xl font-bold text-emerald-800">
              <money-field name="paid" :modelValue="totalPaid" readonly />
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
    </div>

    <transaction-create-form
      v-if="showTransactionModal"
      :modelValue="showTransactionModal"
      :invoice="selectedInvoice"
      @update:modelValue="showTransactionModal = $event"
      @new-transaction-event="handleNewTransaction"
    />
  </div>
</template>

<script>
import { updateField } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import DateTimeEditableInput from "../fields/datetime/DateTimeEditableInput.vue";
import InvoiceCreateForm from "@/components/forms/InvoiceCreateForm.vue";
import MoneyField from "../fields/number/MoneyField.vue";
import TransactionCreateForm from "@/components/forms/TransactionCreateForm.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      localInvoices: [...(this.proposal?.invoices || [])],
      showTransactionModal: false,
      selectedInvoice: null,
    };
  },
  components: {
    DateTimeEditableInput,
    InvoiceCreateForm,
    MoneyField,
    TransactionCreateForm,
  },
  watch: {
    "proposal.invoices": {
      handler(newInvoices) {
        this.localInvoices = [...(newInvoices || [])];
      },
      deep: true,
    },
  },
  computed: {
    totalInvoices() {
      return this.localInvoices.reduce(
        (acc, inv) => acc + Number(inv?.price ?? 0),
        0
      );
    },
    totalPaid() {
      return this.localInvoices.reduce(
        (acc, inv) => acc + Number(inv?.total_paid ?? 0),
        0
      );
    },
    balance() {
      return this.totalInvoices - this.totalPaid;
    },
  },
  methods: {
    formatDateBr,
    addInvoiceCreated(newInvoices) {
      // Adiciona as novas faturas aos dados locais
      if (Array.isArray(newInvoices)) {
        this.localInvoices.push(...newInvoices);
      } else {
        this.localInvoices.push(newInvoices);
      }

      // Emite evento para o componente pai atualizar a proposta original
      this.$emit("invoices-updated", this.localInvoices);
    },
    openTransactionModal(invoice) {
      this.selectedInvoice = invoice;
      this.showTransactionModal = true;
    },
    handleNewTransaction(newTransaction) {
      // Encontrar a invoice correspondente e adicionar a transação
      const invoice = this.localInvoices.find(
        (inv) => inv.id === newTransaction.invoice_id
      );
      if (invoice) {
        if (!invoice.transactions) {
          invoice.transactions = [];
        }
        invoice.transactions.push(newTransaction);
        // Atualizar total_paid
        invoice.total_paid =
          (invoice.total_paid || 0) + Number(newTransaction.amount);
      }
      // Emite evento para o pai recarregar a proposta
      this.$emit("reload-proposal");
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value);
    },
    calculateInvoiceBalance(invoice) {
      return invoice.price - (invoice.total_paid || 0);
    },
    async updateTransaction(fieldName, editedValue, transactionId) {
  const updatedTransaction = await updateField(
    "transactions",
    transactionId,
    fieldName,
    editedValue
  );

  if (!this.invoice || !Array.isArray(this.invoice.transactions)) {
    return;
  }

  const index = this.invoice.transactions.findIndex(
    (t) => t.id === transactionId
  );

  if (index !== -1) {
    this.invoice.transactions[index] = updatedTransaction;
  }
}
  },
};
</script>

<style scoped>
/* Removi todas as classes CSS personalizadas que foram substituídas por Tailwind */
</style>