<template>
  <div class="page-container">
    <PageHeader
      :title="creditCard?.name || 'Carregando...'"
      icon="fa-solid fa-credit-card"
      show-back
      @back="goBack"
    >
      <template #actions>
        <button @click="openChargeModal" class="btn btn-primary">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Compra
        </button>
        <button @click="openEditModal" class="btn btn-secondary">
          <font-awesome-icon icon="fa-solid fa-edit" />
          Editar
        </button>
      </template>
    </PageHeader>

    <section class="section-container">
      <div v-if="loading" class="loading-state">
        <p>Carregando...</p>
      </div>

      <div v-else-if="creditCard" class="details-grid">
        <!-- Informações Principais -->
        <div class="card col-span-2">
          <h2 class="card-title">Informações do Cartão</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Nome:</span>
              <span class="info-value">{{ creditCard.name }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Bandeira:</span>
              <span class="info-value">{{ creditCard.brand || '-' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Final:</span>
              <span class="info-value">{{ creditCard.last_digits ? `**** ${creditCard.last_digits}` : '-' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Limite:</span>
              <span class="info-value">{{ creditCard.credit_limit_formatted }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Fechamento:</span>
              <span class="info-value">dia {{ creditCard.closing_day }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Vencimento:</span>
              <span class="info-value">dia {{ creditCard.due_day }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Status:</span>
              <span class="info-value">
                <StatusToggle :active="creditCard.is_active" readonly />
              </span>
            </div>
            <div class="info-item" v-if="creditCard.default_bank_account">
              <span class="info-label">Conta padrão p/ pagamento:</span>
              <span class="info-value">{{ creditCard.default_bank_account.account_name }}</span>
            </div>
          </div>
        </div>

        <!-- Descrição -->
        <div class="card col-span-2" v-if="creditCard.description">
          <h2 class="card-title">Descrição</h2>
          <p class="description-text">{{ creditCard.description }}</p>
        </div>

        <!-- Faturas -->
        <div class="card col-span-2">
          <h2 class="card-title">Faturas</h2>

          <div class="list-header">
            <div class="w-2/10 text-left font-bold">Competência</div>
            <div class="w-2/10 text-center font-bold">Fechamento</div>
            <div class="w-2/10 text-center font-bold">Vencimento</div>
            <div class="w-2/10 text-center font-bold">Total</div>
            <div class="w-2/10 text-center font-bold">Status</div>
          </div>

          <div
            v-for="invoice in invoices"
            :key="invoice.id"
            class="list-line clickable"
            @click="viewInvoice(invoice)"
          >
            <div class="w-2/10 text-left text-base-content font-semibold">
              {{ invoice.reference_label }}
            </div>
            <div class="w-2/10 text-center text-base-content">
              {{ formatDate(invoice.closing_date) }}
            </div>
            <div class="w-2/10 text-center text-base-content">
              {{ formatDate(invoice.due_date) }}
            </div>
            <div class="w-2/10 text-center text-base-content font-semibold">
              {{ invoice.total_amount_formatted }}
            </div>
            <div class="w-2/10 text-center">
              <span class="status-badge" :class="statusClass(invoice.status)">
                {{ invoice.status_label }}
              </span>
            </div>
          </div>

          <EmptyState
            v-if="invoices.length === 0"
            text="Nenhuma fatura ainda. Lance uma compra para começar."
          />
        </div>
      </div>

      <div v-else class="error-state">
        <p>Erro ao carregar cartão de crédito</p>
      </div>
    </section>

    <!-- Modal de Edição -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeEditModal"
    >
      <ModalCard title="Editar Cartão de Crédito" icon="fa-solid fa-credit-card" size="md" @close="closeEditModal">
        <CreditCardForm
          :creditCard="creditCard"
          :isEditing="true"
          @saved="handleSaved"
          @cancel="closeEditModal"
        />
      </ModalCard>
    </div>

    <!-- Modal de Nova Compra -->
    <div
      v-if="showChargeModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closeChargeModal"
    >
      <ModalCard title="Nova Compra" icon="fa-solid fa-shopping-cart" size="md" @close="closeChargeModal">
        <CreditCardChargeForm
          :creditCardId="creditCard.id"
          @saved="handleChargeSaved"
          @cancel="closeChargeModal"
        />
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { show, index } from "@/utils/requests/httpUtils";
import CreditCardForm from "@/components/forms/CreditCardForm.vue";
import CreditCardChargeForm from "@/components/forms/CreditCardChargeForm.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PageHeader from "@/components/layout/PageHeader.vue";
import EmptyState from "@/components/layout/EmptyState.vue";
import StatusToggle from "@/components/buttons/StatusToggle.vue";

export default {
  name: "CreditCardShow",
  components: {
    StatusToggle,
    EmptyState,
    PageHeader,
    ModalCard,
    CreditCardForm,
    CreditCardChargeForm,
  },
  data() {
    return {
      creditCard: null,
      invoices: [],
      loading: true,
      showEditModal: false,
      showChargeModal: false,
    };
  },
  methods: {
    async getCreditCard() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        this.creditCard = await show("credit_cards", id);
        await this.getInvoices();
      } catch (error) {
        console.error("Erro ao carregar cartão de crédito:", error);
      } finally {
        this.loading = false;
      }
    },

    async getInvoices() {
      try {
        const response = await index("credit_card_invoices", { credit_card_id: this.creditCard.id });
        this.invoices = Array.isArray(response) ? response : (response.data || []);
      } catch (error) {
        console.error("Erro ao carregar faturas:", error);
      }
    },

    statusClass(status) {
      return {
        open: "status-open",
        closed: "status-closed",
        partial: "status-partial",
        paid: "status-paid",
        overdue: "status-overdue",
      }[status] || "status-closed";
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      const [year, month, day] = dateString.split('-');
      return `${day}/${month}/${year}`;
    },

    viewInvoice(invoice) {
      this.$router.push({ name: 'credit-card-invoices-show', params: { id: invoice.id } });
    },

    openEditModal() {
      this.showEditModal = true;
    },

    closeEditModal() {
      this.showEditModal = false;
    },

    openChargeModal() {
      this.showChargeModal = true;
    },

    closeChargeModal() {
      this.showChargeModal = false;
    },

    handleSaved() {
      this.closeEditModal();
      this.getCreditCard();
    },

    handleChargeSaved() {
      this.closeChargeModal();
      this.getInvoices();
    },

    goBack() {
      this.$router.push({ name: 'credit-cards' });
    },
  },
  mounted() {
    this.getCreditCard();
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

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}

.status-paid {
  background-color: color-mix(in oklab, var(--color-success) 15%, var(--color-base-100));
  color: var(--color-success);
}

.status-overdue {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  color: var(--color-error);
}

.status-open {
  background-color: color-mix(in oklab, var(--color-info) 15%, var(--color-base-100));
  color: var(--color-info);
}

.status-closed,
.status-partial {
  background-color: color-mix(in oklab, var(--color-warning) 15%, var(--color-base-100));
  color: var(--color-warning);
}

.description-text {
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
  line-height: 1.6;
  margin: 0;
}

.list-header {
  display: flex;
  padding: 0.75rem;
  background-color: var(--color-base-200);
  border-bottom: 2px solid var(--color-base-300);
  color: color-mix(in oklab, var(--color-base-content) 80%, transparent);
}

.list-line {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  border-bottom: 1px solid var(--color-base-300);
  transition: background-color 0.2s;
}

.list-line.clickable {
  cursor: pointer;
}

.list-line.clickable:hover {
  background-color: var(--color-base-200);
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
