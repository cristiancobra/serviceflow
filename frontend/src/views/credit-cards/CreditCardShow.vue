<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <button @click="goBack" class="btn-back">
          <font-awesome-icon icon="fa-solid fa-arrow-left" />
        </button>
        <font-awesome-icon icon="fa-solid fa-credit-card" class="page-icon" />
        <h1>{{ creditCard?.name || 'Carregando...' }}</h1>
      </div>
      <div class="page-action">
        <button @click="openChargeModal" class="btn-primary">
          <font-awesome-icon icon="fa-solid fa-plus" />
          Nova Compra
        </button>
        <button @click="openEditModal" class="btn-secondary">
          <font-awesome-icon icon="fa-solid fa-edit" />
          Editar
        </button>
      </div>
    </div>

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
                <span
                  class="status-badge"
                  :class="creditCard.is_active ? 'status-active' : 'status-inactive'"
                >
                  {{ creditCard.is_active ? 'Ativo' : 'Inativo' }}
                </span>
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
            <div class="w-2/10 text-left text-black font-semibold">
              {{ invoice.reference_label }}
            </div>
            <div class="w-2/10 text-center text-black">
              {{ formatDate(invoice.closing_date) }}
            </div>
            <div class="w-2/10 text-center text-black">
              {{ formatDate(invoice.due_date) }}
            </div>
            <div class="w-2/10 text-center text-black font-semibold">
              {{ invoice.total_amount_formatted }}
            </div>
            <div class="w-2/10 text-center">
              <span class="status-badge" :class="statusClass(invoice.status)">
                {{ invoice.status_label }}
              </span>
            </div>
          </div>

          <div v-if="invoices.length === 0" class="empty-state">
            <p>Nenhuma fatura ainda. Lance uma compra para começar.</p>
          </div>
        </div>
      </div>

      <div v-else class="error-state">
        <p>Erro ao carregar cartão de crédito</p>
      </div>
    </section>

    <!-- Modal de Edição -->
    <div v-if="showEditModal" class="modal-overlay" @click="closeEditModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Editar Cartão de Crédito</h2>
          <button @click="closeEditModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <CreditCardForm
          :creditCard="creditCard"
          :isEditing="true"
          @saved="handleSaved"
          @cancel="closeEditModal"
        />
      </div>
    </div>

    <!-- Modal de Nova Compra -->
    <div v-if="showChargeModal" class="modal-overlay" @click="closeChargeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Nova Compra</h2>
          <button @click="closeChargeModal" class="btn-close">
            <font-awesome-icon icon="fa-solid fa-times" />
          </button>
        </div>

        <CreditCardChargeForm
          :creditCardId="creditCard.id"
          @saved="handleChargeSaved"
          @cancel="closeChargeModal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { show, index } from "@/utils/requests/httpUtils";
import CreditCardForm from "@/components/forms/CreditCardForm.vue";
import CreditCardChargeForm from "@/components/forms/CreditCardChargeForm.vue";

export default {
  name: "CreditCardShow",
  components: {
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

.page-action {
  display: flex;
  gap: 0.75rem;
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

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}

.status-active,
.status-paid {
  background-color: #dcfce7;
  color: #166534;
}

.status-inactive,
.status-overdue {
  background-color: #fecaca;
  color: #991b1b;
}

.status-open {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-closed,
.status-partial {
  background-color: #fef3c7;
  color: #92400e;
}

.description-text {
  color: #374151;
  line-height: 1.6;
  margin: 0;
}

.list-header {
  display: flex;
  padding: 0.75rem;
  background-color: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
  color: #374151;
}

.list-line {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.2s;
}

.list-line.clickable {
  cursor: pointer;
}

.list-line.clickable:hover {
  background-color: #f9fafb;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
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

.btn-primary,
.btn-secondary {
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
