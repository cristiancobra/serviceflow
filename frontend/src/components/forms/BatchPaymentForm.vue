<template>
  <ModalCard
    title="Pagar em Lote"
    :subtitle="`${items.length} faturas em uma única movimentação`"
    icon="fa-solid fa-layer-group"
    size="lg"
    compact-size="max-w-2xl"
    :compact="compact"
    @close="$emit('close')"
  >
    <form id="batchPaymentForm" @submit.prevent="submitForm">
      <!-- Faturas e valor pago em cada uma -->
      <div class="mb-6 rounded-lg border border-base-300 divide-y divide-base-300">
        <div
          v-for="item in items"
          :key="item.invoice.id"
          class="flex items-center gap-3 px-3 py-2"
        >
          <div class="min-w-0 flex-1">
            <p class="font-semibold text-sm text-base-content truncate">
              {{ item.invoice.name || 'Fatura #' + item.invoice.id }}
            </p>
            <p class="text-xs text-base-content/60 truncate">
              {{ getSupplierName(item.invoice) }} • vence {{ formatDateBr(item.invoice.date_due) }}
              • saldo {{ formatCurrency(item.invoice.balance) }}
            </p>
          </div>
          <div class="w-36 flex-shrink-0">
            <MoneyInput
              :name="`amount_${item.invoice.id}`"
              v-model="item.amount"
              :show-currency-symbol="true"
            />
          </div>
        </div>

        <div class="flex items-center justify-between px-3 py-2 bg-base-200">
          <span class="text-sm font-bold uppercase text-base-content">Total da movimentação</span>
          <span class="text-lg font-bold text-red-600">{{ formatCurrency(total) }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <label for="batch_bank_account_id" class="block text-sm font-semibold text-base-content mb-2">
            Conta Bancária
          </label>
          <select id="batch_bank_account_id" v-model="form.bank_account_id" :class="selectClass">
            <option v-for="account in bankAccounts" :key="account.id" :value="account.id">
              {{ account.account_name }} - {{ account.bank_name }}
            </option>
          </select>
        </div>
        <div>
          <label for="batch_method" class="block text-sm font-semibold text-base-content mb-2">
            Método de Pagamento
          </label>
          <select id="batch_method" v-model="form.method" :class="selectClass">
            <option value="pix">PIX</option>
            <option value="bank_transfer">Transferência Bancária</option>
            <option value="debit_card">Cartão de Débito</option>
            <option value="cash">Dinheiro</option>
            <option value="check">Cheque</option>
          </select>
        </div>
        <div>
          <label for="batch_transaction_date" class="block text-sm font-semibold text-base-content mb-2">
            Data do Pagamento
          </label>
          <input
            id="batch_transaction_date"
            type="date"
            v-model="form.transaction_date"
            :class="selectClass"
          />
        </div>
      </div>

      <div class="mb-6">
        <label for="batch_description" class="block text-sm font-semibold text-base-content mb-2">
          Descrição
        </label>
        <input
          id="batch_description"
          type="text"
          v-model="form.description"
          maxlength="255"
          placeholder="Ex: PIX fornecedores setembro"
          :class="selectClass"
        />
      </div>

      <!-- Um Pix com o total: só gerado quando todas as faturas são do mesmo recebedor -->
      <PixPaymentCard
        v-if="form.method === 'pix'"
        class="mb-6"
        title="Pagar via Pix"
        :pix="pix"
        :loading="pixLoading"
        :error="pixError"
        is-payable
      />

      <div v-if="errorMessages.length" class="mb-6">
        <p v-for="(message, i) in errorMessages" :key="i" class="error text-red-500 text-sm">
          {{ message }}
        </p>
      </div>
    </form>

    <template #footer>
      <button
        type="button"
        class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
        @click="$emit('close')"
      >
        Cancelar
      </button>
      <button
        type="submit"
        form="batchPaymentForm"
        :disabled="isSubmitting"
        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-2 disabled:opacity-60"
      >
        <font-awesome-icon
          :icon="isSubmitting ? 'fa-solid fa-spinner' : 'fa-solid fa-check'"
          :class="{ 'animate-spin': isSubmitting }"
        />
        Pagar {{ formatCurrency(total) }}
      </button>
    </template>
  </ModalCard>
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";
import { index } from "@/utils/requests/httpUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import MoneyInput from "./inputs/money/MoneyInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";
import PixPaymentCard from "@/components/common/PixPaymentCard.vue";

/**
 * Paga várias invoices com uma única movimentação bancária (ex: um PIX que
 * quita 4 contas). Cria um payment_batch com uma transaction por invoice, de
 * modo que a listagem de movimentações mostre uma linha só, como no extrato.
 */
export default {
  name: "BatchPaymentForm",
  emits: ["batch-paid", "close"],
  components: {
    MoneyInput,
    ModalCard,
    PixPaymentCard,
  },
  props: {
    invoices: {
      type: Array,
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      items: this.invoices.map((invoice) => ({
        invoice,
        amount: Number(invoice.balance) || 0,
      })),
      form: {
        bank_account_id: null,
        method: "pix",
        transaction_date: this.today(),
        description: "",
      },
      bankAccounts: [],
      errorMessages: [],
      isSubmitting: false,
      pix: null,
      pixError: null,
      pixLoading: false,
      pixTimer: null,
      pixRequestId: 0,
      selectClass:
        "w-full px-3 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 ease-in-out hover:border-gray-400",
    };
  },
  computed: {
    total() {
      return this.items.reduce((sum, item) => sum + (Number(item.amount) || 0), 0);
    },
  },
  methods: {
    formatDateBr,
    today() {
      const d = new Date();
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value || 0);
    },
    getSupplierName(invoice) {
      if (invoice.lead?.name) return invoice.lead.name;
      if (invoice.company?.name) return invoice.company.name;
      if (invoice.proposal?.opportunity?.name) return invoice.proposal.opportunity.name;
      return "Sem fornecedor";
    },
    async getBankAccounts() {
      try {
        this.bankAccounts = await index("bank_accounts");
        if (this.bankAccounts.length > 0 && !this.form.bank_account_id) {
          this.form.bank_account_id = this.bankAccounts[0].id;
        }
      } catch (error) {
        console.error("Erro ao carregar contas bancárias:", error);
        this.bankAccounts = [];
      }
    },
    // Espera o usuário parar de digitar os valores antes de regerar o QR
    schedulePix() {
      clearTimeout(this.pixTimer);
      this.pix = null;
      this.pixError = null;
      if (this.form.method !== "pix") return;

      this.pixLoading = true;
      this.pixTimer = setTimeout(this.getPix, 500);
    },
    async getPix() {
      const requestId = ++this.pixRequestId;
      const items = this.items
        .filter((item) => Number(item.amount) > 0)
        .map((item) => ({ invoice_id: item.invoice.id, amount: Number(item.amount) }));

      if (items.length === 0) {
        this.pixError = "Informe o valor a pagar das faturas.";
        this.pixLoading = false;
        return;
      }

      try {
        const response = await axios.post(`${BACKEND_URL}invoices/pix-batch`, { items });
        if (requestId === this.pixRequestId) this.pix = response.data.data;
      } catch (error) {
        if (requestId === this.pixRequestId) {
          this.pixError = error.response?.data?.message || "Não foi possível gerar o QR Code Pix.";
        }
      } finally {
        if (requestId === this.pixRequestId) this.pixLoading = false;
      }
    },
    async submitForm() {
      this.errorMessages = [];

      if (this.items.some((item) => !(Number(item.amount) > 0))) {
        this.errorMessages = ["O valor de cada fatura deve ser maior que zero."];
        return;
      }

      this.isSubmitting = true;
      try {
        const response = await axios.post(`${BACKEND_URL}payment_batches`, {
          ...this.form,
          items: this.items.map((item) => ({
            invoice_id: item.invoice.id,
            amount: Number(item.amount),
          })),
        });

        this.$emit("batch-paid", response.data.data);
        this.$emit("close");
      } catch (error) {
        const errors = error.response?.data?.errors;
        this.errorMessages = errors
          ? [...new Set(Object.values(errors).flat())]
          : ["Erro ao registrar o pagamento. Tente novamente."];
        console.error("Erro ao pagar em lote:", error);
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  watch: {
    items: {
      handler: "schedulePix",
      deep: true,
    },
    "form.method": "schedulePix",
  },
  mounted() {
    this.getBankAccounts();
    this.schedulePix();
  },
  beforeUnmount() {
    clearTimeout(this.pixTimer);
  },
};
</script>
