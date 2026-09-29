<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <button @click="goBack" class="btn-back">
          <font-awesome-icon icon="fa-solid fa-arrow-left" />
        </button>
        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="page-icon" />
        <h1 v-if="invoice">
          Fatura {{ invoice.credit_card?.name }} — {{ invoice.reference_label }}
        </h1>
        <h1 v-else>Carregando...</h1>
      </div>
      <div class="page-action" v-if="invoice">
        <button
          v-if="invoice.status === 'open'"
          @click="closeInvoice"
          class="btn btn-secondary"
        >
          <font-awesome-icon icon="fa-solid fa-lock" />
          Fechar Fatura
        </button>
        <button
          v-if="['closed', 'partial', 'overdue'].includes(invoice.status)"
          @click="openPayModal"
          class="btn btn-primary"
        >
          <font-awesome-icon icon="fa-solid fa-money-bill-wave" />
          Registrar Pagamento
        </button>
      </div>
    </div>

    <section class="section-container">
      <div v-if="loading" class="loading-state">
        <p>Carregando...</p>
      </div>

      <div v-else-if="invoice" class="details-grid">
        <!-- Resumo -->
        <div class="card">
          <h2 class="card-title">Resumo</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Status:</span>
              <span class="status-badge" :class="statusClass(invoice.status)">
                {{ invoice.status_label }}
              </span>
            </div>
            <div class="info-item">
              <span class="info-label">Fechamento:</span>
              <span class="info-value">{{ formatDate(invoice.closing_date) }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Vencimento:</span>
              <span class="info-value">{{ formatDate(invoice.due_date) }}</span>
            </div>
          </div>
        </div>

        <div class="card">
          <h2 class="card-title">Valores</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Total da Fatura:</span>
              <span class="info-value font-semibold">{{ invoice.total_amount_formatted }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Pago:</span>
              <span class="info-value">{{ invoice.total_paid_formatted }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Saldo:</span>
              <span class="info-value font-semibold">{{ invoice.balance_formatted }}</span>
            </div>
          </div>
        </div>

        <!-- Compras -->
        <div class="card col-span-2">
          <h2 class="card-title">Compras</h2>

          <div class="list-header">
            <div class="w-3/10 text-left font-bold">Descrição</div>
            <div class="w-2/10 text-center font-bold">Data</div>
            <div class="w-2/10 text-center font-bold">Categoria</div>
            <div class="w-1/10 text-center font-bold">Parcela</div>
            <div class="w-1/10 text-center font-bold">Valor</div>
            <div class="w-1/10 text-center font-bold" v-if="invoice.status === 'open'">Ações</div>
          </div>

          <div
            v-for="charge in invoice.charges"
            :key="charge.id"
            class="list-line"
          >
            <div class="w-3/10 text-left text-base-content font-semibold">
              {{ charge.description }}
            </div>
            <div class="w-2/10 text-center text-base-content">
              {{ formatDate(charge.purchase_date) }}
            </div>
            <div class="w-2/10 text-center text-base-content">
              {{ charge.category || '-' }}
            </div>
            <div class="w-1/10 text-center text-base-content">
              {{ charge.installment_label }}
            </div>
            <div class="w-1/10 text-center text-base-content font-semibold">
              {{ charge.amount_formatted }}
            </div>
            <div class="w-1/10 text-center" v-if="invoice.status === 'open'">
              <delete-icon-button
                size="w-8 h-8"
                title="Excluir"
                :confirm-message='`Excluir a compra "${charge.description}"?`'
                @confirm="deleteCharge(charge)"
              />
            </div>
          </div>

          <div v-if="!invoice.charges || invoice.charges.length === 0" class="empty-state">
            <p>Nenhuma compra lançada nesta fatura.</p>
          </div>
        </div>

        <!-- Pagamentos -->
        <div class="card col-span-2" v-if="invoice.transactions && invoice.transactions.length > 0">
          <h2 class="card-title">Pagamentos</h2>

          <div class="list-header">
            <div class="w-3/10 text-left font-bold">Conta Bancária</div>
            <div class="w-3/10 text-center font-bold">Data</div>
            <div class="w-3/10 text-center font-bold">Método</div>
            <div class="w-3/10 text-center font-bold">Valor</div>
          </div>

          <div
            v-for="transaction in invoice.transactions"
            :key="transaction.id"
            class="list-line"
          >
            <div class="w-3/10 text-left text-base-content font-semibold">
              {{ transaction.bank_account?.account_name || '-' }}
            </div>
            <div class="w-3/10 text-center text-base-content">
              {{ formatDateTime(transaction.transaction_date) }}
            </div>
            <div class="w-3/10 text-center text-base-content">
              {{ transaction.method }}
            </div>
            <div class="w-3/10 text-center text-base-content font-semibold">
              {{ transaction.amount_formatted || transaction.amount }}
            </div>
          </div>
        </div>
      </div>

      <div v-else class="error-state">
        <p>Erro ao carregar fatura</p>
      </div>
    </section>

    <!-- Modal de Pagamento -->
    <div
      v-if="showPayModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm"
      @click.self="closePayModal"
    >
      <ModalCard title="Registrar Pagamento" icon="fa-solid fa-money-bill-wave" size="md" @close="closePayModal">
        <CreditCardInvoicePaymentForm
          :invoiceId="invoice.id"
          :balance="invoice.balance"
          :defaultBankAccountId="invoice.credit_card?.default_bank_account_id"
          @saved="handlePaySaved"
          @cancel="closePayModal"
        />
      </ModalCard>
    </div>
  </div>
</template>

<script>
import { show, post, destroy } from "@/utils/requests/httpUtils";
import CreditCardInvoicePaymentForm from "@/components/forms/CreditCardInvoicePaymentForm.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "CreditCardInvoiceShow",
  components: {
    ModalCard,
    CreditCardInvoicePaymentForm,
    DeleteIconButton,
  },
  data() {
    return {
      invoice: null,
      loading: true,
      showPayModal: false,
    };
  },
  methods: {
    async getInvoice() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        this.invoice = await show("credit_card_invoices", id);
      } catch (error) {
        console.error("Erro ao carregar fatura:", error);
      } finally {
        this.loading = false;
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

    formatDateTime(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR');
    },

    async closeInvoice() {
      try {
        await post(`credit_card_invoices/${this.invoice.id}/close`);
        this.getInvoice();
      } catch (error) {
        console.error("Erro ao fechar fatura:", error);
        alert(error.response?.data?.message || "Erro ao fechar fatura");
      }
    },

    async deleteCharge(charge) {
      try {
        await destroy("credit_card_charges", charge.id);
        this.getInvoice();
      } catch (error) {
        console.error("Erro ao excluir compra:", error);
        alert(error.response?.data?.message || "Erro ao excluir compra");
      }
    },

    openPayModal() {
      this.showPayModal = true;
    },

    closePayModal() {
      this.showPayModal = false;
    },

    handlePaySaved() {
      this.closePayModal();
      this.getInvoice();
    },

    goBack() {
      this.$router.push({ name: 'credit-cards-show', params: { id: this.invoice?.credit_card?.id } });
    },
  },
  mounted() {
    this.getInvoice();
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
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
  transition: color 0.2s;
}

.btn-back:hover {
  color: var(--color-base-content);
}

.page-action {
  display: flex;
  gap: 0.75rem;
}

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
  width: fit-content;
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
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: color-mix(in oklab, var(--color-base-content) 60%, transparent);
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
  margin: 0 auto;
}

.btn-delete {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
  color: var(--color-error);
}

.btn-delete:hover {
  background-color: color-mix(in oklab, var(--color-error) 15%, var(--color-base-100));
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
