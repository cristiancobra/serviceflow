<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-credit-card" class="page-icon" />
        <h1>CARTÕES DE CRÉDITO</h1>
      </div>
      <div class="page-action">
        <button @click="openCreateModal" class="btn-primary">
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
        <div class="w-3/10 text-left text-black font-semibold">
          {{ creditCard.name }}
        </div>

        <div class="w-1/10 text-center text-black">
          {{ creditCard.brand || '-' }}
        </div>

        <div class="w-1/10 text-center text-black">
          {{ creditCard.last_digits ? `**** ${creditCard.last_digits}` : '-' }}
        </div>

        <div class="w-1/10 text-center text-black">
          dia {{ creditCard.closing_day }}
        </div>

        <div class="w-1/10 text-center text-black">
          dia {{ creditCard.due_day }}
        </div>

        <div class="w-2/10 text-center text-black font-semibold">
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
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ isEditing ? 'Editar' : 'Novo' }} Cartão de Crédito</h2>
          <button @click="closeModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <CreditCardForm
          :creditCard="selectedCreditCard"
          :isEditing="isEditing"
          @saved="handleSaved"
          @cancel="closeModal"
        />
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
          <p>Tem certeza que deseja excluir o cartão <strong>{{ creditCardToDelete?.name }}</strong>?</p>
          <p class="text-sm text-gray-600 mt-2">Esta ação não poderá ser desfeita.</p>
        </div>

        <div class="modal-footer">
          <button @click="closeDeleteModal" class="btn-secondary">
            Cancelar
          </button>
          <button @click="deleteCreditCard" class="btn-danger">
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { index, destroy, post } from "@/utils/requests/httpUtils";
import CreditCardForm from "@/components/forms/CreditCardForm.vue";

export default {
  name: "CreditCardsList",
  components: {
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
  color: #374151;
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
}

.btn-view:hover {
  background-color: #bfdbfe;
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
  color: #6b7280;
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
