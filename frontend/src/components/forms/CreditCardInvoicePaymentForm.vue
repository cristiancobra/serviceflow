<template>
  <div>
    <form @submit.prevent="submitForm">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 mb-6">
        <!-- Conta Bancária -->
        <div class="fieldset md:col-span-2">
          <label for="bank_account_id" class="fieldset-legend justify-start">Conta Bancária <span class="text-error">*</span></label>
          <select
            id="bank_account_id"
            v-model="form.bank_account_id"
            class="select w-full"
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
          <span v-if="errors.bank_account_id" class="text-error text-xs">{{ errors.bank_account_id[0] }}</span>
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

        <!-- Data do Pagamento -->
        <div class="fieldset">
          <label for="transaction_date" class="fieldset-legend justify-start">Data do Pagamento <span class="text-error">*</span></label>
          <input
            id="transaction_date"
            v-model="form.transaction_date"
            type="date"
            class="input w-full"
            required
          />
          <span v-if="errors.transaction_date" class="text-error text-xs">{{ errors.transaction_date[0] }}</span>
        </div>

        <!-- Método -->
        <div class="fieldset md:col-span-2">
          <label for="method" class="fieldset-legend justify-start">Método <span class="text-error">*</span></label>
          <select
            id="method"
            v-model="form.method"
            class="select w-full"
            required
          >
            <option value="bank_transfer">Transferência Bancária</option>
            <option value="pix">Pix</option>
            <option value="cash">Dinheiro</option>
            <option value="check">Cheque</option>
          </select>
          <span v-if="errors.method" class="text-error text-xs">{{ errors.method[0] }}</span>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="flex justify-end gap-3 pt-4 border-t border-base-300">
        <button type="button" @click="cancel" class="btn btn-ghost">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
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
