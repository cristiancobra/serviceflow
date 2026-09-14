<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div class="form-grid">
        <!-- Nome -->
        <div class="form-group col-span-2">
          <label for="name" class="form-label required">Nome</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="form-input"
            placeholder="Ex: Aluguel do escritório"
            required
          />
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
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

        <!-- Categoria -->
        <div class="form-group">
          <label for="category" class="form-label">Categoria</label>
          <select
            id="category"
            v-model="form.category"
            class="form-input"
          >
            <option value="fixed">Fixa</option>
            <option value="variable">Variável</option>
          </select>
          <span v-if="errors.category" class="error-message">{{ errors.category[0] }}</span>
        </div>

        <!-- Departamento -->
        <div class="form-group">
          <label for="department_id" class="form-label">Departamento</label>
          <select
            id="department_id"
            v-model="form.department_id"
            class="form-input"
          >
            <option value="">Nenhum</option>
            <option
              v-for="department in departments"
              :key="department.id"
              :value="department.id"
            >
              {{ department.name }}
            </option>
          </select>
          <span v-if="errors.department_id" class="error-message">{{ errors.department_id[0] }}</span>
        </div>

        <!-- Data de Início -->
        <div class="form-group">
          <label for="start_date" class="form-label required">Início da Recorrência</label>
          <input
            id="start_date"
            v-model="form.start_date"
            type="date"
            class="form-input"
            required
          />
          <span v-if="errors.start_date" class="error-message">{{ errors.start_date[0] }}</span>
        </div>

        <!-- Data de Término -->
        <div class="form-group">
          <label for="end_date" class="form-label">Término da Recorrência (opcional)</label>
          <input
            id="end_date"
            v-model="form.end_date"
            type="date"
            class="form-input"
          />
          <span v-if="errors.end_date" class="error-message">{{ errors.end_date[0] }}</span>
        </div>

        <!-- Descrição -->
        <div class="form-group col-span-2">
          <label for="description" class="form-label">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="form-input"
            rows="3"
            placeholder="Informações adicionais sobre a despesa..."
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
            <span class="checkbox-label">Despesa ativa (gera faturas automaticamente)</span>
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
  name: "RecurringExpenseForm",
  components: {
    MoneyInput,
  },
  props: {
    recurringExpense: {
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
      departments: [],
      errors: {},
      isSubmitting: false,
    };
  },
  watch: {
    recurringExpense: {
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
        department_id: "",
        name: "",
        description: "",
        category: "fixed",
        amount: 0,
        due_day: null,
        is_active: true,
        start_date: this.getToday(),
        end_date: "",
      };
    },

    getToday() {
      const date = new Date();
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    async getDepartments() {
      try {
        const response = await index("departments");
        this.departments = Array.isArray(response) ? response : (response.data || []);
      } catch (error) {
        console.error("Erro ao carregar departamentos:", error);
      }
    },

    populateForm(recurringExpense) {
      this.form = {
        department_id: recurringExpense.department_id || "",
        name: recurringExpense.name || "",
        description: recurringExpense.description || "",
        category: recurringExpense.category || "fixed",
        amount: recurringExpense.amount || 0,
        due_day: recurringExpense.due_day || null,
        is_active: recurringExpense.is_active !== undefined ? recurringExpense.is_active : true,
        start_date: recurringExpense.start_date || this.getToday(),
        end_date: recurringExpense.end_date || "",
      };
    },

    async submitForm() {
      this.isSubmitting = true;
      this.errors = {};

      const payload = {
        ...this.form,
        department_id: this.form.department_id || null,
        end_date: this.form.end_date || null,
      };

      try {
        let response;

        if (this.isEditing) {
          response = await update("recurring_expenses", this.recurringExpense.id, payload);
        } else {
          response = await store("recurring_expenses", payload);
        }

        this.$emit("saved", response);
        this.form = this.emptyForm();
      } catch (error) {
        console.error("Erro ao salvar despesa recorrente:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = {
            general: [error.response?.data?.message || "Erro ao salvar despesa recorrente"]
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
    this.getDepartments();

    if (this.recurringExpense) {
      this.populateForm(this.recurringExpense);
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
