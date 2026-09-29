<template>
  <div>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 mb-6">
        <!-- Nome -->
        <div class="fieldset md:col-span-2">
          <label for="name" class="fieldset-legend justify-start">Nome <span class="text-error">*</span></label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="input w-full"
            placeholder="Ex: Aluguel do escritório"
            required
          />
          <span v-if="errors.name" class="text-error text-xs">{{ errors.name[0] }}</span>
        </div>

        <!-- Valor -->
        <div class="fieldset">
          <label for="amount" class="fieldset-legend justify-start">Valor <span class="text-error">*</span></label>
          <money-input
            name="amount"
            v-model="form.amount"
            class="input w-full"
          />
          <span v-if="errors.amount" class="text-error text-xs">{{ errors.amount[0] }}</span>
        </div>

        <!-- Dia de Vencimento -->
        <div class="fieldset">
          <label for="due_day" class="fieldset-legend justify-start">Dia de Vencimento <span class="text-error">*</span></label>
          <input
            id="due_day"
            v-model.number="form.due_day"
            type="number"
            min="1"
            max="31"
            class="input w-full"
            required
          />
          <span v-if="errors.due_day" class="text-error text-xs">{{ errors.due_day[0] }}</span>
        </div>

        <!-- Categoria -->
        <div class="fieldset">
          <label for="category" class="fieldset-legend">Categoria</label>
          <select
            id="category"
            v-model="form.category"
            class="select w-full"
          >
            <option value="fixed">Fixa</option>
            <option value="variable">Variável</option>
          </select>
          <span v-if="errors.category" class="text-error text-xs">{{ errors.category[0] }}</span>
        </div>

        <!-- Departamento -->
        <div class="fieldset">
          <label for="department_id" class="fieldset-legend">Departamento</label>
          <select
            id="department_id"
            v-model="form.department_id"
            class="select w-full"
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
          <span v-if="errors.department_id" class="text-error text-xs">{{ errors.department_id[0] }}</span>
        </div>

        <!-- Data de Início -->
        <div class="fieldset">
          <label for="start_date" class="fieldset-legend justify-start">Início da Recorrência <span class="text-error">*</span></label>
          <input
            id="start_date"
            v-model="form.start_date"
            type="date"
            class="input w-full"
            required
          />
          <span v-if="errors.start_date" class="text-error text-xs">{{ errors.start_date[0] }}</span>
        </div>

        <!-- Data de Término -->
        <div class="fieldset">
          <label for="end_date" class="fieldset-legend">Término da Recorrência (opcional)</label>
          <input
            id="end_date"
            v-model="form.end_date"
            type="date"
            class="input w-full"
          />
          <span v-if="errors.end_date" class="text-error text-xs">{{ errors.end_date[0] }}</span>
        </div>

        <!-- Cliente (opcional) -->
        <div class="fieldset md:col-span-2">
          <label class="fieldset-legend">Cliente (opcional)</label>
          <div class="flex gap-5 mb-3">
            <label class="flex items-center gap-1.5 cursor-pointer text-sm">
              <input type="radio" class="radio radio-primary radio-sm" v-model="clientType" value="none" />
              <span>Sem cliente</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer text-sm">
              <input type="radio" class="radio radio-primary radio-sm" v-model="clientType" value="lead" />
              <span>Pessoa</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer text-sm">
              <input type="radio" class="radio radio-primary radio-sm" v-model="clientType" value="company" />
              <span>Empresa</span>
            </label>
          </div>
          <LeadsSelectInput
            v-if="clientType === 'lead'"
            name="lead_id"
            label="Cliente (Pessoa)"
            v-model="form.lead_id"
            fieldsToDisplay="name"
            fieldNull="Selecione um cliente"
          />
          <CompaniesSelectInput
            v-if="clientType === 'company'"
            name="company_id"
            label="Cliente (Empresa)"
            v-model="form.company_id"
            :fieldsToDisplay="['business_name', 'legal_name']"
            fieldNull="Selecione uma empresa"
          />
          <span v-if="errors.lead_id" class="text-error text-xs">{{ errors.lead_id[0] }}</span>
          <span v-if="errors.company_id" class="text-error text-xs">{{ errors.company_id[0] }}</span>
        </div>

        <!-- Descrição -->
        <div class="fieldset md:col-span-2">
          <label for="description" class="fieldset-legend">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="textarea w-full"
            rows="3"
            placeholder="Informações adicionais sobre a despesa..."
          ></textarea>
          <span v-if="errors.description" class="text-error text-xs">{{ errors.description[0] }}</span>
        </div>

        <!-- Status Ativo -->
        <div class="fieldset md:col-span-2">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input
              type="checkbox"
              class="checkbox checkbox-primary checkbox-sm"
              v-model="form.is_active"
            />
            <span class="text-sm font-medium">Despesa ativa (gera faturas automaticamente)</span>
          </label>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="flex justify-end gap-3 pt-4 border-t border-base-300">
        <button type="button" @click="cancel" class="btn btn-ghost">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
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
import LeadsSelectInput from "./selects/LeadsSelectInput.vue";
import CompaniesSelectInput from "./selects/CompaniesSelectInput.vue";

export default {
  name: "RecurringExpenseForm",
  components: {
    MoneyInput,
    LeadsSelectInput,
    CompaniesSelectInput,
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
      clientType: "none",
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
    clientType(newType) {
      if (newType !== "lead") this.form.lead_id = null;
      if (newType !== "company") this.form.company_id = null;
    },
  },
  methods: {
    emptyForm() {
      return {
        department_id: "",
        lead_id: null,
        company_id: null,
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
        lead_id: recurringExpense.lead_id || null,
        company_id: recurringExpense.company_id || null,
        name: recurringExpense.name || "",
        description: recurringExpense.description || "",
        category: recurringExpense.category || "fixed",
        amount: recurringExpense.amount || 0,
        due_day: recurringExpense.due_day || null,
        is_active: recurringExpense.is_active !== undefined ? recurringExpense.is_active : true,
        start_date: recurringExpense.start_date || this.getToday(),
        end_date: recurringExpense.end_date || "",
      };

      this.clientType = recurringExpense.lead_id
        ? "lead"
        : recurringExpense.company_id
          ? "company"
          : "none";
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
        this.clientType = "none";
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
      this.clientType = "none";
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
