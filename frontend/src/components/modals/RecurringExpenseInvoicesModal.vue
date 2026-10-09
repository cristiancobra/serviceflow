<template>
  <ModalCard
    :title="`Faturas de ${recurringExpense?.name || ''}`"
    icon="fa-solid fa-receipt"
    size="xl"
    :compact="compact"
    compact-size="max-w-3xl"
    @close="$emit('close')"
  >
    <EmptyState v-if="isLoadingInvoices" text="Carregando faturas..." />

    <EmptyState
      v-else-if="invoicesForModal.length === 0"
      text="Nenhuma fatura gerada ainda"
      icon="fa-solid fa-receipt"
    />

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
                <div class="flex justify-end gap-2">
                  <IconButton
                    v-if="invoice.transactions && invoice.transactions.length > 0"
                    icon="fa-solid fa-coins"
                    color="info"
                    title="Ver transações"
                    @click="toggleInvoiceTransactions(invoice.id)"
                  />
                  <IconButton
                    v-if="invoice.balance > 0"
                    icon="fa-solid fa-plus"
                    color="success"
                    title="Adicionar Pagamento"
                    @click="openTransactionModal(invoice)"
                  />
                  <IconButton icon="fa-solid fa-eye" color="info" title="Abrir fatura" @click="openInvoiceModal(invoice)" />
                </div>
              </td>
            </tr>
            <tr v-if="isInvoiceExpanded(invoice.id)">
              <td colspan="6" class="transactions-cell">
                <transactions-list-section
                  :transactions="invoice.transactions"
                  :is-debit="true"
                  @edit-transaction="(transactionId) => openTransactionEditModal(invoice, transactionId)"
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
import { show, updateField } from "@/utils/requests/httpUtils";
import ModalCard from "@/components/modals/ModalCard.vue";
import DateEditableInput from "@/components/fields/date/DateEditableInput.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import TransactionsListSection from "@/components/show/TransactionsListSection.vue";
import IconButton from "@/components/buttons/IconButton.vue";
import EmptyState from "@/components/layout/EmptyState.vue";

export default {
  name: "RecurringExpenseInvoicesModal",
  components: {
    EmptyState,
    ModalCard,
    DateEditableInput,
    MoneyEditableField,
    TransactionsListSection,
    IconButton,
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

    openInvoiceModal(invoice) {
      this.openModal({
        component: "InvoiceDetailModal",
        props: { invoiceId: invoice.id },
        listeners: {
          "invoice-updated": this.loadInvoices,
          "invoice-deleted": this.loadInvoices,
        },
        id: `invoice-${invoice.id}`,
      });
    },
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

    // Espelha Invoice::calculateStatus() do backend, para os casos em que a
    // resposta da API não devolve a fatura atualizada (ex: exclusão de transação).
    recalcInvoiceStatus(invoice) {
      if (invoice.status === "cancelled") return;

      const totalPaid = Number(invoice.total_paid || 0);
      const price = Number(invoice.price || 0);

      if (price > 0 && totalPaid >= price) {
        invoice.status = "paid";
        return;
      }

      if (invoice.date_due && new Date() > new Date(`${invoice.date_due}T00:00:00`)) {
        invoice.status = "overdue";
        return;
      }

      invoice.status = totalPaid > 0 ? "partial" : "pending";
    },

    applyInvoiceUpdate(invoice, updatedInvoice) {
      if (!updatedInvoice) {
        this.recalcInvoiceStatus(invoice);
        return;
      }
      invoice.total_paid = updatedInvoice.total_paid;
      invoice.balance = updatedInvoice.balance;
      invoice.status = updatedInvoice.status;
    },

    removeTransaction(invoice, transactionId) {
      invoice.transactions = invoice.transactions.filter((t) => t.id !== transactionId);
      invoice.total_paid = invoice.transactions.reduce((sum, t) => sum + Number(t.amount || 0), 0);
      invoice.balance = invoice.price - invoice.total_paid;
      this.recalcInvoiceStatus(invoice);
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

    openTransactionEditModal(invoice, transactionId) {
      this.openModal({
        component: "TransactionCreateForm",
        props: { invoice, transactionId },
        listeners: {
          "transaction-updated": (updatedTransaction) => {
            const index = invoice.transactions.findIndex((t) => t.id === updatedTransaction.id);
            if (index !== -1) {
              invoice.transactions[index] = updatedTransaction;
            }
            this.applyInvoiceUpdate(invoice, updatedTransaction.invoice);
          },
          "transaction-deleted": (transactionId) => this.removeTransaction(invoice, transactionId),
        },
        id: `transaction-edit-${transactionId}`,
      });
    },

    handleNewTransaction(newTransaction) {
      const invoice = this.invoicesForModal.find((inv) => inv.id === newTransaction.invoice_id);
      if (invoice) {
        if (!invoice.transactions) {
          invoice.transactions = [];
        }
        invoice.transactions.push(newTransaction);
        this.applyInvoiceUpdate(invoice, newTransaction.invoice);

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
  border-bottom: 1px solid var(--color-base-300);
  font-size: 0.875rem;
}

.invoices-table th {
  background-color: var(--color-base-200);
  font-weight: 600;
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.invoice-status {
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.invoice-status-pending {
  background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
  color: var(--color-warning);
}

.invoice-status-partial {
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
}

.invoice-status-paid {
  background-color: color-mix(in oklab, var(--color-success) 15%, var(--color-base-100));
  color: var(--color-success);
}

.invoice-status-overdue {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  color: var(--color-error);
}

.invoice-status-cancelled {
  background-color: var(--color-base-300);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.btn-secondary {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  background-color: var(--color-base-200);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.btn-secondary:hover {
  background-color: var(--color-base-300);
}
</style>
