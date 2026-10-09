<template>
  <ModalCard
    :title="modalTitle"
    icon="fa-solid fa-money-bill-wave"
    :compact="compact"
    @close="closeModal"
  >
            <form id="transactionCreateForm" @submit.prevent="submitForm">
              <div v-if="!isEdit" class="mb-6">
                <TextAreaInput
                  label="Observações"
                  name="observations"
                  v-model="form.observations"
                  :placeholder="isDebit ? 'Detalhes do pagamento' : 'Detalhes do recebimento'"
                  :rows="4"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label
                    for="invoice"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Fatura</label
                  >
                  <TextValue v-model="invoiceDisplay" class="selected" />
                </div>
                <div>
                  <label
                    for="price"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Valor</label
                  >
                  <money-input
                    name="price"
                    v-model="form.amount"
                    placeholder="0,00"
                    class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-base-content/20"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div v-if="form.method === 'credit_card'">
                  <label
                    for="credit_card_id"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Cartão de Crédito</label
                  >
                  <select
                    id="credit_card_id"
                    v-model="form.credit_card_id"
                    class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-base-content/20"
                  >
                    <option
                      v-for="card in creditCards"
                      :key="card.id"
                      :value="card.id"
                      class="text-base-content"
                    >
                      {{ card.name }} - final {{ card.last_digits }}
                    </option>
                  </select>
                </div>
                <div v-else>
                  <label
                    for="bank_account_id"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Conta Bancária</label
                  >
                  <select
                    id="bank_account_id"
                    v-model="form.bank_account_id"
                    class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-base-content/20"
                  >
                    <option
                      v-for="account in bankAccounts"
                      :key="account.id"
                      :value="account.id"
                      class="text-base-content"
                    >
                      {{ account.account_name }} - {{ account.bank_name }}
                    </option>
                  </select>
                </div>
                <div>
                  <label
                    for="method"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Método de Pagamento</label
                  >
                  <select
                    id="method"
                    v-model="form.method"
                    class="w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 ease-in-out hover:border-base-content/20"
                  >
                    <option value="pix" class="text-base-content">PIX</option>
                    <option value="bank_transfer" class="text-base-content">
                      Transferência Bancária
                    </option>
                    <option value="cash" class="text-base-content">Dinheiro</option>
                    <option value="credit_card" class="text-base-content">
                      Cartão de Crédito
                    </option>
                    <option value="debit_card" class="text-base-content">
                      Cartão de Débito
                    </option>
                    <option value="check" class="text-base-content">Cheque</option>
                  </select>
                </div>
              </div>

              <div v-if="isEdit" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <label
                    for="transaction_date"
                    class="block text-sm font-semibold text-base-content mb-2"
                    >Data</label
                  >
                  <DatePicker
                    id="transaction_date"
                    v-model="editDate"
                    format="dd/MM/yyyy"
                    :enable-time-picker="false"
                    :clearable="false"
                  />
                </div>
              </div>

              <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                    label="Data"
                    name="transaction_date"
                    :autoFillNow="true"
                    @update="updateForm"
                  />
                </div>
              </div>

              <div v-if="errorMessage" class="mb-6">
                <div class="w-full">
                  <p class="error text-error">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>

            </form>

    <template #footer>
      <DeleteIconButton
        v-if="isEdit"
        class="me-auto"
        label="Excluir"
        :title="isDebit ? 'Excluir pagamento' : 'Excluir recebimento'"
        :confirm-message="isDebit ? 'Tem certeza que deseja excluir este pagamento?' : 'Tem certeza que deseja excluir este recebimento?'"
        @confirm="deleteTransaction"
      />
      <button type="button" class="btn btn-ghost" @click="closeModal">
        Cancelar
      </button>
      <button type="submit" form="transactionCreateForm" class="btn btn-primary">
        {{ isEdit ? "Salvar" : isDebit ? "Pagar" : "Receber" }}
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { submitFormCreate, index, show, update, destroy } from "@/utils/requests/httpUtils";
import DateInput from "./inputs/date/DateInput.vue";
import DatePicker from "./inputs/date/DatePicker.vue";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import TextValue from "../fields/text/TextValue.vue";
import UsersSelectInput from "./selects/UsersSelectInput.vue";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import DeleteIconButton from "@/components/buttons/DeleteIconButton.vue";

export default {
  name: "TransactionCreateForm",
  emits: ["new-transaction-event", "transaction-updated", "transaction-deleted", "close"],
  components: {
    DateInput,
    DatePicker,
    TextAreaInput,
    TextValue,
    UsersSelectInput,
    MoneyInput,
    ModalCard,
    DeleteIconButton,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
    // Quando informado, o formulário edita esse pagamento (forma, conta/cartão e valor)
    transactionId: {
      type: Number,
      default: null,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: {
        invoice_id: this.invoice?.id || null,
        bank_account_id: null,
        credit_card_id: null,
        amount: this.invoice?.balance || 0,
        transaction_date: null,
        type: this.invoice?.type === 'debit' ? 'debit' : 'credit',
        method: "pix",
        observations: null,
      },
      errorMessage: null,
      // Modo edição: data do pagamento (só o dia) e a data original, para só enviar se mudar
      editDate: null,
      originalDate: null,
      bankAccounts: [],
      creditCards: [],
    };
  },
  computed: {
    isDebit() {
      return this.form.type === "debit";
    },
    isEdit() {
      return !!this.transactionId;
    },
    modalTitle() {
      if (this.isEdit) {
        return this.isDebit ? "Editar Pagamento" : "Editar Recebimento";
      }
      return this.isDebit ? "Novo Pagamento" : "Novo Recebimento";
    },
    invoiceDisplay() {
      return `Fatura #${this.invoice?.id || "..."}`;
    },
  },
  watch: {
    invoice: {
      handler(newInvoice) {
        if (newInvoice && newInvoice.id) {
          console.log("Invoice no watcher:", {
            id: newInvoice.id,
            price: newInvoice.price,
            total_paid: newInvoice.total_paid,
            balance: newInvoice.balance,
            calculado: newInvoice.price - (newInvoice.total_paid || 0)
          });
          this.form.invoice_id = newInvoice.id;
          if (!this.isEdit) {
            this.form.amount = newInvoice.balance || 0;
          }
          // Atualiza o tipo de transação baseado no tipo da fatura
          this.form.type = newInvoice.type === 'debit' ? 'debit' : 'credit';
        }
      },
      deep: true,
      immediate: true,
    },
    "form.method"(newMethod) {
      if (newMethod === "credit_card") {
        this.form.bank_account_id = null;
        if (this.creditCards.length > 0 && !this.form.credit_card_id) {
          this.form.credit_card_id = this.creditCards[0].id;
        }
      } else if (this.bankAccounts.length > 0 && !this.form.bank_account_id) {
        this.form.bank_account_id = this.bankAccounts[0].id;
      }
    },
  },
  methods: {
    submitFormCreate,
    index,
    async getTransaction() {
      try {
        const transaction = await show("transactions", this.transactionId);
        // Cartão/conta antes do método, para o watcher de method não sobrescrever
        this.form.credit_card_id = transaction.credit_card_id ?? null;
        this.form.bank_account_id = transaction.bank_account_id;
        this.form.amount = transaction.amount;
        this.form.type = transaction.type;
        this.form.method = transaction.method;
        this.editDate = this.parseLocalDate(transaction.transaction_date);
        this.originalDate = this.toDateString(this.editDate);
      } catch (error) {
        this.errorMessage = "Erro ao carregar o pagamento.";
      }
    },
    async getBankAccounts() {
      try {
        this.bankAccounts = await this.index("bank_accounts");
        // Seleciona automaticamente a primeira conta se não houver uma já selecionada
        if (this.bankAccounts.length > 0 && !this.form.bank_account_id && this.form.method !== "credit_card") {
          this.form.bank_account_id = this.bankAccounts[0].id;
        }
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
        this.bankAccounts = [];
      }
    },
    async getCreditCards() {
      try {
        this.creditCards = await this.index("credit_cards", { is_active: 1 });
        if (this.creditCards.length > 0 && !this.form.credit_card_id && this.form.method === "credit_card") {
          this.form.credit_card_id = this.creditCards[0].id;
        }
      } catch (error) {
        console.error("Erro ao carregar cartões de crédito:", error);
        this.creditCards = [];
      }
    },
    closeModal() {
      this.$emit("close");
      this.errorMessage = null;
    },
    async submitForm() {
      if (this.isEdit) {
        return this.submitUpdate();
      }

      // Adicionar timezone do navegador
      const formWithTimezone = {
        ...this.form,
        user_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
      };

      const { data, error } = await this.submitFormCreate(
        "transactions",
        formWithTimezone
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-transaction-event", data);
      }
      if (error) {
        this.errorMessage = "Erro ao criar transação. Tente novamente.";
        console.error("Erro:", error);
      }
    },
    async submitUpdate() {
      try {
        const payload = {
          method: this.form.method,
          bank_account_id: this.form.method === "credit_card" ? null : this.form.bank_account_id,
          credit_card_id: this.form.method === "credit_card" ? this.form.credit_card_id : null,
          amount: this.form.amount,
        };
        // Data só vai se mudou, para não perder o horário original do pagamento
        const newDate = this.toDateString(this.editDate);
        if (newDate && newDate !== this.originalDate) {
          payload.transaction_date = newDate;
        }
        const data = await update("transactions", this.transactionId, payload);
        this.$emit("close");
        this.$emit("transaction-updated", data);
      } catch (error) {
        this.errorMessage =
          error.response?.data?.message || "Erro ao salvar o pagamento. Tente novamente.";
      }
    },
    async deleteTransaction() {
      try {
        this.errorMessage = null;
        await destroy("transactions", this.transactionId);
        this.$emit("close");
        this.$emit("transaction-deleted", this.transactionId);
      } catch (error) {
        // 422: lançamento no cartão já está em fatura fechada
        this.errorMessage =
          error.response?.data?.message || "Erro ao excluir o pagamento. Tente novamente.";
      }
    },
    // "YYYY-MM-DD HH:mm:ss" (horário local do usuário) -> Date local, só o dia
    parseLocalDate(value) {
      if (!value) return null;
      const [year, month, day] = value.toString().slice(0, 10).split("-").map(Number);
      return new Date(year, month - 1, day);
    },
    toDateString(date) {
      if (!(date instanceof Date)) return null;
      const pad = (n) => n.toString().padStart(2, "0");
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    },
    updateForm(field, value) {
      this.form[field] = value;
    },
  },
  mounted() {
    if (this.isEdit) {
      this.getTransaction();
    }
    this.getBankAccounts();
    this.getCreditCards();
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
  border-color: color-mix(in oklab, var(--color-base-content) 50%, transparent);
  cursor: not-allowed;
}
</style>