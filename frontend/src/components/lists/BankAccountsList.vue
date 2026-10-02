<template>
  <div class="page-container">
    <PageHeader title="CONTAS BANCÁRIAS" icon="fa-solid fa-building-columns">
      <template #actions>
        <button @click="openCreateModal" class="btn btn-primary">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Conta
        </button>
      </template>
    </PageHeader>

    <section class="section-container">
      <div class="filters-container">
        <div class="search-container">
          <input
            type="text"
            class="search-input"
            v-model="searchTerm"
            placeholder="Buscar por nome, banco, número da conta..."
          />
        </div>
        
        <div class="filter-container">
          <label for="type-filter" class="filter-label">Tipo:</label>
          <select
            id="type-filter"
            v-model="selectedType"
            @change="applyFilters"
            class="filter-select"
          >
            <option value="">Todos os tipos</option>
            <option
              v-for="(label, value) in typeOptions"
              :key="value"
              :value="value"
            >
              {{ label }}
            </option>
          </select>
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
        <div class="w-2/10 text-left font-bold">Nome da Conta</div>
        <div class="w-2/10 text-center font-bold">Banco</div>
        <div class="w-1/10 text-center font-bold">Agência</div>
        <div class="w-2/10 text-center font-bold">Número da Conta</div>
        <div class="w-1/10 text-center font-bold">Tipo</div>
        <div class="w-1/10 text-center font-bold">Saldo</div>
        <div class="w-1/10 text-center font-bold">Status</div>
        <div class="w-1/10 text-center font-bold">Ações</div>
      </div>

      <div
        v-for="bankAccount in filteredBankAccounts"
        :key="bankAccount.id"
        class="list-line"
      >
        <div class="w-2/10 text-left text-base-content font-semibold">
          {{ bankAccount.account_name }}
        </div>

        <div class="w-2/10 text-center text-base-content">
          {{ bankAccount.bank_name }}
        </div>

        <div class="w-1/10 text-center text-base-content">
          {{ bankAccount.agency || '-' }}
        </div>

        <div class="w-2/10 text-center text-base-content">
          {{ bankAccount.account_number }}
        </div>

        <div class="w-1/10 text-center">
          <span class="type-badge">
            {{ bankAccount.type_label }}
          </span>
        </div>

        <div class="w-1/10 text-center text-base-content font-semibold">
          {{ bankAccount.initial_balance_formatted }}
        </div>
        
        <div class="w-1/10 text-center">
          <StatusToggle :active="bankAccount.is_active" @toggle="toggleActive(bankAccount)" />
        </div>
        
        <div class="w-1/10 text-center">
          <div class="action-buttons">
            <button 
              @click="viewBankAccount(bankAccount)" 
              class="btn-action btn-view"
              title="Visualizar"
            >
              <font-awesome-icon icon="fa-solid fa-eye" />
            </button>
            <button 
              @click="editBankAccount(bankAccount)" 
              class="btn-action btn-edit"
              title="Editar"
            >
              <font-awesome-icon icon="fa-solid fa-edit" />
            </button>
            <button 
              @click="confirmDelete(bankAccount)" 
              class="btn-action btn-delete"
              title="Excluir"
            >
              <font-awesome-icon icon="fa-solid fa-trash" />
            </button>
          </div>
        </div>
      </div>

      <EmptyState
        v-if="filteredBankAccounts && filteredBankAccounts.length === 0"
        text="Nenhuma conta bancária encontrada"
        icon="fa-solid fa-building-columns"
      />
    </section>

    <!-- Modal de Criar/Editar -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <ModalCard :title="`${isEditing ? 'Editar' : 'Nova'} Conta Bancária`" icon="fa-solid fa-building-columns" size="md" @close="closeModal">
        <BankAccountForm
          :bankAccount="selectedBankAccount"
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
        <p>Tem certeza que deseja excluir a conta bancária <strong>{{ bankAccountToDelete?.account_name }}</strong>?</p>
        <p class="text-sm text-base-content/60 mt-2">Esta ação não poderá ser desfeita.</p>

        <template #footer>
          <button @click="closeDeleteModal" class="btn btn-ghost">
            Cancelar
          </button>
          <button @click="deleteBankAccount" class="btn btn-error">
            Excluir
          </button>
        </template>
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { index, destroy, post, get } from "@/utils/requests/httpUtils";
import BankAccountForm from "@/components/forms/BankAccountForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import EmptyState from "@/components/layout/EmptyState.vue";
import StatusToggle from "@/components/buttons/StatusToggle.vue";

export default {
  name: "BankAccountsList",
  components: {
    StatusToggle,
    EmptyState,
    PageHeader,
    ModalCard,
    BankAccountForm,
  },
  data() {
    return {
      searchTerm: "",
      selectedType: "",
      selectedStatus: "",
      bankAccounts: [],
      filteredBankAccounts: [],
      typeOptions: {},
      showModal: false,
      showDeleteModal: false,
      isEditing: false,
      selectedBankAccount: null,
      bankAccountToDelete: null,
    };
  },
  watch: {
    searchTerm() {
      this.applyFilters();
    },
  },
  methods: {
    async getBankAccounts() {
      try {
        const response = await index("bank_accounts");
        
        // A função index() já retorna response.data.data, então response já é o array
        this.bankAccounts = Array.isArray(response) ? response : (response.data || []);
        
        this.applyFilters();
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
        this.$emit("error", { message: "Erro ao carregar contas bancárias" });
      }
    },

    async getTypeOptions() {
      try {
        this.typeOptions = await get("bank_accounts/options/types");
      } catch (error) {
        console.error("Erro ao carregar tipos de conta:", error);
      }
    },

    applyFilters() {
      let filtered = this.bankAccounts;

      // Filtro de busca
      if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(account =>
          account.account_name.toLowerCase().includes(term) ||
          account.bank_name.toLowerCase().includes(term) ||
          account.account_number.toLowerCase().includes(term) ||
          (account.agency && account.agency.toLowerCase().includes(term))
        );
      }

      // Filtro por tipo
      if (this.selectedType) {
        filtered = filtered.filter(account => account.type === this.selectedType);
      }

      // Filtro por status
      if (this.selectedStatus !== "") {
        const isActive = this.selectedStatus === "1";
        filtered = filtered.filter(account => account.is_active === isActive);
      }

      this.filteredBankAccounts = filtered;
    },

    openCreateModal() {
      this.isEditing = false;
      this.selectedBankAccount = null;
      this.showModal = true;
    },

    editBankAccount(bankAccount) {
      this.isEditing = true;
      this.selectedBankAccount = { ...bankAccount };
      this.showModal = true;
    },

    viewBankAccount(bankAccount) {
      this.$router.push({ name: 'bank-accounts-show', params: { id: bankAccount.id } });
    },

    closeModal() {
      this.showModal = false;
      this.selectedBankAccount = null;
      this.isEditing = false;
    },

    handleSaved() {
      this.closeModal();
      this.getBankAccounts();
      this.$emit("success", { 
        message: `Conta bancária ${this.isEditing ? 'atualizada' : 'criada'} com sucesso!` 
      });
    },

    confirmDelete(bankAccount) {
      this.bankAccountToDelete = bankAccount;
      this.showDeleteModal = true;
    },

    closeDeleteModal() {
      this.showDeleteModal = false;
      this.bankAccountToDelete = null;
    },

    async deleteBankAccount() {
      try {
        await destroy("bank_accounts", this.bankAccountToDelete.id);
        this.closeDeleteModal();
        this.getBankAccounts();
        this.$emit("success", { message: "Conta bancária excluída com sucesso!" });
      } catch (error) {
        console.error("Erro ao excluir conta bancária:", error);
        this.$emit("error", { 
          message: error.response?.data?.message || "Erro ao excluir conta bancária" 
        });
        this.closeDeleteModal();
      }
    },

    async toggleActive(bankAccount) {
      try {
        await post(`bank_accounts/${bankAccount.id}/toggle-active`);
        this.getBankAccounts();
        this.$emit("success", { 
          message: `Conta bancária ${bankAccount.is_active ? 'desativada' : 'ativada'} com sucesso!` 
        });
      } catch (error) {
        console.error("Erro ao alterar status:", error);
        this.$emit("error", { message: "Erro ao alterar status da conta" });
      }
    },
  },
  mounted() {
    this.getBankAccounts();
    this.getTypeOptions();
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

.type-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
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

</style>
