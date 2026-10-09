<template>
  <div class="page-container">
    <PageHeader title="DESPESAS RECORRENTES" icon="fa-solid fa-rotate">
      <template #actions>
        <button type="button" class="btn bg-base-100 text-primary border-0 hover:bg-base-200" @click="openCreateModal">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Despesa Recorrente
        </button>
      </template>
    </PageHeader>

    <section class="mt-8 mb-20 px-8">
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
          <StatusToggle
            :active="recurringExpense.is_active"
            active-label="Ativa"
            inactive-label="Inativa"
            @toggle="toggleActive(recurringExpense)"
          />
        </div>

        <div class="w-1/10 text-center">
          <div class="flex justify-center gap-2">
            <IconButton icon="fa-solid fa-receipt" color="info" title="Ver faturas" @click="openInvoicesModal(recurringExpense)" />
            <IconButton v-if="needsBackfill(recurringExpense)" icon="fa-solid fa-history" color="info" title="Gerar faturas retroativas" @click="openBackfillModal(recurringExpense)" />
            <EditIconButton @click="editRecurringExpense(recurringExpense)" />
            <DeleteIconButton
              :confirm-message="`Tem certeza que deseja excluir a despesa recorrente ${recurringExpense.name}?`"
              @confirm="deleteRecurringExpense(recurringExpense)"
            />
          </div>
        </div>
      </div>

      <EmptyState
        v-if="filteredRecurringExpenses && filteredRecurringExpenses.length === 0"
        text="Nenhuma despesa recorrente encontrada"
        icon="fa-solid fa-rotate"
      />
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

  </div>
</template>

<script>
import { mapMutations } from "vuex";
import { index, destroy, post } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import RecurringExpenseForm from "@/components/forms/RecurringExpenseForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import EmptyState from "@/components/layout/EmptyState.vue";
import StatusToggle from "@/components/buttons/StatusToggle.vue";
import EditIconButton from "@/components/buttons/EditIconButton.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";
import IconButton from "@/components/buttons/IconButton.vue";

export default {
  name: "RecurringExpensesList",
  components: {
    EditIconButton,
    DeleteIconButton,
    IconButton,
    StatusToggle,
    EmptyState,
    PageHeader,
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
      showBackfillModal: false,
      isEditing: false,
      isBackfilling: false,
      selectedRecurringExpense: null,
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

    async deleteRecurringExpense(recurringExpense) {
      try {
        await destroy("recurring_expenses", recurringExpense.id);
        this.getRecurringExpenses();
        this.$emit("success", { message: "Despesa recorrente excluída com sucesso!" });
      } catch (error) {
        console.error("Erro ao excluir despesa recorrente:", error);
        this.$emit("error", {
          message: error.response?.data?.message || "Erro ao excluir despesa recorrente"
        });
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

</style>
