<template>
  <div class="page-container">
    <PageHeader title="CARTÕES DE CRÉDITO" icon="fa-solid fa-credit-card">
      <template #actions>
        <button type="button" class="btn bg-base-100 text-primary border-0 hover:bg-base-200" @click="openCreateModal">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Novo Cartão
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
          <StatusToggle :active="creditCard.is_active" @toggle="toggleActive(creditCard)" />
        </div>

        <div class="w-1/10 text-center">
          <div class="flex justify-center gap-2">
            <IconButton icon="fa-solid fa-eye" color="info" title="Visualizar" @click="viewCreditCard(creditCard)" />
            <EditIconButton @click="editCreditCard(creditCard)" />
            <DeleteIconButton
              :confirm-message="`Tem certeza que deseja excluir o cartão ${creditCard.name}?`"
              @confirm="deleteCreditCard(creditCard)"
            />
          </div>
        </div>
      </div>

      <EmptyState
        v-if="filteredCreditCards && filteredCreditCards.length === 0"
        text="Nenhum cartão de crédito encontrado"
        icon="fa-solid fa-credit-card"
      />
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

  </div>
</template>

<script>
import { index, destroy, post } from "@/utils/requests/httpUtils";
import CreditCardForm from "@/components/forms/CreditCardForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import EmptyState from "@/components/layout/EmptyState.vue";
import StatusToggle from "@/components/buttons/StatusToggle.vue";
import EditIconButton from "@/components/buttons/EditIconButton.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";
import IconButton from "@/components/buttons/IconButton.vue";

export default {
  name: "CreditCardsList",
  components: {
    EditIconButton,
    DeleteIconButton,
    IconButton,
    StatusToggle,
    EmptyState,
    PageHeader,
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
      isEditing: false,
      selectedCreditCard: null,
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

    async deleteCreditCard(creditCard) {
      try {
        await destroy("credit_cards", creditCard.id);
        this.getCreditCards();
        this.$emit("success", { message: "Cartão excluído com sucesso!" });
      } catch (error) {
        console.error("Erro ao excluir cartão:", error);
        this.$emit("error", {
          message: error.response?.data?.message || "Erro ao excluir cartão"
        });
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

</style>
