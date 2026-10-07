<template>
  <div class="page-container">
    <PageHeader title="NOTAS FISCAIS" icon="fa-solid fa-file-invoice" />

    <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
      <fieldset class="fieldset">
        <legend class="fieldset-legend">Mês de emissão</legend>
        <div class="join">
          <input v-model="month" type="month" class="input join-item" @change="getNfses" />
          <button v-if="month" type="button" class="btn join-item" title="Mostrar todos os meses" @click="clearMonth">
            Todos
          </button>
        </div>
      </fieldset>

      <div v-if="nfses.length" class="text-right">
        <p class="text-sm text-base-content/60">
          {{ activeNfses.length }} {{ activeNfses.length === 1 ? "nota emitida" : "notas emitidas" }}
        </p>
        <p class="text-lg font-bold text-primary">{{ formatCurrencySymbol(total) }}</p>
      </div>
    </div>

    <error-message v-if="errorResponse" :formResponse="errorResponse" />

    <div v-if="isLoading" class="flex justify-center p-8">
      <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    <EmptyState
      v-else-if="!nfses.length"
      icon="fa-solid fa-file-invoice"
      :text="month ? 'Nenhuma nota fiscal emitida neste mês.' : 'Nenhuma nota fiscal registrada.'"
      description="As notas são registradas dentro de cada fatura a receber."
    />

    <div v-else class="overflow-x-auto rounded-lg border border-base-300">
      <table class="table">
        <thead>
          <tr>
            <th>Número</th>
            <th>Emissão</th>
            <th>Cliente</th>
            <th>Fatura</th>
            <th class="text-right">Valor</th>
            <th>Situação</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="nfse in nfses" :key="nfse.id" class="hover:bg-base-200">
            <td>
              <div class="font-semibold">{{ nfse.nfse_number || "—" }}</div>
              <div v-if="nfse.access_key" class="text-xs text-base-content/60 break-all max-w-[16rem]">
                {{ nfse.access_key }}
              </div>
            </td>
            <td class="whitespace-nowrap">{{ nfse.issued_date ? displayDate(nfse.issued_date) : "—" }}</td>
            <td>{{ nfse.invoice?.client_name || "—" }}</td>
            <td>
              <button
                v-if="nfse.invoice"
                type="button"
                class="text-primary hover:underline text-left"
                @click="openInvoiceModal(nfse.invoice.id)"
              >
                {{ nfse.invoice.name || `Fatura #${nfse.invoice.id}` }}
              </button>
            </td>
            <td class="text-right whitespace-nowrap">{{ formatCurrencySymbol(nfse.amount) }}</td>
            <td>
              <div class="flex flex-wrap gap-1">
                <span class="badge badge-sm" :class="statusBadge(nfse.status).class">
                  {{ statusBadge(nfse.status).label }}
                </span>
                <span v-if="nfse.is_manual" class="badge badge-sm badge-ghost" title="Emitida fora do sistema">
                  manual
                </span>
              </div>
            </td>
            <td class="text-right">
              <button
                v-if="nfse.has_pdf"
                type="button"
                class="btn btn-sm btn-ghost text-primary"
                title="Abrir PDF da nota"
                @click="openPdf(nfse)"
              >
                <font-awesome-icon icon="fa-solid fa-file-pdf" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapMutations } from "vuex";
import { BACKEND_URL } from "@/config/apiConfig";
import { displayDate } from "@/utils/date/dateUtils";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import PageHeader from "@/components/layout/PageHeader.vue";
import EmptyState from "@/components/layout/EmptyState.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";
import { nfseStatusBadge } from "@/utils/nfse/nfseStatus";

function currentMonth() {
  const now = new Date();
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}`;
}

export default {
  name: "NfsesIndex",
  components: {
    PageHeader,
    EmptyState,
    ErrorMessage,
  },
  data() {
    return {
      nfses: [],
      month: currentMonth(),
      isLoading: false,
      errorResponse: null,
    };
  },
  computed: {
    // Canceladas e rejeitadas não entram no total
    activeNfses() {
      return this.nfses.filter((nfse) => nfse.status === "authorized");
    },
    total() {
      return this.activeNfses.reduce((sum, nfse) => sum + Number(nfse.amount || 0), 0);
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    displayDate,
    formatCurrencySymbol,
    statusBadge: nfseStatusBadge,
    async getNfses() {
      this.isLoading = true;
      this.errorResponse = null;
      try {
        const response = await axios.get(`${BACKEND_URL}nfses`, {
          params: this.month ? { month: this.month } : {},
        });
        this.nfses = response.data.data;
      } catch (error) {
        console.error(error);
        this.errorResponse = { errors: { nfse: ["Erro ao carregar as notas fiscais. Tente novamente."] } };
      } finally {
        this.isLoading = false;
      }
    },
    clearMonth() {
      this.month = "";
      this.getNfses();
    },
    openInvoiceModal(invoiceId) {
      this.openModal({
        component: "InvoiceDetailModal",
        props: { invoiceId },
        listeners: {
          "invoice-updated": this.getNfses,
        },
        id: `invoice-${invoiceId}`,
      });
    },
    openPdf(nfse) {
      window.open(`${BACKEND_URL}nfses/${nfse.id}/pdf`, "_blank");
    },
  },
  mounted() {
    this.getNfses();
  },
};
</script>
