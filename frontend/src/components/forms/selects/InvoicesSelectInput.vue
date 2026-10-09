<template>
  <SelectInput class="text-base-content" :label="label" :name="name" v-model="localValue" :items="invoices"
    fieldsToDisplay="label" :fieldNull="fieldNull" @update:modelValue="updateInput" />
</template>

<script>
import axios from "axios";
import { BACKEND_URL, INVOICE_URL_QUERY } from "@/config/apiConfig";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import { formatDateBr } from "@/utils/date/dateUtils";
import SelectInput from "./SelectInput.vue";

// Lista as faturas em aberto (com saldo), da mais próxima do vencimento para a mais distante.
// type: 'debit' (a pagar) ou 'credit' (a receber); vazio traz as duas.
export default {
  components: {
    SelectInput,
  },
  props: {
    fieldNull: String,
    label: String,
    modelValue: null,
    name: String,
    type: String,
  },
  emits: ["update:modelValue"],
  data() {
    return {
      invoices: [],
      localValue: this.modelValue,
    };
  },
  methods: {
    async getInvoices() {
      try {
        const params = new URLSearchParams({ open: 1 });
        if (this.type) params.append("type", this.type);
        const response = await axios.get(`${BACKEND_URL}${INVOICE_URL_QUERY}${params}`);
        this.invoices = response.data.data
          .sort((a, b) => new Date(a.date_due) - new Date(b.date_due))
          .map((invoice) => ({ ...invoice, label: this.invoiceLabel(invoice) }));
      } catch (error) {
        console.error("Erro ao acessar faturas:", error);
      }
    },
    invoiceLabel(invoice) {
      const name = invoice.name || invoice.opportunity?.name || `Fatura #${invoice.id}`;
      const installment = invoice.installment_quantity > 1
        ? ` (${invoice.installment_number}/${invoice.installment_quantity})`
        : "";
      const dueDate = invoice.date_due ? formatDateBr(invoice.date_due) : "sem vencimento";
      return `${name}${installment} — vence ${dueDate} — ${formatCurrencySymbol(invoice.balance ?? invoice.price)}`;
    },
    updateInput(newValue) {
      this.$emit("update:modelValue", newValue);
    },
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
    },
  },
  mounted() {
    this.getInvoices();
  },
};
</script>
