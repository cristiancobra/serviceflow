<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-rotate" class="page-icon" />
        <h1>DESPESAS RECORRENTES</h1>
      </div>
      <div class="page-action">
        <button @click="openCreateModal" class="btn btn-primary">
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
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <ModalCard :title="`${isEditing ? 'Editar' : 'Nova'} Despesa Recorrente`" icon="fa-solid fa-rotate" size="md" @close="closeModal">
        <RecurringExpenseForm
          :recurringExpense="selectedRecurringExpense"
          :isEditing="isEditing"
          @saved="handleSaved"
          @cancel="closeModal"
        />
      </ModalCard>
    </div>

    <!-- Modal de Geração Retroativa (Backfill) -->
    <div
      v-if="showBackfillModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeBackfillModal"
    >
      <ModalCard title="Gerar Faturas Retroativas" icon="fa-solid fa-history" size="sm" @close="closeBackfillModal">
        <p>
          Gerar as faturas de <strong>{{ recurringExpenseToBackfill?.name }}</strong>
          desde <strong>{{ formatDate(recurringExpenseToBackfill?.start_date) }}</strong> até a data abaixo.
        </p>

        <div class="fieldset mt-4">
          <label for="backfill-end-date" class="fieldset-legend">Gerar até (opcional, padrão: mês atual)</label>
          <input
            id="backfill-end-date"
            type="date"
            v-model="backfillEndDate"
            class="input w-full"
          />
        </div>

        <p v-if="backfillError" class="text-error text-sm mt-3">{{ backfillError }}</p>

        <template #footer>
          <button @click="closeBackfillModal" class="btn btn-ghost">
            Cancelar
          </button>
          <button @click="submitBackfill" class="btn btn-primary" :disabled="isBackfilling">
            {{ isBackfilling ? 'Gerando...' : 'Gerar Faturas' }}
          </button>
        </template>
      </ModalCard>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeDeleteModal"
    >
      <ModalCard title="Confirmar Exclusão" icon="fa-solid fa-trash" size="sm" @close="closeDeleteModal">
        <p>Tem certeza que deseja excluir a despesa recorrente <strong>{{ recurringExpenseToDelete?.name }}</strong>?</p>
        <p class="text-sm text-base-content/60 mt-2">As faturas já geradas não serão excluídas, apenas deixará de gerar novas faturas.</p>

        <template #footer>
          <button @click="closeDeleteModal" class="btn btn-ghost">
            Cancelar
          </button>
          <button @click="deleteRecurringExpense" class="btn btn-error">
            Excluir
          </button>
        </template>
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index, destroy, post } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import RecurringExpenseForm from "@/components/forms/RecurringExpenseForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "RecurringExpensesList",
  components: {
    ModalCard,
    RecurringExpenseForm,
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
      isEditing: false,
      isBackfilling: false,
      selectedRecurringExpense: null,
      recurringExpenseToDelete: null,
      recurringExpenseToBackfill: null,
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

    openInvoicesModal(recurringExpense) {
      this.openModal({
        component: "RecurringExpenseInvoicesModal",
        props: { recurringExpense },
      });
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
  border: 1px solid var(--color-base-300);
  border-radius: 0.375rem;
  background-color: var(--color-base-100);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.filter-select:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px color-mix(in oklab, var(--color-primary) 10%, transparent);
}

.list-header {
  display: flex;
  padding: 1rem;
  background-color: var(--color-base-200);
  border-bottom: 2px solid var(--color-base-300);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.list-line {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid var(--color-base-300);
  transition: background-color 0.2s;
}

.list-line:hover {
  background-color: var(--color-base-200);
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
  background-color: color-mix(in oklab, var(--color-success) 15%, var(--color-base-100));
  color: var(--color-success);
}

.status-active:hover {
  background-color: color-mix(in oklab, var(--color-success) 15%, var(--color-base-100));
}

.status-inactive {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  color: var(--color-error);
}

.status-inactive:hover {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
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
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
  text-decoration: none;
}

.btn-view:hover {
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
}

.btn-backfill {
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
}

.btn-backfill:hover {
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
}

.btn-edit {
  background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
  color: var(--color-warning);
}

.btn-edit:hover {
  background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
}

.btn-delete {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  color: var(--color-error);
}

.btn-delete:hover {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
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

</style>
