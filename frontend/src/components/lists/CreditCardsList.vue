<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-credit-card" class="page-icon" />
        <h1>CARTÕES DE CRÉDITO</h1>
      </div>
      <div class="page-action">
        <button @click="openCreateModal" class="btn btn-primary">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Novo Cartão
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
            placeholder="Buscar por nome, bandeira..."
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
            <option value="1">Ativos</option>
            <option value="0">Inativos</option>
          </select>
        </div>
      </div>

      <div class="list-header">
        <div class="w-3/10 text-left font-bold">Nome</div>
        <div class="w-1/10 text-center font-bold">Bandeira</div>
        <div class="w-1/10 text-center font-bold">Final</div>
        <div class="w-1/10 text-center font-bold">Fechamento</div>
        <div class="w-1/10 text-center font-bold">Vencimento</div>
        <div class="w-2/10 text-center font-bold">Limite</div>
        <div class="w-1/10 text-center font-bold">Status</div>
        <div class="w-1/10 text-center font-bold">Ações</div>
      </div>

      <div
        v-for="creditCard in filteredCreditCards"
        :key="creditCard.id"
        class="list-line"
      >
        <div class="w-3/10 text-left text-base-content font-semibold">
          {{ creditCard.name }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          {{ creditCard.brand || '-' }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          {{ creditCard.last_digits ? `**** ${creditCard.last_digits}` : '-' }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          dia {{ creditCard.closing_day }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          dia {{ creditCard.due_day }}
        </div>

        <div class="w-2/10 text-center text-base-content font-semibold">
          {{ creditCard.credit_limit_formatted }}
        </div>

        <div class="w-1/10 text-center">
          <button
            @click="toggleActive(creditCard)"
            class="status-toggle"
            :class="creditCard.is_active ? 'status-active' : 'status-inactive'"
          >
            {{ creditCard.is_active ? 'Ativo' : 'Inativo' }}
          </button>
        </div>

        <div class="w-1/10 text-center">
          <div class="action-buttons">
            <button
              @click="viewCreditCard(creditCard)"
              class="btn-action btn-view"
              title="Visualizar"
            >
              <font-awesome-icon icon="fa-solid fa-eye" />
            </button>
            <button
              @click="editCreditCard(creditCard)"
              class="btn-action btn-edit"
              title="Editar"
            >
              <font-awesome-icon icon="fa-solid fa-edit" />
            </button>
            <button
              @click="confirmDelete(creditCard)"
              class="btn-action btn-delete"
              title="Excluir"
            >
              <font-awesome-icon icon="fa-solid fa-trash" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredCreditCards && filteredCreditCards.length === 0" class="empty-state">
        <font-awesome-icon icon="fa-solid fa-credit-card" class="empty-icon" />
        <p>Nenhum cartão de crédito encontrado</p>
      </div>
    </section>

    <!-- Modal de Criar/Editar -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <ModalCard :title="`${isEditing ? 'Editar' : 'Novo'} Cartão de Crédito`" icon="fa-solid fa-credit-card" size="md" @close="closeModal">
        <CreditCardForm
          :creditCard="selectedCreditCard"
          :isEditing="isEditing"
          @saved="handleSaved"
          @cancel="closeModal"
        />
      </ModalCard>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeDeleteModal"
    >
      <ModalCard title="Confirmar Exclusão" icon="fa-solid fa-trash" size="sm" @close="closeDeleteModal">
        <p>Tem certeza que deseja excluir o cartão <strong>{{ creditCardToDelete?.name }}</strong>?</p>
        <p class="text-sm text-base-content/60 mt-2">Esta ação não poderá ser desfeita.</p>

        <template #footer>
          <button @click="closeDeleteModal" class="btn btn-ghost">
            Cancelar
          </button>
          <button @click="deleteCreditCard" class="btn btn-error">
            Excluir
          </button>
        </template>
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { index, destroy, post } from "@/utils/requests/httpUtils";
import CreditCardForm from "@/components/forms/CreditCardForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "CreditCardsList",
  components: {
    ModalCard,
    CreditCardForm,
  },
  data() {
    return {
      searchTerm: "",
      selectedStatus: "",
      creditCards: [],
      filteredCreditCards: [],
      showModal: false,
      showDeleteModal: false,
      isEditing: false,
      selectedCreditCard: null,
      creditCardToDelete: null,
    };
  },
  watch: {
    searchTerm() {
      this.applyFilters();
    },
  },
  methods: {
    async getCreditCards() {
      try {
        const response = await index("credit_cards");
        this.creditCards = Array.isArray(response) ? response : (response.data || []);
        this.applyFilters();
      } catch (error) {
        console.error("Erro ao carregar cartões de crédito:", error);
        this.$emit("error", { message: "Erro ao carregar cartões de crédito" });
      }
    },

    applyFilters() {
      let filtered = this.creditCards;

      if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(card =>
          card.name.toLowerCase().includes(term) ||
          (card.brand && card.brand.toLowerCase().includes(term))
        );
      }

      if (this.selectedStatus !== "") {
        const isActive = this.selectedStatus === "1";
        filtered = filtered.filter(card => card.is_active === isActive);
      }

      this.filteredCreditCards = filtered;
    },

    openCreateModal() {
      this.isEditing = false;
      this.selectedCreditCard = null;
      this.showModal = true;
    },

    editCreditCard(creditCard) {
      this.isEditing = true;
      this.selectedCreditCard = { ...creditCard };
      this.showModal = true;
    },

    viewCreditCard(creditCard) {
      this.$router.push({ name: 'credit-cards-show', params: { id: creditCard.id } });
    },

    closeModal() {
      this.showModal = false;
      this.selectedCreditCard = null;
      this.isEditing = false;
    },

    handleSaved() {
      this.closeModal();
      this.getCreditCards();
      this.$emit("success", {
        message: `Cartão ${this.isEditing ? 'atualizado' : 'criado'} com sucesso!`
      });
    },

    confirmDelete(creditCard) {
      this.creditCardToDelete = creditCard;
      this.showDeleteModal = true;
    },

    closeDeleteModal() {
      this.showDeleteModal = false;
      this.creditCardToDelete = null;
    },

    async deleteCreditCard() {
      try {
        await destroy("credit_cards", this.creditCardToDelete.id);
        this.closeDeleteModal();
        this.getCreditCards();
        this.$emit("success", { message: "Cartão excluído com sucesso!" });
      } catch (error) {
        console.error("Erro ao excluir cartão:", error);
        this.$emit("error", {
          message: error.response?.data?.message || "Erro ao excluir cartão"
        });
        this.closeDeleteModal();
      }
    },

    async toggleActive(creditCard) {
      try {
        await post(`credit_cards/${creditCard.id}/toggle-active`);
        this.getCreditCards();
        this.$emit("success", {
          message: `Cartão ${creditCard.is_active ? 'desativado' : 'ativado'} com sucesso!`
        });
      } catch (error) {
        console.error("Erro ao alterar status:", error);
        this.$emit("error", { message: "Erro ao alterar status do cartão" });
      }
    },
  },
  mounted() {
    this.getCreditCards();
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
}

.btn-view:hover {
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
