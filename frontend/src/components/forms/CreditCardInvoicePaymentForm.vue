<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div class="form-grid">
        <!-- Conta Bancária -->
        <div class="form-group col-span-2">
          <label for="bank_account_id" class="form-label required">Conta Bancária</label>
          <select
            id="bank_account_id"
            v-model="form.bank_account_id"
            class="form-input"
            required
          >
            <option value="">Selecione...</option>
            <option
              v-for="bankAccount in bankAccounts"
              :key="bankAccount.id"
              :value="bankAccount.id"
            >
              {{ bankAccount.account_name }}
            </option>
          </select>
          <span v-if="errors.bank_account_id" class="error-message">{{ errors.bank_account_id[0] }}</span>
        </div>

        <!-- Valor -->
        <div class="form-group">
          <label for="amount" class="form-label required">Valor</label>
          <money-input
            name="amount"
            v-model="form.amount"
            class="form-input"
          />
          <span v-if="errors.amount" class="error-message">{{ errors.amount[0] }}</span>
        </div>

        <!-- Data do Pagamento -->
        <div class="form-group">
          <label for="transaction_date" class="form-label required">Data do Pagamento</label>
          <input
            id="transaction_date"
            v-model="form.transaction_date"
            type="date"
            class="form-input"
            required
          />
          <span v-if="errors.transaction_date" class="error-message">{{ errors.transaction_date[0] }}</span>
        </div>

        <!-- Método -->
        <div class="form-group col-span-2">
          <label for="method" class="form-label required">Método</label>
          <select
            id="method"
            v-model="form.method"
            class="form-input"
            required
          >
            <option value="bank_transfer">Transferência Bancária</option>
            <option value="pix">Pix</option>
            <option value="cash">Dinheiro</option>
            <option value="check">Cheque</option>
          </select>
          <span v-if="errors.method" class="error-message">{{ errors.method[0] }}</span>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="form-actions">
        <button type="button" @click="cancel" class="btn-secondary">
          Cancelar
        </button>
        <button type="submit" class="btn-primary" :disabled="isSubmitting">
          <span v-if="isSubmitting">Salvando...</span>
          <span v-else>Registrar Pagamento</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { index, post } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  name: "CreditCardInvoicePaymentForm",
  components: {
    MoneyInput,
  },
  props: {
    invoiceId: {
      type: [Number, String],
      required: true,
    },
    balance: {
      type: [Number, String],
      default: 0,
    },
    defaultBankAccountId: {
      type: [Number, String],
      default: "",
    },
  },
  data() {
    return {
      form: {
        bank_account_id: this.defaultBankAccountId || "",
        amount: Number(this.balance) || 0,
        transaction_date: new Date().toISOString().slice(0, 10),
        method: "bank_transfer",
      },
      bankAccounts: [],
      errors: {},
      isSubmitting: false,
    };
  },
  methods: {
    async getBankAccounts() {
      try {
        const response = await index("bank_accounts");
        this.bankAccounts = Array.isArray(response) ? response : (response.data || []);
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
      }
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      try {
        const response = await post(`credit_card_invoices/${this.invoiceId}/pay`, this.form);
        this.$emit("saved", response);
      } catch (error) {
        console.error("Erro ao registrar pagamento:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = {
            general: [error.response?.data?.message || "Erro ao registrar pagamento"]
          };
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    cancel() {
      this.$emit("cancel");
    },
  },
  mounted() {
    this.getBankAccounts();
  },
};
</script>

<style scoped>
.form-container {
  padding: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.col-span-2 {
  grid-column: span 2;
}

.form-label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #374151;
  font-size: 0.875rem;
}

.form-label.required::after {
  content: " *";
  color: #ef4444;
}

.form-input {
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.error-message {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary,
.btn-secondary {
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  opacity: 0.9;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .col-span-2 {
    grid-column: span 1;
  }
}
</style>
