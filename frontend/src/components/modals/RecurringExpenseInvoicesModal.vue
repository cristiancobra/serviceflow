<template>
  <ModalCard
    :title="`Faturas de ${recurringExpense?.name || ''}`"
    icon="fa-solid fa-receipt"
    size="xl"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <div v-if="isLoadingInvoices" class="empty-state">
      <p>Carregando faturas...</p>
    </div>

    <div v-else-if="invoicesForModal.length === 0" class="empty-state">
      <font-awesome-icon icon="fa-solid fa-receipt" class="empty-icon" />
      <p>Nenhuma fatura gerada ainda</p>
    </div>

    <div v-else class="invoices-table-wrapper">
      <table class="invoices-table">
        <thead>
          <tr>
            <th>Vencimento</th>
            <th>Valor</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <template v-for="invoice in invoicesForModal" :key="invoice.id">
            <tr>
              <td>
                <date-editable-input
                  name="date_due"
                  :modelValue="invoice.date_due"
                  @save="updateModalInvoice('date_due', invoice.id, $event)"
                  class-text="text-sm font-semibold"
                />
              </td>
              <td>
                <money-editable-field
                  name="price"
                  :modelValue="invoice.price"
                  @save="updateModalInvoice('price', invoice.id, $event)"
                />
              </td>
              <td>{{ formatCurrency(invoice.total_paid) }}</td>
              <td>{{ formatCurrency(invoice.balance) }}</td>
              <td>
                <span class="invoice-status" :class="`invoice-status-${invoice.status}`">
                  {{ invoiceStatusLabel(invoice.status) }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button
                    v-if="invoice.transactions && invoice.transactions.length > 0"
                    @click="toggleInvoiceTransactions(invoice.id)"
                    class="btn-action btn-view"
                    title="Ver transações"
                  >
                    <font-awesome-icon icon="fa-solid fa-coins" />
                  </button>
                  <button
                    v-if="invoice.balance > 0"
                    @click="openTransactionModal(invoice)"
                    class="btn-action btn-add-payment"
                    title="Adicionar Pagamento"
                  >
                    <font-awesome-icon icon="fa-solid fa-plus" />
                  </button>
                  <router-link :to="{ name: 'invoiceShow', params: { id: invoice.id } }" class="btn-action btn-view" title="Abrir fatura">
                    <font-awesome-icon icon="fa-solid fa-eye" />
                  </router-link>
                </div>
              </td>
            </tr>
            <tr v-if="isInvoiceExpanded(invoice.id)">
              <td colspan="6" class="transactions-cell">
                <transactions-list-section
                  :transactions="invoice.transactions"
                  :is-debit="true"
                  @update-transaction="
                    (fieldName, transactionId, editedValue) =>
                      updateModalTransaction(fieldName, transactionId, editedValue, invoice.id)
                  "
                  @delete-transaction="(transactionId) => deleteModalTransaction(transactionId, invoice.id)"
                />
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <template #footer>
      <button @click="$emit('close')" class="btn-secondary">
        Fechar
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { mapMutations } from "vuex";
import { destroy, show, updateField } from "@/utils/requests/httpUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import DateEditableInput from "@/components/fields/date/DateEditableInput.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import TransactionsListSection from "@/components/show/TransactionsListSection.vue";

export default {
  name: "RecurringExpenseInvoicesModal",
  components: {
    ModalCard,
    DateEditableInput,
    MoneyEditableField,
    TransactionsListSection,
  },
  props: {
    recurringExpense: {
      type: Object,
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["close"],
  data() {
    return {
      invoicesForModal: [],
      isLoadingInvoices: false,
      expandedInvoiceIds: [],
    };
  },
  methods: {
    ...mapMutations(["openModal"]),

    async loadInvoices() {
      this.isLoadingInvoices = true;
      try {
        const data = await show("recurring_expenses", this.recurringExpense.id);
        this.invoicesForModal = data.invoices || [];
      } catch (error) {
        console.error("Erro ao carregar faturas:", error);
      } finally {
        this.isLoadingInvoices = false;
      }
    },

    formatCurrency(value) {
      return `R$ ${Number(value || 0).toFixed(2).replace(".", ",")}`;
    },

    invoiceStatusLabel(status) {
      const labels = {
        pending: "Pendente",
        partial: "Parcial",
        paid: "Pago",
        overdue: "Vencido",
        cancelled: "Cancelado",
      };
      return labels[status] || status;
    },

    async updateModalInvoice(fieldName, invoiceId, editedValue) {
      try {
        const updatedInvoice = await updateField("invoices", invoiceId, fieldName, editedValue);
        const index = this.invoicesForModal.findIndex((invoice) => invoice.id === invoiceId);
        if (index !== -1) {
          this.invoicesForModal[index] = updatedInvoice;
        }
      } catch (error) {
        console.error("Erro ao atualizar fatura:", error);
      }
    },

    toggleInvoiceTransactions(invoiceId) {
      const index = this.expandedInvoiceIds.indexOf(invoiceId);
      if (index !== -1) {
        this.expandedInvoiceIds.splice(index, 1);
      } else {
        this.expandedInvoiceIds.push(invoiceId);
      }
    },

    isInvoiceExpanded(invoiceId) {
      return this.expandedInvoiceIds.includes(invoiceId);
    },

    async updateModalTransaction(fieldName, transactionId, editedValue, invoiceId) {
      try {
        const updatedTransaction = await updateField("transactions", transactionId, fieldName, editedValue);
        const invoice = this.invoicesForModal.find((inv) => inv.id === invoiceId);
        if (invoice && invoice.transactions) {
          const index = invoice.transactions.findIndex((t) => t.id === transactionId);
          if (index !== -1) {
            invoice.transactions[index] = updatedTransaction;
          }
          invoice.total_paid = invoice.transactions.reduce((sum, t) => sum + Number(t.amount || 0), 0);
          invoice.balance = invoice.price - invoice.total_paid;
        }
      } catch (error) {
        console.error("Erro ao atualizar transação:", error);
      }
    },

    async deleteModalTransaction(transactionId, invoiceId) {
      try {
        await destroy("transactions", transactionId);
        const invoice = this.invoicesForModal.find((inv) => inv.id === invoiceId);
        if (invoice && invoice.transactions) {
          invoice.transactions = invoice.transactions.filter((t) => t.id !== transactionId);
          invoice.total_paid = invoice.transactions.reduce((sum, t) => sum + Number(t.amount || 0), 0);
          invoice.balance = invoice.price - invoice.total_paid;
        }
      } catch (error) {
        console.error("Erro ao excluir transação:", error);
      }
    },

    openTransactionModal(invoice) {
      this.openModal({
        component: "TransactionCreateForm",
        props: { invoice },
        listeners: {
          "new-transaction-event": this.handleNewTransaction,
        },
      });
    },

    handleNewTransaction(newTransaction) {
      const invoice = this.invoicesForModal.find((inv) => inv.id === newTransaction.invoice_id);
      if (invoice) {
        if (!invoice.transactions) {
          invoice.transactions = [];
        }
        invoice.transactions.push(newTransaction);
        invoice.total_paid = invoice.transactions.reduce((sum, t) => sum + Number(t.amount || 0), 0);
        invoice.balance = invoice.price - invoice.total_paid;

        if (!this.expandedInvoiceIds.includes(invoice.id)) {
          this.expandedInvoiceIds.push(invoice.id);
        }
      }
    },
  },
  mounted() {
    this.loadInvoices();
  },
};
</script>

<style scoped>
.invoices-table-wrapper {
  overflow-x: auto;
}

.invoices-table {
  width: 100%;
  border-collapse: collapse;
}

.invoices-table th,
.invoices-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.875rem;
}

.invoices-table th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.invoices-table .action-buttons {
  justify-content: flex-end;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

.btn-action {
  padding: 0.5rem;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-view {
  background-color: #dbeafe;
  color: #1e40af;
  text-decoration: none;
}

.btn-view:hover {
  background-color: #bfdbfe;
}

.btn-add-payment {
  background-color: #dcfce7;
  color: #166534;
}

.btn-add-payment:hover {
  background-color: #bbf7d0;
}

.invoice-status {
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.invoice-status-pending {
  background-color: #fef3c7;
  color: #92400e;
}

.invoice-status-partial {
  background-color: #dbeafe;
  color: #1e40af;
}

.invoice-status-paid {
  background-color: #dcfce7;
  color: #166534;
}

.invoice-status-overdue {
  background-color: #fecaca;
  color: #991b1b;
}

.invoice-status-cancelled {
  background-color: #e5e7eb;
  color: #374151;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state p {
  font-size: 1.125rem;
}

.btn-secondary {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  background-color: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}
</style>
