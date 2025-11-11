<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div class="form-grid">
        <!-- Nome da Conta -->
        <div class="form-group col-span-2">
          <label for="account_name" class="form-label required">Nome da Conta</label>
          <input
            id="account_name"
            v-model="form.account_name"
            type="text"
            class="form-input"
            placeholder="Ex: Conta Empresarial Principal"
            required
          />
          <span v-if="errors.account_name" class="error-message">{{ errors.account_name[0] }}</span>
        </div>

        <!-- Banco -->
        <div class="form-group col-span-2">
          <label for="bank_name" class="form-label required">Banco</label>
          <input
            id="bank_name"
            v-model="form.bank_name"
            type="text"
            class="form-input"
            placeholder="Ex: Banco do Brasil"
            required
          />
          <span v-if="errors.bank_name" class="error-message">{{ errors.bank_name[0] }}</span>
        </div>

        <!-- Agência -->
        <div class="form-group">
          <label for="agency" class="form-label">Agência</label>
          <input
            id="agency"
            v-model="form.agency"
            type="text"
            class="form-input"
            placeholder="Ex: 1234-5"
          />
          <span v-if="errors.agency" class="error-message">{{ errors.agency[0] }}</span>
        </div>

        <!-- Número da Conta -->
        <div class="form-group">
          <label for="account_number" class="form-label required">Número da Conta</label>
          <input
            id="account_number"
            v-model="form.account_number"
            type="text"
            class="form-input"
            placeholder="Ex: 12345-6"
            required
          />
          <span v-if="errors.account_number" class="error-message">{{ errors.account_number[0] }}</span>
        </div>

        <!-- Tipo de Conta -->
        <div class="form-group">
          <label for="type" class="form-label required">Tipo de Conta</label>
          <select
            id="type"
            v-model="form.type"
            class="form-input"
            required
          >
            <option value="">Selecione...</option>
            <option
              v-for="(label, value) in typeOptions"
              :key="value"
              :value="value"
            >
              {{ label }}
            </option>
          </select>
          <span v-if="errors.type" class="error-message">{{ errors.type[0] }}</span>
        </div>

        <!-- Saldo Inicial -->
        <div class="form-group">
          <label for="initial_balance" class="form-label">Saldo Inicial</label>
          <input
            id="initial_balance"
            v-model.number="form.initial_balance"
            type="number"
            step="0.01"
            class="form-input"
            placeholder="0.00"
          />
          <span v-if="errors.initial_balance" class="error-message">{{ errors.initial_balance[0] }}</span>
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
            placeholder="Informações adicionais sobre a conta..."
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
            <span class="checkbox-label">Conta ativa</span>
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
import { index, store, update, get } from "@/utils/requests/httpUtils";

export default {
  name: "BankAccountForm",
  props: {
    bankAccount: {
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
      form: {
        user_id: "",
        account_name: "",
        account_number: "",
        bank_name: "",
        agency: "",
        initial_balance: 0,
        type: "",
        is_active: true,
        description: "",
      },
      users: [],
      typeOptions: {},
      errors: {},
      isSubmitting: false,
    };
  },
  watch: {
    bankAccount: {
      handler(newValue) {
        if (newValue) {
          this.populateForm(newValue);
        }
      },
      immediate: true,
    },
  },
  methods: {
    async getUsers() {
      try {
        const response = await index("users");
        this.users = response.data || response;
      } catch (error) {
        console.error("Erro ao carregar usuários:", error);
      }
    },

    async getTypeOptions() {
      try {
        this.typeOptions = await get("bank_accounts/options/types");
      } catch (error) {
        console.error("Erro ao carregar tipos de conta:", error);
      }
    },

    populateForm(bankAccount) {
      this.form = {
        user_id: bankAccount.user_id || "",
        account_name: bankAccount.account_name || "",
        account_number: bankAccount.account_number || "",
        bank_name: bankAccount.bank_name || "",
        agency: bankAccount.agency || "",
        initial_balance: bankAccount.initial_balance || 0,
        type: bankAccount.type || "",
        is_active: bankAccount.is_active !== undefined ? bankAccount.is_active : true,
        description: bankAccount.description || "",
      };
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      try {
        let response;
        
        if (this.isEditing) {
          response = await update("bank_accounts", this.bankAccount.id, this.form);
        } else {
          response = await store("bank_accounts", this.form);
        }

        this.$emit("saved", response);
        this.resetForm();
      } catch (error) {
        console.error("Erro ao salvar conta bancária:", error);
        
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = { 
            general: [error.response?.data?.message || "Erro ao salvar conta bancária"] 
          };
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    cancel() {
      this.resetForm();
      this.$emit("cancel");
    },

    resetForm() {
      this.form = {
        user_id: "",
        account_name: "",
        account_number: "",
        bank_name: "",
        agency: "",
        initial_balance: 0,
        type: "",
        is_active: true,
        description: "",
      };
      this.errors = {};
    },
  },
  mounted() {
    this.getUsers();
    this.getTypeOptions();
    
    if (this.bankAccount) {
      this.populateForm(this.bankAccount);
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

.form-input:disabled {
  background-color: #f3f4f6;
  cursor: not-allowed;
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
