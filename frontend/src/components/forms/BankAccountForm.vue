<template>
  <div>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 mb-6">
        <!-- Nome da Conta -->
        <div class="fieldset md:col-span-2">
          <label for="account_name" class="fieldset-legend justify-start">Nome da Conta <span class="text-error">*</span></label>
          <input
            id="account_name"
            v-model="form.account_name"
            type="text"
            class="input w-full"
            placeholder="Ex: Conta Empresarial Principal"
            required
          />
          <span v-if="errors.account_name" class="text-error text-xs">{{ errors.account_name[0] }}</span>
        </div>

        <!-- Banco -->
        <div class="fieldset md:col-span-2">
          <label for="bank_name" class="fieldset-legend justify-start">Banco <span class="text-error">*</span></label>
          <input
            id="bank_name"
            v-model="form.bank_name"
            type="text"
            class="input w-full"
            placeholder="Ex: Banco do Brasil"
            required
          />
          <span v-if="errors.bank_name" class="text-error text-xs">{{ errors.bank_name[0] }}</span>
        </div>

        <!-- Agência -->
        <div class="fieldset">
          <label for="agency" class="fieldset-legend">Agência</label>
          <input
            id="agency"
            v-model="form.agency"
            type="text"
            class="input w-full"
            placeholder="Ex: 1234-5"
          />
          <span v-if="errors.agency" class="text-error text-xs">{{ errors.agency[0] }}</span>
        </div>

        <!-- Número da Conta -->
        <div class="fieldset">
          <label for="account_number" class="fieldset-legend justify-start">Número da Conta <span class="text-error">*</span></label>
          <input
            id="account_number"
            v-model="form.account_number"
            type="text"
            class="input w-full"
            placeholder="Ex: 12345-6"
            required
          />
          <span v-if="errors.account_number" class="text-error text-xs">{{ errors.account_number[0] }}</span>
        </div>

        <!-- Tipo de Conta -->
        <div class="fieldset">
          <label for="type" class="fieldset-legend justify-start">Tipo de Conta <span class="text-error">*</span></label>
          <select
            id="type"
            v-model="form.type"
            class="select w-full"
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
          <span v-if="errors.type" class="text-error text-xs">{{ errors.type[0] }}</span>
        </div>

        <!-- Saldo Inicial -->
        <div class="fieldset">
          <label for="initial_balance" class="fieldset-legend">Saldo Inicial</label>
          <money-input
            name="initial_balance"
            v-model="form.initial_balance"
            allow-negative
            class="input w-full"
          />
          <span v-if="errors.initial_balance" class="text-error text-xs">{{ errors.initial_balance[0] }}</span>
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
            placeholder="Informações adicionais sobre a conta..."
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
            <span class="text-sm font-medium">Conta ativa</span>
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
import { index, store, update, get } from "@/utils/requests/httpUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";

export default {
  name: "BankAccountForm",
  components: {
    MoneyInput,
  },
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
