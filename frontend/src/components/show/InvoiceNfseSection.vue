<template>
  <SectionCard title="Nota fiscal (NFS-e)">
    <template #actions>
      <button
        v-if="!showForm"
        type="button"
        class="btn btn-ghost btn-xs text-primary-content"
        @click="openForm"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Registrar nota emitida
      </button>
    </template>

    <error-message v-if="validationErrors" :formResponse="validationErrors" />

    <p v-if="!nfses.length && !showForm" class="text-sm text-base-content/60">
      Nenhuma nota fiscal registrada nesta fatura.
    </p>

    <div v-if="nfses.length" class="space-y-2" :class="{ 'mb-4': showForm }">
      <div
        v-for="nfse in nfses"
        :key="nfse.id"
        class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-base-300 bg-base-100 p-3"
      >
        <div class="min-w-0 space-y-1">
          <div class="flex flex-wrap items-center gap-2">
            <font-awesome-icon icon="fa-solid fa-file-invoice" class="text-primary" />
            <span class="font-semibold">Nota nº {{ nfse.nfse_number || "—" }}</span>
            <span class="badge badge-sm" :class="statusBadge(nfse.status).class">
              {{ statusBadge(nfse.status).label }}
            </span>
            <span v-if="nfse.is_manual" class="badge badge-sm badge-ghost" title="Emitida fora do sistema">
              manual
            </span>
          </div>
          <p class="text-sm text-base-content/70">
            {{ nfse.issued_date ? `Emitida em ${displayDate(nfse.issued_date)}` : "Sem data de emissão" }}
            · {{ formatCurrencySymbol(nfse.amount) }}
          </p>
          <p v-if="nfse.access_key" class="text-xs text-base-content/60 break-all">
            Chave: {{ nfse.access_key }}
          </p>
        </div>

        <div class="flex gap-2">
          <button
            v-if="nfse.has_pdf"
            type="button"
            class="btn btn-sm btn-primary"
            title="Abrir PDF da nota"
            @click="openPdf(nfse)"
          >
            <font-awesome-icon icon="fa-solid fa-file-pdf" />
            PDF
          </button>
          <button
            v-if="nfse.is_manual"
            type="button"
            class="btn btn-sm btn-ghost text-error"
            title="Remover registro da nota"
            :disabled="isSubmitting"
            @click="removeNfse(nfse)"
          >
            <font-awesome-icon icon="fa-solid fa-trash" />
          </button>
        </div>
      </div>
    </div>

    <form v-if="showForm" class="space-y-3" @submit.prevent="submit">
      <p class="text-sm text-base-content/60">
        Para notas emitidas fora do sistema, como no Emissor Nacional (nfse.gov.br).
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <fieldset class="fieldset">
          <legend class="fieldset-legend">Número da nota *</legend>
          <input v-model.trim="form.nfse_number" type="text" class="input w-full" maxlength="20" />
        </fieldset>
        <fieldset class="fieldset">
          <legend class="fieldset-legend">Data de emissão *</legend>
          <input v-model="form.issued_date" type="date" class="input w-full" />
        </fieldset>
        <fieldset class="fieldset">
          <legend class="fieldset-legend">Valor *</legend>
          <money-input v-model="form.amount" name="nfse_amount" show-currency-symbol class="input w-full" />
        </fieldset>
        <fieldset class="fieldset">
          <legend class="fieldset-legend">PDF da nota</legend>
          <input
            ref="pdfFile"
            type="file"
            accept="application/pdf,.pdf"
            class="file-input w-full"
            @change="form.pdf = $event.target.files[0] || null"
          />
        </fieldset>
      </div>

      <fieldset class="fieldset">
        <legend class="fieldset-legend">Chave de acesso</legend>
        <input v-model.trim="form.access_key" type="text" class="input w-full" maxlength="70" placeholder="50 dígitos" />
      </fieldset>

      <div class="flex justify-end gap-2">
        <button type="button" class="btn btn-ghost" :disabled="isSubmitting" @click="closeForm">Cancelar</button>
        <button type="submit" class="btn btn-primary" :disabled="isSubmitting || !canSubmit">
          <span v-if="isSubmitting" class="loading loading-spinner loading-sm"></span>
          <font-awesome-icon v-else icon="fa-solid fa-check" />
          Registrar nota
        </button>
      </div>
    </form>
  </SectionCard>
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";
import { displayDate } from "@/utils/date/dateUtils";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import SectionCard from "@/components/common/SectionCard.vue";
import MoneyInput from "@/components/forms/inputs/money/MoneyInput.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

const STATUS_BADGES = {
  pending: { label: "processando", class: "badge-warning" },
  authorized: { label: "emitida", class: "badge-success" },
  rejected: { label: "rejeitada", class: "badge-error" },
  cancelled: { label: "cancelada", class: "badge-neutral" },
};

function today() {
  const now = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
}

export default {
  name: "InvoiceNfseSection",
  components: {
    SectionCard,
    MoneyInput,
    ErrorMessage,
  },
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  // nfse-changed: nota registrada ou removida (quem usa recarrega a fatura)
  emits: ["nfse-changed"],
  data() {
    return {
      showForm: false,
      form: this.emptyForm(),
      isSubmitting: false,
      validationErrors: null,
    };
  },
  computed: {
    nfses() {
      return this.invoice.nfses || [];
    },
    canSubmit() {
      return this.form.nfse_number && this.form.issued_date && Number(this.form.amount) > 0;
    },
  },
  methods: {
    displayDate,
    formatCurrencySymbol,
    emptyForm() {
      return {
        nfse_number: "",
        access_key: "",
        issued_date: today(),
        amount: this.invoice?.price ?? null,
        pdf: null,
      };
    },
    statusBadge(status) {
      return STATUS_BADGES[status] || { label: status, class: "badge-ghost" };
    },
    openForm() {
      this.form = this.emptyForm();
      this.validationErrors = null;
      this.showForm = true;
    },
    closeForm() {
      this.showForm = false;
      this.validationErrors = null;
    },
    async submit() {
      const formData = new FormData();
      formData.append("nfse_number", this.form.nfse_number);
      formData.append("issued_date", this.form.issued_date);
      formData.append("amount", this.form.amount);
      if (this.form.access_key) formData.append("access_key", this.form.access_key);
      if (this.form.pdf) formData.append("pdf", this.form.pdf);

      this.isSubmitting = true;
      this.validationErrors = null;
      try {
        await axios.post(`${BACKEND_URL}invoices/${this.invoice.id}/nfses/manual`, formData);
        this.showForm = false;
        this.$emit("nfse-changed");
      } catch (error) {
        this.handleError(error, "Erro ao registrar a nota. Tente novamente.");
      } finally {
        this.isSubmitting = false;
      }
    },
    async removeNfse(nfse) {
      if (!confirm(`Remover o registro da nota nº ${nfse.nfse_number}? Isso não cancela a nota na prefeitura/Receita.`)) {
        return;
      }

      this.isSubmitting = true;
      this.validationErrors = null;
      try {
        await axios.delete(`${BACKEND_URL}nfses/${nfse.id}`);
        this.$emit("nfse-changed");
      } catch (error) {
        this.handleError(error, "Erro ao remover a nota. Tente novamente.");
      } finally {
        this.isSubmitting = false;
      }
    },
    openPdf(nfse) {
      window.open(`${BACKEND_URL}nfses/${nfse.id}/pdf`, "_blank");
    },
    handleError(error, fallbackText) {
      console.error(error);
      const errors = error.response?.status === 422
        ? error.response.data.errors || { nfse: [error.response.data.message] }
        : { nfse: [fallbackText] };
      this.validationErrors = { errors };
    },
  },
};
</script>
