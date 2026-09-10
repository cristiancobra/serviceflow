<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div class="form-grid">
        <!-- Nome do Cartão -->
        <div class="form-group col-span-2">
          <label for="name" class="form-label required">Nome do Cartão</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-input"
            placeholder="Ex: Nubank Empresarial"
            required
          />
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
        </div>

        <!-- Bandeira -->
        <div class="form-group">
          <label for="brand" class="form-label">Bandeira</label>
          <input
            id="brand"
            v-model="form.brand"
            type="text"
            class="form-input"
            placeholder="Ex: Visa, Mastercard"
          />
          <span v-if="errors.brand" class="error-message">{{ errors.brand[0] }}</span>
        </div>

        <!-- Últimos Dígitos -->
        <div class="form-group">
          <label for="last_digits" class="form-label">Últimos Dígitos</label>
          <input
            id="last_digits"
            v-model="form.last_digits"
            type="text"
            maxlength="4"
            class="form-input"
            placeholder="Ex: 1234"
          />
          <span v-if="errors.last_digits" class="error-message">{{ errors.last_digits[0] }}</span>
        </div>

        <!-- Dia de Fechamento -->
        <div class="form-group">
          <label for="closing_day" class="form-label required">Dia de Fechamento</label>
          <input
            id="closing_day"
            v-model.number="form.closing_day"
            type="number"
            min="1"
            max="31"
            class="form-input"
            required
          />
          <span v-if="errors.closing_day" class="error-message">{{ errors.closing_day[0] }}</span>
        </div>

        <!-- Dia de Vencimento -->
        <div class="form-group">
          <label for="due_day" class="form-label required">Dia de Vencimento</label>
          <input
            id="due_day"
            v-model.number="form.due_day"
            type="number"
            min="1"
            max="31"
            class="form-input"
            required
          />
          <span v-if="errors.due_day" class="error-message">{{ errors.due_day[0] }}</span>
        </div>

        <!-- Limite de Crédito -->
        <div class="form-group">
          <label for="credit_limit" class="form-label">Limite de Crédito</label>
          <money-input
            name="credit_limit"
            v-model="form.credit_limit"
            class="form-input"
          />
          <span v-if="errors.credit_limit" class="error-message">{{ errors.credit_limit[0] }}</span>
        </div>

        <!-- Conta Bancária Padrão -->
        <div class="form-group">
          <label for="default_bank_account_id" class="form-label">Conta Padrão p/ Pagamento</label>
          <select
            id="default_bank_account_id"
            v-model="form.default_bank_account_id"
            class="form-input"
          >
            <option value="">Nenhuma</option>
            <option
              v-for="bankAccount in bankAccounts"
              :key="bankAccount.id"
              :value="bankAccount.id"
            >
              {{ bankAccount.account_name }}
            </option>
          </select>
          <span v-if="errors.default_bank_account_id" class="error-message">{{ errors.default_bank_account_id[0] }}</span>
        </div>

        <!-- Usuário Responsável -->
        <div class="form-group col-span-2">
          <label for="user_id" class="form-label">Usuário Responsável</label>
          <select
            id="user_id"
            v-model="form.user_id"
            class="form-input"
          >
            <option value="">Nenhum</option>
            <option
              v-for="user in users"
              :key="user.id"
              :value="user.id"
            >
              {{ user.name }}
            </option>
          </select>
          <span v-if="errors.user_id" class="error-message">{{ errors.user_id[0] }}</span>
        </div>

        <!-- Descrição -->
        <div class="form-group col-span-2">
          <label for="description" class="form-label">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="form-input"
            rows="3"
            placeholder="Informações adicionais sobre o cartão..."
          ></textarea>
          <span v-if="errors.description" class="error-message">{{ errors.description[0] }}</span>
        </div>

        <!-- Status Ativo -->
        <div class="form-group col-span-2">
          <label class="form-checkbox">
            <input
              type="checkbox"
              v-model="form.is_active"
            />
            <span class="checkbox-label">Cartão ativo</span>
          </label>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="form-actions">
        <button type="button" @click="cancel" class="btn-secondary">
          Cancelar
        </button>
        <button type="submit" class="btn-primary" :disabled="isSubmitting">
          <span v-if="isSubmitting">Salvando...</span>
          <span v-else>{{ isEditing ? 'Atualizar' : 'Criar' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { index, store, update } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  name: "CreditCardForm",
  components: {
    MoneyInput,
  },
  props: {
    creditCard: {
      type: Object,
      default: null,
    },
    isEditing: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: this.emptyForm(),
      users: [],
      bankAccounts: [],
      errors: {},
      isSubmitting: false,
    };
  },
  watch: {
    creditCard: {
      handler(newValue) {
        if (newValue) {
          this.populateForm(newValue);
        }
      },
      immediate: true,
    },
  },
  methods: {
    emptyForm() {
      return {
        user_id: "",
        default_bank_account_id: "",
        name: "",
        brand: "",
        last_digits: "",
        credit_limit: 0,
        closing_day: null,
        due_day: null,
        is_active: true,
        description: "",
      };
    },

    async getUsers() {
      try {
        const response = await index("users");
        this.users = response.data || response;
      } catch (error) {
        console.error("Erro ao carregar usuários:", error);
      }
    },

    async getBankAccounts() {
      try {
        const response = await index("bank_accounts");
        this.bankAccounts = Array.isArray(response) ? response : (response.data || []);
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
      }
    },

    populateForm(creditCard) {
      this.form = {
        user_id: creditCard.user_id || "",
        default_bank_account_id: creditCard.default_bank_account_id || "",
        name: creditCard.name || "",
        brand: creditCard.brand || "",
        last_digits: creditCard.last_digits || "",
        credit_limit: creditCard.credit_limit || 0,
        closing_day: creditCard.closing_day || null,
        due_day: creditCard.due_day || null,
        is_active: creditCard.is_active !== undefined ? creditCard.is_active : true,
        description: creditCard.description || "",
      };
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      try {
        let response;

        if (this.isEditing) {
          response = await update("credit_cards", this.creditCard.id, this.form);
        } else {
          response = await store("credit_cards", this.form);
        }

        this.$emit("saved", response);
        this.form = this.emptyForm();
      } catch (error) {
        console.error("Erro ao salvar cartão de crédito:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = {
            general: [error.response?.data?.message || "Erro ao salvar cartão de crédito"]
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
  mounted() {
    this.getUsers();
    this.getBankAccounts();

    if (this.creditCard) {
      this.populateForm(this.creditCard);
    }
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

textarea.form-input {
  resize: vertical;
  font-family: inherit;
}

.form-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
}

.form-checkbox input[type="checkbox"] {
  width: 1.25rem;
  height: 1.25rem;
  margin-right: 0.5rem;
  cursor: pointer;
}

.checkbox-label {
  font-weight: 500;
  color: #374151;
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
