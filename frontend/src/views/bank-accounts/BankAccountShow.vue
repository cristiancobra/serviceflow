<template>
  <div class="page-container">
    <PageHeader
      :title="bankAccount?.account_name || 'Carregando...'"
      icon="fa-solid fa-building-columns"
      show-back
      @back="goBack"
    >
      <template #actions>
        <button type="button" class="btn bg-base-100 text-primary border-0 hover:bg-base-200" @click="openEditModal">
          <font-awesome-icon icon="fa-solid fa-edit" />
          Editar
        </button>
      </template>
    </PageHeader>

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
                <StatusToggle :active="bankAccount.is_active" active-label="Ativa" inactive-label="Inativa" readonly />
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
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeEditModal"
    >
      <ModalCard title="Editar Conta Bancária" icon="fa-solid fa-building-columns" size="md" @close="closeEditModal">
        <BankAccountForm
          :bankAccount="bankAccount"
          :isEditing="true"
          @saved="handleSaved"
          @cancel="closeEditModal"
        />
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { show, post } from "@/utils/requests/httpUtils";
import BankAccountForm from "@/components/forms/BankAccountForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import StatusToggle from "@/components/buttons/StatusToggle.vue";

export default {
  name: "BankAccountShow",
  components: {
    StatusToggle,
    PageHeader,
    ModalCard,
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
.loading-state,
.error-state {
  text-align: center;
  padding: 3rem;
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  font-size: 1.125rem;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.card {
  background-color: var(--color-base-100);
  border: 1px solid var(--color-base-300);
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.col-span-2 {
  grid-column: span 2;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-base-content);
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid var(--color-base-300);
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
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
}

.info-value {
  font-size: 1rem;
  color: var(--color-base-content);
}

.type-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
  display: inline-block;
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
  color: var(--color-base-content);
}

.btn-update-balance {
  padding: 0.5rem 1rem;
  border: 1px solid var(--color-base-300);
  border-radius: 0.375rem;
  background-color: var(--color-base-100);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-update-balance:hover {
  background-color: var(--color-base-200);
  border-color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
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
  background-color: var(--color-base-200);
  border-radius: 0.375rem;
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
}

.stat-value {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-base-content);
}

.description-text {
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
  line-height: 1.6;
  margin: 0;
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
