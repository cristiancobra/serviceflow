<template>
  <div>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 mb-6">
        <!-- Nome do Cartão -->
        <div class="fieldset md:col-span-2">
          <label for="name" class="fieldset-legend justify-start">Nome do Cartão <span class="text-error">*</span></label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="input w-full"
            placeholder="Ex: Nubank Empresarial"
            required
          />
          <span v-if="errors.name" class="text-error text-xs">{{ errors.name[0] }}</span>
        </div>

        <!-- Bandeira -->
        <div class="fieldset">
          <label for="brand" class="fieldset-legend">Bandeira</label>
          <input
            id="brand"
            v-model="form.brand"
            type="text"
            class="input w-full"
            placeholder="Ex: Visa, Mastercard"
          />
          <span v-if="errors.brand" class="text-error text-xs">{{ errors.brand[0] }}</span>
        </div>

        <!-- Últimos Dígitos -->
        <div class="fieldset">
          <label for="last_digits" class="fieldset-legend">Últimos Dígitos</label>
          <input
            id="last_digits"
            v-model="form.last_digits"
            type="text"
            maxlength="4"
            class="input w-full"
            placeholder="Ex: 1234"
          />
          <span v-if="errors.last_digits" class="text-error text-xs">{{ errors.last_digits[0] }}</span>
        </div>

        <!-- Dia de Fechamento -->
        <div class="fieldset">
          <label for="closing_day" class="fieldset-legend justify-start">Dia de Fechamento <span class="text-error">*</span></label>
          <input
            id="closing_day"
            v-model.number="form.closing_day"
            type="number"
            min="1"
            max="31"
            class="input w-full"
            required
          />
          <span v-if="errors.closing_day" class="text-error text-xs">{{ errors.closing_day[0] }}</span>
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

        <!-- Limite de Crédito -->
        <div class="fieldset">
          <label for="credit_limit" class="fieldset-legend">Limite de Crédito</label>
          <money-input
            name="credit_limit"
            v-model="form.credit_limit"
            class="input w-full"
          />
          <span v-if="errors.credit_limit" class="text-error text-xs">{{ errors.credit_limit[0] }}</span>
        </div>

        <!-- Conta Bancária Padrão -->
        <div class="fieldset">
          <label for="default_bank_account_id" class="fieldset-legend">Conta Padrão p/ Pagamento</label>
          <select
            id="default_bank_account_id"
            v-model="form.default_bank_account_id"
            class="select w-full"
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
          <span v-if="errors.default_bank_account_id" class="text-error text-xs">{{ errors.default_bank_account_id[0] }}</span>
        </div>

        <!-- Usuário Responsável -->
        <div class="fieldset md:col-span-2">
          <label for="user_id" class="fieldset-legend">Usuário Responsável</label>
          <select
            id="user_id"
            v-model="form.user_id"
            class="select w-full"
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
          <span v-if="errors.user_id" class="text-error text-xs">{{ errors.user_id[0] }}</span>
        </div>

        <!-- Descrição -->
        <div class="fieldset md:col-span-2">
          <label for="description" class="fieldset-legend">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            class="textarea w-full"
            rows="3"
            placeholder="Informações adicionais sobre o cartão..."
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
            <span class="text-sm font-medium">Cartão ativo</span>
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
