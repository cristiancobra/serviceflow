<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-rotate" class="page-icon" />
        <h1>DESPESAS RECORRENTES</h1>
      </div>
      <div class="page-action">
        <button @click="openCreateModal" class="btn-primary">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Despesa Recorrente
        </button>
      </div>
    </div>

    <section class="section-container">
      <div class="filters-container">
        <div class="search-container">
          <input
            type="text"
            class="search-input"
            v-model="searchTerm"
            placeholder="Buscar por nome..."
          />
        </div>

        <div class="filter-container">
          <label for="status-filter" class="filter-label">Status:</label>
          <select
            id="status-filter"
            v-model="selectedStatus"
            @change="applyFilters"
            class="filter-select"
          >
            <option value="">Todos</option>
            <option value="1">Ativas</option>
            <option value="0">Inativas</option>
          </select>
        </div>

        <div class="filter-container">
          <label for="category-filter" class="filter-label">Categoria:</label>
          <select
            id="category-filter"
            v-model="selectedCategory"
            @change="applyFilters"
            class="filter-select"
          >
            <option value="">Todas</option>
            <option value="fixed">Fixa</option>
            <option value="variable">Variável</option>
          </select>
        </div>
      </div>

      <div class="list-header">
        <div class="w-3/10 text-left font-bold">Nome</div>
        <div class="w-1/10 text-center font-bold">Categoria</div>
        <div class="w-2/10 text-center font-bold">Valor</div>
        <div class="w-1/10 text-center font-bold">Vencimento</div>
        <div class="w-2/10 text-center font-bold">Faturas Geradas</div>
        <div class="w-1/10 text-center font-bold">Status</div>
        <div class="w-1/10 text-center font-bold">Ações</div>
      </div>

      <div
        v-for="recurringExpense in filteredRecurringExpenses"
        :key="recurringExpense.id"
        class="list-line"
      >
        <div class="w-3/10 text-left text-base-content font-semibold">
          {{ recurringExpense.name }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          {{ recurringExpense.category === 'variable' ? 'Variável' : 'Fixa' }}
        </div>

        <div class="w-2/10 text-center text-base-content font-semibold">
          {{ recurringExpense.amount_formatted }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          dia {{ recurringExpense.due_day }}
        </div>

        <div class="w-2/10 text-center text-base-content">
          {{ recurringExpense.invoices_count }}
        </div>

        <div class="w-1/10 text-center">
          <button
            @click="toggleActive(recurringExpense)"
            class="status-toggle"
            :class="recurringExpense.is_active ? 'status-active' : 'status-inactive'"
          >
            {{ recurringExpense.is_active ? 'Ativa' : 'Inativa' }}
          </button>
        </div>

        <div class="w-1/10 text-center">
          <div class="action-buttons">
            <button
              @click="openInvoicesModal(recurringExpense)"
              class="btn-action btn-view"
              title="Ver faturas"
            >
              <font-awesome-icon icon="fa-solid fa-receipt" />
            </button>
            <button
              v-if="needsBackfill(recurringExpense)"
              @click="openBackfillModal(recurringExpense)"
              class="btn-action btn-backfill"
              title="Gerar faturas retroativas"
            >
              <font-awesome-icon icon="fa-solid fa-history" />
            </button>
            <button
              @click="editRecurringExpense(recurringExpense)"
              class="btn-action btn-edit"
              title="Editar"
            >
              <font-awesome-icon icon="fa-solid fa-edit" />
            </button>
            <button
              @click="confirmDelete(recurringExpense)"
              class="btn-action btn-delete"
              title="Excluir"
            >
              <font-awesome-icon icon="fa-solid fa-trash" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredRecurringExpenses && filteredRecurringExpenses.length === 0" class="empty-state">
        <font-awesome-icon icon="fa-solid fa-rotate" class="empty-icon" />
        <p>Nenhuma despesa recorrente encontrada</p>
      </div>
    </section>

    <!-- Modal de Criar/Editar -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ isEditing ? 'Editar' : 'Nova' }} Despesa Recorrente</h2>
          <button @click="closeModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <RecurringExpenseForm
          :recurringExpense="selectedRecurringExpense"
          :isEditing="isEditing"
          @saved="handleSaved"
          @cancel="closeModal"
        />
      </div>
    </div>

    <!-- Modal de Geração Retroativa (Backfill) -->
    <div v-if="showBackfillModal" class="modal-overlay" @click="closeBackfillModal">
      <div class="modal-content modal-small" @click.stop>
        <div class="modal-header">
          <h2>Gerar Faturas Retroativas</h2>
          <button @click="closeBackfillModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div class="modal-body">
          <p>
            Gerar as faturas de <strong>{{ recurringExpenseToBackfill?.name }}</strong>
            desde <strong>{{ formatDate(recurringExpenseToBackfill?.start_date) }}</strong> até a data abaixo.
          </p>

          <div class="form-group" style="margin-top: 1rem;">
            <label for="backfill-end-date" class="filter-label">Gerar até (opcional, padrão: mês atual)</label>
            <input
              id="backfill-end-date"
              type="date"
              v-model="backfillEndDate"
              class="filter-select"
              style="width: 100%;"
            />
          </div>

          <p v-if="backfillError" class="error-message" style="margin-top: 0.75rem;">{{ backfillError }}</p>
        </div>

        <div class="modal-footer">
          <button @click="closeBackfillModal" class="btn-secondary">
            Cancelar
          </button>
          <button @click="submitBackfill" class="btn-primary" :disabled="isBackfilling">
            {{ isBackfilling ? 'Gerando...' : 'Gerar Faturas' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Faturas Relacionadas -->
    <div v-if="showInvoicesModal" class="modal-overlay" @click="closeInvoicesModal">
      <div class="modal-content modal-large" @click.stop>
        <div class="modal-header">
          <h2>Faturas de {{ recurringExpenseForInvoices?.name }}</h2>
          <button @click="closeInvoicesModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div class="modal-body">
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
        </div>

        <div class="modal-footer">
          <button @click="closeInvoicesModal" class="btn-secondary">
            Fechar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
      <div class="modal-content modal-small" @click.stop>
        <div class="modal-header">
          <h2>Confirmar Exclusão</h2>
          <button @click="closeDeleteModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <div class="modal-body">
          <p>Tem certeza que deseja excluir a despesa recorrente <strong>{{ recurringExpenseToDelete?.name }}</strong>?</p>
          <p class="text-sm text-gray-600 mt-2">As faturas já geradas não serão excluídas, apenas deixará de gerar novas faturas.</p>
        </div>

        <div class="modal-footer">
          <button @click="closeDeleteModal" class="btn-secondary">
            Cancelar
          </button>
          <button @click="deleteRecurringExpense" class="btn-danger">
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index, destroy, post, show, updateField } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import RecurringExpenseForm from "@/components/forms/RecurringExpenseForm.vue";
import DateEditableInput from "@/components/fields/date/DateEditableInput.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import TransactionsListSection from "@/components/show/TransactionsListSection.vue";

export default {
  name: "RecurringExpensesList",
  components: {
    RecurringExpenseForm,
    DateEditableInput,
    MoneyEditableField,
    TransactionsListSection,
  },
  data() {
    return {
      searchTerm: "",
      selectedStatus: "",
      selectedCategory: "",
      recurringExpenses: [],
      filteredRecurringExpenses: [],
      showModal: false,
      showDeleteModal: false,
      showBackfillModal: false,
      showInvoicesModal: false,
      isEditing: false,
      isBackfilling: false,
      isLoadingInvoices: false,
      selectedRecurringExpense: null,
      recurringExpenseToDelete: null,
      recurringExpenseToBackfill: null,
      recurringExpenseForInvoices: null,
      invoicesForModal: [],
      expandedInvoiceIds: [],
      backfillEndDate: "",
      backfillError: "",
    };
  },
  watch: {
    searchTerm() {
      this.applyFilters();
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    async getRecurringExpenses() {
      try {
        const response = await index("recurring_expenses");
        this.recurringExpenses = Array.isArray(response) ? response : (response.data || []);
        this.applyFilters();
      } catch (error) {
        console.error("Erro ao carregar despesas recorrentes:", error);
        this.$emit("error", { message: "Erro ao carregar despesas recorrentes" });
      }
    },

    applyFilters() {
      let filtered = this.recurringExpenses;

      if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(item => item.name.toLowerCase().includes(term));
      }

      if (this.selectedStatus !== "") {
        const isActive = this.selectedStatus === "1";
        filtered = filtered.filter(item => item.is_active === isActive);
      }

      if (this.selectedCategory !== "") {
        filtered = filtered.filter(item => item.category === this.selectedCategory);
      }

      this.filteredRecurringExpenses = filtered;
    },

    openCreateModal() {
      this.isEditing = false;
      this.selectedRecurringExpense = null;
      this.showModal = true;
    },

    editRecurringExpense(recurringExpense) {
      this.isEditing = true;
      this.selectedRecurringExpense = { ...recurringExpense };
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.selectedRecurringExpense = null;
      this.isEditing = false;
    },

    handleSaved() {
      this.closeModal();
      this.getRecurringExpenses();
      this.$emit("success", {
        message: `Despesa recorrente ${this.isEditing ? 'atualizada' : 'criada'} com sucesso!`
      });
    },

    confirmDelete(recurringExpense) {
      this.recurringExpenseToDelete = recurringExpense;
      this.showDeleteModal = true;
    },

    closeDeleteModal() {
      this.showDeleteModal = false;
      this.recurringExpenseToDelete = null;
    },

    async deleteRecurringExpense() {
      try {
        await destroy("recurring_expenses", this.recurringExpenseToDelete.id);
        this.closeDeleteModal();
        this.getRecurringExpenses();
        this.$emit("success", { message: "Despesa recorrente excluída com sucesso!" });
      } catch (error) {
        console.error("Erro ao excluir despesa recorrente:", error);
        this.$emit("error", {
          message: error.response?.data?.message || "Erro ao excluir despesa recorrente"
        });
        this.closeDeleteModal();
      }
    },

    async toggleActive(recurringExpense) {
      try {
        await post(`recurring_expenses/${recurringExpense.id}/toggle-active`);
        this.getRecurringExpenses();
        this.$emit("success", {
          message: `Despesa recorrente ${recurringExpense.is_active ? 'desativada' : 'ativada'} com sucesso!`
        });
      } catch (error) {
        console.error("Erro ao alterar status:", error);
        this.$emit("error", { message: "Erro ao alterar status da despesa recorrente" });
      }
    },

    formatDateBr,

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

    async openInvoicesModal(recurringExpense) {
      this.recurringExpenseForInvoices = recurringExpense;
      this.showInvoicesModal = true;
      this.isLoadingInvoices = true;
      this.invoicesForModal = [];
      this.expandedInvoiceIds = [];

      try {
        const data = await show("recurring_expenses", recurringExpense.id);
        this.invoicesForModal = data.invoices || [];
      } catch (error) {
        console.error("Erro ao carregar faturas:", error);
        this.$emit("error", { message: "Erro ao carregar faturas da despesa recorrente" });
      } finally {
        this.isLoadingInvoices = false;
      }
    },

    closeInvoicesModal() {
      this.showInvoicesModal = false;
      this.recurringExpenseForInvoices = null;
      this.invoicesForModal = [];
      this.expandedInvoiceIds = [];
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
        this.$emit("error", { message: "Erro ao atualizar fatura" });
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
        this.$emit("error", { message: "Erro ao atualizar transação" });
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
        this.$emit("error", { message: "Erro ao excluir transação" });
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

    needsBackfill(recurringExpense) {
      if ((recurringExpense.invoices_count || 0) > 0) return false;
      if (!recurringExpense.start_date) return false;

      const oneMonthAgo = new Date();
      oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);

      return new Date(recurringExpense.start_date) < oneMonthAgo;
    },

    formatDate(dateString) {
      if (!dateString) return "";
      const [year, month, day] = dateString.split("-");
      return `${day}/${month}/${year}`;
    },

    openBackfillModal(recurringExpense) {
      this.recurringExpenseToBackfill = recurringExpense;
      this.backfillEndDate = "";
      this.backfillError = "";
      this.showBackfillModal = true;
    },

    closeBackfillModal() {
      this.showBackfillModal = false;
      this.recurringExpenseToBackfill = null;
      this.backfillEndDate = "";
      this.backfillError = "";
    },

    async submitBackfill() {
      this.isBackfilling = true;
      this.backfillError = "";

      try {
        const response = await post(
          `recurring_expenses/${this.recurringExpenseToBackfill.id}/backfill`,
          { end_date: this.backfillEndDate || null }
        );
        this.closeBackfillModal();
        this.getRecurringExpenses();
        this.$emit("success", { message: response?.message || "Faturas geradas com sucesso!" });
      } catch (error) {
        console.error("Erro ao gerar faturas retroativas:", error);
        this.backfillError = error.response?.data?.message || "Erro ao gerar faturas retroativas";
      } finally {
        this.isBackfilling = false;
      }
    },
  },
  mounted() {
    this.getRecurringExpenses();
  },
};
</script>

<style scoped>
.filters-container {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  align-items: end;
  flex-wrap: wrap;
}

.search-container {
  flex: 1;
  min-width: 250px;
}

.filter-container {
  display: flex;
  flex-direction: column;
  min-width: 150px;
}

.filter-label {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: var(--color-base-content);
  font-size: 0.875rem;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  color: #374151;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.list-header {
  display: flex;
  padding: 1rem;
  background-color: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
  color: #374151;
}

.list-line {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.2s;
}

.list-line:hover {
  background-color: #f9fafb;
}

.status-toggle {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.status-active {
  background-color: #dcfce7;
  color: #166534;
}

.status-active:hover {
  background-color: #bbf7d0;
}

.status-inactive {
  background-color: #fecaca;
  color: #991b1b;
}

.status-inactive:hover {
  background-color: #fca5a5;
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

.invoices-table-wrapper {
  overflow-x: auto;
}

.invoices-table .action-buttons {
  justify-content: flex-end;
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

.btn-backfill {
  background-color: #dbeafe;
  color: #1e40af;
}

.btn-backfill:hover {
  background-color: #bfdbfe;
}

.btn-add-payment {
  background-color: #dcfce7;
  color: #166534;
}

.btn-add-payment:hover {
  background-color: #bbf7d0;
}

.btn-edit {
  background-color: #fef3c7;
  color: #92400e;
}

.btn-edit:hover {
  background-color: #fde68a;
}

.btn-delete {
  background-color: #fecaca;
  color: #991b1b;
}

.btn-delete:hover {
  background-color: #fca5a5;
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

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-small {
  max-width: 500px;
}

.modal-large {
  max-width: 1000px;
  max-height: 98vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.375rem;
}

.btn-close:hover {
  background-color: #f3f4f6;
  color: #111827;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary,
.btn-secondary,
.btn-danger {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
}

.btn-primary:hover {
  opacity: 0.9;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

.btn-danger {
  background-color: #ef4444;
  color: white;
}

.btn-danger:hover {
  background-color: #dc2626;
}
</style>
