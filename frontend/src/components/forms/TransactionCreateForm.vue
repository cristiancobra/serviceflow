<template>
  <div>
    <div v-if="modelValue" class="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1>Novo recebimento</h1>
            <button
              type="button"
              class="btn-close"
              @click="closeModal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="mb-6">
                <TextAreaInput
                  class="text-start"
                  label="Observações:"
                  name="observations"
                  v-model="form.observations"
                  placeholder="Detalhes do recebimento"
                  :rows="4"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label
                    for="invoice"
                    class="block text-sm font-semibold text-gray-900 mb-2"
                    >Fatura</label
                  >
                  <TextValue v-model="invoiceDisplay" class="selected" />
                </div>
                <div>
                  <label
                    for="price"
                    class="block text-sm font-semibold text-gray-900 mb-2"
                    >Valor</label
                  >
                  <input
                    type="number"
                    id="price"
                    name="price"
                    v-model="form.amount"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                    class="w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 ease-in-out hover:border-gray-400 text-right"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label
                    for="bank_account_id"
                    class="block text-sm font-semibold text-gray-900 mb-2"
                    >Conta Bancária</label
                  >
                  <select
                    id="bank_account_id"
                    v-model="form.bank_account_id"
                    class="w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 ease-in-out hover:border-gray-400"
                  >
                    <option
                      v-for="account in bankAccounts"
                      :key="account.id"
                      :value="account.id"
                      class="text-gray-900"
                    >
                      {{ account.account_name }} - {{ account.bank_name }}
                    </option>
                  </select>
                </div>
                <div>
                  <label
                    for="method"
                    class="block text-sm font-semibold text-gray-900 mb-2"
                    >Método de Pagamento</label
                  >
                  <select
                    id="method"
                    v-model="form.method"
                    class="w-full px-3 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 ease-in-out hover:border-gray-400"
                  >
                    <option value="pix" class="text-gray-900">PIX</option>
                    <option value="bank_transfer" class="text-gray-900">
                      Transferência Bancária
                    </option>
                    <option value="cash" class="text-gray-900">Dinheiro</option>
                    <option value="credit_card" class="text-gray-900">
                      Cartão de Crédito
                    </option>
                    <option value="debit_card" class="text-gray-900">
                      Cartão de Débito
                    </option>
                    <option value="check" class="text-gray-900">Cheque</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <UsersSelectInput
                    class="text-start"
                    label="Responsável"
                    v-model="form.user_id"
                    fieldsToDisplay="name"
                    autoSelect="true"
                  />
                </div>
                <div>
                  <DateInput
                    class="text-start"
                    v-model="form.transaction_date"
                    label="Data de recebimento"
                    name="transaction_date"
                    placeholder="data do recebimento"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
              </div>

              <div v-if="errorMessage" class="mb-6">
                <div class="w-full">
                  <p class="error text-red-500">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>

              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="closeModal"
                >
                  Fechar
                </button>
                <button type="submit" class="button-new">criar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { submitFormCreate, index } from "@/utils/requests/httpUtils";
import DateInput from "./inputs/date/DateInput.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";

export default {
  name: "TransactionCreateForm",
  emits: ["new-transaction-event", "update:modelValue"],
  components: {
    DateInput,
    TextAreaInput,
    TextValue,
    UsersSelectInput,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: {
        invoice_id: this.invoice?.id || null,
        bank_account_id: null,
        amount: this.invoice?.price || 0,
        transaction_date: null,
        type: "credit",
        method: "pix",
        observations: null,
      },
      errorMessage: null,
      bankAccounts: [],
    };
  },
  computed: {
    invoiceDisplay() {
      return `Fatura #${this.invoice?.id || "..."}`;
    },
  },
  watch: {
    invoice: {
      handler(newInvoice) {
        if (newInvoice && newInvoice.id) {
          console.log("Invoice mudou, atualizando formulário:", newInvoice);
          this.form.invoice_id = newInvoice.id;
          this.form.amount = newInvoice.price || 0;
        }
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    submitFormCreate,
    index,
    async getBankAccounts() {
      try {
        this.bankAccounts = await this.index("bank_accounts");
        // Seleciona automaticamente a primeira conta se não houver uma já selecionada
        if (this.bankAccounts.length > 0 && !this.form.bank_account_id) {
          this.form.bank_account_id = this.bankAccounts[0].id;
        }
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
        this.bankAccounts = [];
      }
    },
    closeModal() {
      this.$emit("update:modelValue", false);
      this.errorMessage = null;
    },
    async submitForm() {
      const { data, error } = await this.submitFormCreate(
        "transactions",
        this.form
      );

      if (data) {
        this.closeModal();
        this.$emit("new-transaction-event", data);
      }
      if (error) {
        this.errorMessage = "Erro ao criar transação. Tente novamente.";
        console.error("Erro:", error);
      }
    },
    updateForm(field, value) {
      this.form[field] = value;
    },
  },
  mounted() {
    this.getBankAccounts();
  },
};
</script>

<style scoped>
.button.disabled {
  font-size: 1rem;
  text-align: center;
  display: flex;
  background-color: gray;
  color: white;
  border-color: gray;
  cursor: not-allowed;
}
</style>