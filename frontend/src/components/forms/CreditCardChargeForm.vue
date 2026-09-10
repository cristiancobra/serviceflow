<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div class="form-grid">
        <!-- Descrição -->
        <div class="form-group col-span-2">
          <label for="description" class="form-label required">Descrição</label>
          <input
            id="description"
            v-model="form.description"
            type="text"
            class="form-input"
            placeholder="Ex: Notebook"
            required
          />
          <span v-if="errors.description" class="error-message">{{ errors.description[0] }}</span>
        </div>

        <!-- Valor -->
        <div class="form-group">
          <label for="amount" class="form-label required">Valor Total</label>
          <money-input
            name="amount"
            v-model="form.amount"
            class="form-input"
          />
          <span v-if="errors.amount" class="error-message">{{ errors.amount[0] }}</span>
        </div>

        <!-- Parcelas -->
        <div class="form-group">
          <label for="installment_total" class="form-label">Parcelas</label>
          <input
            id="installment_total"
            v-model.number="form.installment_total"
            type="number"
            min="1"
            max="60"
            class="form-input"
          />
          <span v-if="errors.installment_total" class="error-message">{{ errors.installment_total[0] }}</span>
        </div>

        <!-- Data da Compra -->
        <div class="form-group">
          <label for="purchase_date" class="form-label required">Data da Compra</label>
          <input
            id="purchase_date"
            v-model="form.purchase_date"
            type="date"
            class="form-input"
            required
          />
          <span v-if="errors.purchase_date" class="error-message">{{ errors.purchase_date[0] }}</span>
        </div>

        <!-- Categoria -->
        <div class="form-group">
          <label for="category" class="form-label">Categoria</label>
          <input
            id="category"
            v-model="form.category"
            type="text"
            class="form-input"
            placeholder="Ex: Equipamentos"
          />
          <span v-if="errors.category" class="error-message">{{ errors.category[0] }}</span>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="form-actions">
        <button type="button" @click="cancel" class="btn-secondary">
          Cancelar
        </button>
        <button type="submit" class="btn-primary" :disabled="isSubmitting">
          <span v-if="isSubmitting">Salvando...</span>
          <span v-else>Lançar Compra</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { store } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  name: "CreditCardChargeForm",
  components: {
    MoneyInput,
  },
  props: {
    creditCardId: {
      type: [Number, String],
      required: true,
    },
  },
  data() {
    return {
      form: this.emptyForm(),
      errors: {},
      isSubmitting: false,
    };
  },
  methods: {
    emptyForm() {
      return {
        credit_card_id: this.creditCardId,
        description: "",
        amount: 0,
        installment_total: 1,
        purchase_date: new Date().toISOString().slice(0, 10),
        category: "",
      };
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      try {
        const response = await store("credit_card_charges", {
          ...this.form,
          credit_card_id: this.creditCardId,
        });

        this.$emit("saved", response);
        this.form = this.emptyForm();
      } catch (error) {
        console.error("Erro ao lançar compra:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = {
            general: [error.response?.data?.message || "Erro ao lançar compra"]
          };
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    cancel() {
      this.form = this.emptyForm();
      this.errors = {};
      this.$emit("cancel");
    },
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
