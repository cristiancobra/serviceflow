<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <button @click="goBack" class="btn-back">
          <font-awesome-icon icon="fa-solid fa-arrow-left" />
        </button>
        <font-awesome-icon icon="fa-solid fa-building-columns" class="page-icon" />
        <h1>{{ bankAccount?.account_name || 'Carregando...' }}</h1>
      </div>
      <div class="page-action">
        <button @click="openEditModal" class="btn-primary">
          <font-awesome-icon icon="fa-solid fa-edit" />
          Editar
        </button>
      </div>
    </div>

    <section class="section-container">
      <div v-if="loading" class="loading-state">
        <p>Carregando...</p>
      </div>

      <div v-else-if="bankAccount" class="details-grid">
        <!-- Informações Principais -->
        <div class="card col-span-2">
          <h2 class="card-title">Informações da Conta</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Nome da Conta:</span>
              <span class="info-value">{{ bankAccount.account_name }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Banco:</span>
              <span class="info-value">{{ bankAccount.bank_name }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Agência:</span>
              <span class="info-value">{{ bankAccount.agency || '-' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Número da Conta:</span>
              <span class="info-value">{{ bankAccount.account_number }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Tipo:</span>
              <span class="info-value">
                <span class="type-badge">{{ bankAccount.type_label }}</span>
              </span>
            </div>
            <div class="info-item">
              <span class="info-label">Status:</span>
              <span class="info-value">
                <span 
                  class="status-badge"
                  :class="bankAccount.is_active ? 'status-active' : 'status-inactive'"
                >
                  {{ bankAccount.is_active ? 'Ativa' : 'Inativa' }}
                </span>
              </span>
            </div>
          </div>
        </div>

        <!-- Saldo -->
        <div class="card">
          <h2 class="card-title">Saldo Atual</h2>
          <div class="balance-display">
            <span class="balance-value">{{ bankAccount.balance_formatted }}</span>
            <button @click="updateBalance" class="btn-update-balance" title="Atualizar saldo">
              <font-awesome-icon icon="fa-solid fa-sync" />
              Atualizar
            </button>
          </div>
        </div>

        <!-- Estatísticas -->
        <div class="card">
          <h2 class="card-title">Estatísticas</h2>
          <div class="stats-grid">
            <div class="stat-item">
              <span class="stat-label">Transações:</span>
              <span class="stat-value">{{ bankAccount.transactions_count || 0 }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Criada em:</span>
              <span class="stat-value">{{ formatDate(bankAccount.created_at) }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Atualizada em:</span>
              <span class="stat-value">{{ formatDate(bankAccount.updated_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Relacionamentos -->
        <div class="card col-span-2" v-if="bankAccount.account">
          <h2 class="card-title">Conta Associada</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Nome:</span>
              <span class="info-value">{{ bankAccount.account.name }}</span>
            </div>
            <div class="info-item" v-if="bankAccount.account.email">
              <span class="info-label">Email:</span>
              <span class="info-value">{{ bankAccount.account.email }}</span>
            </div>
          </div>
        </div>

        <!-- Usuário Responsável -->
        <div class="card col-span-2" v-if="bankAccount.user">
          <h2 class="card-title">Usuário Responsável</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Nome:</span>
              <span class="info-value">{{ bankAccount.user.name }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Email:</span>
              <span class="info-value">{{ bankAccount.user.email }}</span>
            </div>
          </div>
        </div>

        <!-- Descrição -->
        <div class="card col-span-2" v-if="bankAccount.description">
          <h2 class="card-title">Descrição</h2>
          <p class="description-text">{{ bankAccount.description }}</p>
        </div>
      </div>

      <div v-else class="error-state">
        <p>Erro ao carregar conta bancária</p>
      </div>
    </section>

    <!-- Modal de Edição -->
    <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Editar Conta Bancária</h2>
          <button @click="closeEditModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>
        
        <BankAccountForm
          :bankAccount="bankAccount"
          :isEditing="true"
          @saved="handleSaved"
          @cancel="closeEditModal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { show, post } from "@/utils/requests/httpUtils";
import BankAccountForm from "@/components/forms/BankAccountForm.vue";

export default {
  name: "BankAccountShow",
  components: {
    BankAccountForm,
  },
  data() {
    return {
      bankAccount: null,
      loading: true,
      showEditModal: false,
    };
  },
  methods: {
    async getBankAccount() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        this.bankAccount = await show("bank_accounts", id);
      } catch (error) {
        console.error("Erro ao carregar conta bancária:", error);
      } finally {
        this.loading = false;
      }
    },

    async updateBalance() {
      try {
        const response = await post(`bank_accounts/${this.bankAccount.id}/update-balance`);
        this.bankAccount.balance = response.balance;
        this.bankAccount.balance_formatted = response.balance_formatted;
      } catch (error) {
        console.error("Erro ao atualizar saldo:", error);
      }
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    openEditModal() {
      this.showEditModal = true;
    },

    closeEditModal() {
      this.showEditModal = false;
    },

    handleSaved() {
      this.closeEditModal();
      this.getBankAccount();
    },

    goBack() {
      this.$router.push({ name: 'bank-accounts' });
    },
  },
  mounted() {
    this.getBankAccount();
  },
};
</script>

<style scoped>
.btn-back {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  margin-right: 0.5rem;
  color: #6b7280;
  transition: color 0.2s;
}

.btn-back:hover {
  color: #111827;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.125rem;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.card {
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.col-span-2 {
  grid-column: span 2;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #e5e7eb;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
}

.info-value {
  font-size: 1rem;
  color: #111827;
}

.type-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background-color: #dbeafe;
  color: #1e40af;
  display: inline-block;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}

.status-active {
  background-color: #dcfce7;
  color: #166534;
}

.status-inactive {
  background-color: #fecaca;
  color: #991b1b;
}

.balance-display {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
}

.balance-value {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
}

.btn-update-balance {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  color: #374151;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-update-balance:hover {
  background-color: #f3f4f6;
  border-color: #9ca3af;
}

.stats-grid {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background-color: #f9fafb;
  border-radius: 0.375rem;
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
}

.stat-value {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
}

.description-text {
  color: #374151;
  line-height: 1.6;
  margin: 0;
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

.btn-primary {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: var(--primary);
  color: white;
}

.btn-primary:hover {
  opacity: 0.9;
}

@media (max-width: 768px) {
  .details-grid {
    grid-template-columns: 1fr;
  }

  .col-span-2 {
    grid-column: span 1;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
