<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fa-solid fa-arrow-down" class="icon text-red-600" />
        <h2>Faturas de Débito</h2>
      </div>
    </div>

    <!-- Mensagem quando não há faturas de débito -->
    <div
      v-if="debitInvoices.length === 0"
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
        Nenhuma fatura de débito
      </h3>
      <p class="text-sm text-gray-500 max-w-sm">
        Use o botão de "Fatura de Débito" na seção de custos para criar uma nova fatura.
      </p>
    </div>

    <!-- Lista de faturas de débito -->
    <div v-else class="space-y-2">
      <div
        v-for="invoice in debitInvoices"
        :key="invoice.id"
        class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200"
      >
        <router-link
          :to="{ name: 'invoiceShow', params: { id: invoice.id } }"
          class="flex items-center justify-between p-4 text-gray-800 hover:text-red-600 transition-colors duration-200 no-underline"
        >
          <!-- Tipo e Fornecedor -->
          <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
              <font-awesome-icon
                icon="fa-solid fa-receipt"
                class="text-red-600"
              />
            </div>
            <div class="flex flex-col gap-1">
              <span class="text-sm font-medium text-gray-600">Fornecedor</span>
              <span class="text-base font-semibold text-gray-800">
                {{ invoice.lead?.name || 'Sem fornecedor' }}
              </span>
            </div>
          </div>

          <!-- Informações da Fatura -->
          <div class="flex items-center gap-6">
            <div class="flex items-center">
              <font-awesome-icon
                icon="fas fa-calendar-alt"
                class="text-gray-400 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Vencimento:</span>
              <span class="text-gray-700 font-semibold">
                {{ formatDateBr(invoice.date_due) }}
              </span>
            </div>

            <div class="flex items-center">
              <font-awesome-icon
                icon="fas fa-dollar-sign"
                class="text-gray-400 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Valor:</span>
              <span class="text-red-600 font-bold">
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
                {{ formatCurrency(getInvoiceTotalPaid(invoice)) }}
              </span>
            </div>

            <div class="flex items-center">
              <font-awesome-icon
                icon="fas fa-balance-scale"
                class="text-gray-400 mr-2 w-4"
              />
              <span class="font-medium mr-1 text-sm">Saldo:</span>
              <span
                :class="{
                  'text-gray-600': getInvoiceBalance(invoice) === 0,
                  'text-red-600': getInvoiceBalance(invoice) > 0,
                }"
                class="font-bold"
              >
                {{ formatCurrency(getInvoiceBalance(invoice)) }}
              </span>
            </div>

            <button
              v-if="getInvoiceBalance(invoice) > 0"
              @click.prevent.stop="openTransactionModal(invoice)"
              class="btn btn-primary"
              title="Adicionar Pagamento"
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

        <!-- Pagamentos Realizados -->
        <div
          v-if="invoice.transactions && invoice.transactions.length > 0"
          class="mt-0 space-y-1 rounded-xl border border-gray-200 bg-white p-2 border-t-4 shadow-sm"
        >
          <div
            v-for="transaction in invoice.transactions"
            :key="transaction.id"
            class="group flex items-center justify-between ms-36 px-1 py-1 rounded-md bg-white even:bg-sky-50/40 hover:bg-sky-100/60 border-l-4 border-transparent hover:border-sky-400 transition-colors"
          >
            <div class="flex items-center justify-center w-6 h-6 me-2 bg-red-500 rounded-full">
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
                <span class="text-sm font-semibold text-indigo-700">
                  {{ formatDateBr(transaction.transaction_date) }}
                </span>
              </div>
            </div>
            <div class="flex-1"></div>
            <div
              class="text-right inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
            >
              <money-field
                name="amount"
                :modelValue="transaction.amount"
                readonly
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Totais das faturas de débito -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
          <div class="text-xs font-semibold text-gray-500">
            Total de Débitos
          </div>
          <div class="mt-1 text-1xl font-bold text-gray-800">
            <money-field name="total" :modelValue="totalDebits" readonly />
          </div>
        </div>
        <div
          class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"
        >
          <div class="text-xs font-semibold text-emerald-700">
            Total Pago
          </div>
          <div class="mt-1 text-1xl font-bold text-emerald-800">
            <money-field name="paid" :modelValue="totalPaidDebits" readonly />
          </div>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
          <div class="text-xs font-semibold text-sky-700">Saldo</div>
          <div
            class="mt-1 text-1xl font-bold"
            :class="balanceDebits >= 0 ? 'text-sky-800' : 'text-red-700'"
          >
            <money-field name="balance" :modelValue="balanceDebits" readonly />
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
      showTransactionModal: false,
      selectedInvoice: null,
    };
  },
  components: {
    MoneyField,
    TransactionCreateForm,
  },
  computed: {
    debitInvoices() {
      return (this.proposal?.invoices || []).filter(
        (invoice) => invoice.type === 'debit'
      ).map(invoice => ({
        ...invoice,
        lead: invoice.lead || { name: 'Sem fornecedor' }
      }));
    },
    totalDebits() {
      return this.debitInvoices.reduce((acc, inv) => {
        const price = Number(inv?.price) || 0;
        return acc + price;
      }, 0);
    },
    totalPaidDebits() {
      return this.debitInvoices.reduce((acc, inv) => {
        const transactions = inv?.transactions || [];
        const transactionsTotal = transactions.reduce((tAcc, t) => {
          return tAcc + (Number(t?.amount) || 0);
        }, 0);
        return acc + transactionsTotal;
      }, 0);
    },
    balanceDebits() {
      return this.totalDebits - this.totalPaidDebits;
    },
  },
  methods: {
    formatDateBr,
    updateField,
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value);
    },
    calculateInvoiceBalance(invoice) {
      return invoice.price - (invoice.total_paid || 0);
    },
    getInvoiceTotalPaid(invoice) {
      const transactions = invoice.transactions || [];
      return transactions.reduce((acc, t) => acc + (Number(t?.amount) || 0), 0);
    },
    getInvoiceBalance(invoice) {
      return invoice.price - this.getInvoiceTotalPaid(invoice);
    },
    openTransactionModal(invoice) {
      this.selectedInvoice = invoice;
      this.showTransactionModal = true;
    },
    handleNewTransaction(newTransaction) {
      // Encontrar a invoice correspondente e adicionar a transação
      const invoiceIndex = this.debitInvoices.findIndex(
        (inv) => inv.id === newTransaction.invoice_id
      );
      if (invoiceIndex !== -1) {
        const invoice = this.debitInvoices[invoiceIndex];
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
    async updateTransaction(fieldName, editedValue, transactionId) {
      const updatedTransaction = await updateField(
        "transactions",
        transactionId,
        fieldName,
        editedValue
      );

      // Atualizar transação localmente
      for (let invoice of this.debitInvoices) {
        if (invoice.transactions) {
          const index = invoice.transactions.findIndex(
            (t) => t.id === transactionId
          );
          if (index !== -1) {
            invoice.transactions[index] = updatedTransaction;
            break;
          }
        }
      }
    }
  },
};
</script>

<style scoped>
/* Estilos específicos se necessário */
</style>
