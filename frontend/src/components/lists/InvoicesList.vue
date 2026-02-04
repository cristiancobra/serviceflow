<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon
          icon="fa-solid fa-file-invoice-dollar"
          class="page-icon"
        />
        <h1>FATURAS</h1>
      </div>
      <div class="page-action">
        <!-- <InvoiceCreateForm
          @new-invoice-event="addInvoiceCreated"
          :proposalId="proposalId"
        /> -->
      </div>
    </div>

    <section class="section-container">
      <div class="w-full mb-6">
        <search-input
          v-model="searchTerm"
          placeholder="Digite para buscar faturas"
        />
      </div>

      <!-- Filtros de Tipo -->
      <div class="flex items-center gap-3 mb-6">
        <span class="text-sm font-semibold text-gray-700">Filtrar por tipo:</span>
        <button
          @click="typeFilter = null"
          :class="{
            'bg-gray-800 text-white': typeFilter === null,
            'bg-gray-200 text-gray-700 hover:bg-gray-300': typeFilter !== null,
          }"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          Todos
        </button>
        <button
          @click="typeFilter = 'credit'"
          :class="{
            'bg-blue-600 text-white': typeFilter === 'credit',
            'bg-blue-100 text-blue-700 hover:bg-blue-200': typeFilter !== 'credit',
          }"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
        >
          <font-awesome-icon icon="fa-solid fa-arrow-up" class="text-xs" />
          Crédito
        </button>
        <button
          @click="typeFilter = 'debit'"
          :class="{
            'bg-red-600 text-white': typeFilter === 'debit',
            'bg-red-100 text-red-700 hover:bg-red-200': typeFilter !== 'debit',
          }"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
        >
          <font-awesome-icon icon="fa-solid fa-arrow-down" class="text-xs" />
          Débito
        </button>
      </div>

      <!-- Cabeçalho da Tabela -->
      <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <div class="flex items-center py-4 px-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Tipo</div>
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Status</div>
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Data</div>
          <div class="w-3/12 text-gray-700 text-center font-semibold text-sm">Oportunidade</div>
          <div class="w-2/12 text-gray-700 text-center font-semibold text-sm">Cliente</div>
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Valor</div>
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Pago</div>
          <div class="w-1/12 text-gray-700 text-center font-semibold text-sm">Saldo</div>
        </div>

        <!-- Linhas da Tabela -->
        <div v-if="filteredInvoices.length === 0" class="flex items-center justify-center py-12 px-6 bg-white">
          <p class="text-gray-500 text-sm">Nenhuma fatura encontrada</p>
        </div>

        <div v-else>
          <router-link
            v-for="(invoice, index) in filteredInvoices"
            :key="invoice.id"
            :to="{ name: 'opportunityShow', params: { id: invoice.proposal.opportunity_id } }"
            class="flex items-center py-1 px-6 border-b border-gray-100 bg-white hover:bg-blue-50 transition-colors duration-150 cursor-pointer"
            :class="{ 'bg-gray-50': index % 2 === 0 }"
          >
            <!-- Tipo -->
            <div class="w-1/12 flex justify-center">
              <span
                :class="{
                  'bg-blue-100': invoice.type === 'credit',
                  'bg-red-100': invoice.type === 'debit',
                }"
                class="w-7 h-7 flex items-center justify-center rounded-full"
              >
                <font-awesome-icon
                  :icon="invoice.type === 'credit' ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'"
                  :class="{
                    'text-blue-600': invoice.type === 'credit',
                    'text-red-600': invoice.type === 'debit',
                  }"
                  class="text-xs"
                />
              </span>
            </div>

            <!-- Status -->
            <div class="w-1/12 flex justify-center">
              <invoice-status-badge :status="invoice.status" />
            </div>

            <!-- Data -->
            <div class="w-1/12 text-center text-gray-800 text-sm font-medium">
              {{ formatDateBr(invoice.date_due) }}
            </div>

            <!-- Oportunidade -->
            <div class="w-3/12 text-center">
              <p v-if="invoice.proposal?.opportunity?.name" class="text-gray-800 text-sm font-medium truncate">
                {{ invoice.proposal.opportunity.name }}
              </p>
              <p v-else class="text-gray-400 text-sm">-</p>
            </div>

            <!-- Cliente -->
            <div class="w-2/12 text-center">
              <p v-if="!invoice.proposal" class="text-gray-500 text-sm">sem proposta</p>
              <p
                v-else-if="invoice.proposal?.opportunity?.company?.business_name"
                class="text-gray-800 text-sm truncate"
              >
                {{ invoice.proposal.opportunity.company.business_name }}
              </p>
              <p
                v-else-if="invoice.proposal?.opportunity?.company?.legal_name"
                class="text-gray-800 text-sm truncate"
              >
                {{ invoice.proposal.opportunity.company.legal_name }}
              </p>
              <p
                v-else-if="invoice.proposal?.opportunity?.lead?.name"
                class="text-gray-800 text-sm truncate"
              >
                {{ invoice.proposal.opportunity.lead.name }}
              </p>
              <p v-else class="text-gray-400 text-sm">-</p>
            </div>

            <!-- Valor -->
            <div class="w-1/12 text-center">
              <money-field name="price" v-model="invoice.price" class="text-gray-800 text-sm font-semibold" />
            </div>

            <!-- Pago -->
            <div class="w-1/12 text-center">
              <money-field name="total_paid" v-model="invoice.total_paid" class="text-success text-sm font-semibold" />
            </div>

            <!-- Saldo -->
            <div class="w-1/12 text-center">
              <money-field
                name="balance"
                :modelValue="invoice.balance"
                class="text-sm font-semibold"
                :class="{
                  'text-red-600': invoice.balance > 0,
                  'text-green-600': invoice.balance === 0,
                  'text-gray-600': invoice.balance < 0,
                }"
                readonly
              />
            </div>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL } from "@/config/apiConfig";
import { formatDateBr } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import { index, updateField } from "@/utils/requests/httpUtils";
import MoneyField from "../fields/number/MoneyField.vue";
import InvoiceStatusBadge from "../badges/InvoiceStatusBadge.vue";

export default {
  components: {
    MoneyField,
    InvoiceStatusBadge,
  },
  props: {
    proposalId: {
      type: Number,
      required: false,
    },
  },
  data() {
    return {
      isActive: true,
      searchTerm: "",
      invoices: [],
      typeFilter: null,
    };
  },
  computed: {
    filteredInvoices() {
      let filtered = this.invoices;

      if (this.typeFilter) {
        filtered = filtered.filter((invoice) => invoice.type === this.typeFilter);
      }

      if (this.searchTerm) {
        const searchLower = this.searchTerm.toLowerCase();
        filtered = filtered.filter((invoice) => {
          if (
            invoice.invoice_number &&
            invoice.invoice_number.toString().toLowerCase().includes(searchLower)
          ) {
            return true;
          }

          if (
            invoice.description &&
            invoice.description.toLowerCase().includes(searchLower)
          ) {
            return true;
          }

          if (
            invoice.proposal?.opportunity?.company?.business_name
              ?.toLowerCase()
              .includes(searchLower)
          ) {
            return true;
          }

          if (
            invoice.proposal?.opportunity?.company?.legal_name
              ?.toLowerCase()
              .includes(searchLower)
          ) {
            return true;
          }

          if (
            invoice.proposal?.opportunity?.lead?.name
              ?.toLowerCase()
              .includes(searchLower)
          ) {
            return true;
          }

          return false;
        });
      }

      return filtered;
    },
  },
  methods: {
    formatDateBr,
    getDeadlineClass,
    updateField,
    addInvoiceCreated(newInvoice) {
      this.invoices.unshift(newInvoice);
    },
    async getInvoicesFromProposal(page = 1) {
      const invoicesUrl = `${BACKEND_URL}/api/invoices?proposal_id=${this.proposalId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(invoicesUrl);

        this.invoices = response.data.data;

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };
      } catch (error) {
        console.error("Erro ao acessar faturas:", error);
      }
    },
    async getInvoices() {
      this.invoices = await index("invoices");
      console.log("invoices", this.invoices);
    },
    async updateInvoice(fieldName, invoiceId, editedValue) {
      const updatedInvoice = await updateField(
        "invoices",
        invoiceId,
        fieldName,
        editedValue
      );
      const invoiceIndex = this.invoices.findIndex(
        (invoice) => invoice.id === invoiceId
      );
      if (invoiceIndex !== -1) {
        this.invoices[invoiceIndex] = updatedInvoice;
      }
    },
    toggleForm() {
      this.isActive = !this.isActive;
    },
  },
  mounted() {
    console.log("invoice", this.invoice);
    if (this.proposalId) {
      this.getInvoicesFromProposal();
    } else {
      this.getInvoices();
    }
  },
};
</script>