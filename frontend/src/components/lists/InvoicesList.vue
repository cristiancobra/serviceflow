<template>
  <div class="page-container">
    <PageHeader title="FATURAS" icon="fa-solid fa-file-invoice-dollar">
      <template #actions>
        <!-- <InvoiceCreateForm
          @new-invoice-event="addInvoiceCreated"
          :proposalId="proposalId"
        /> -->
      </template>
    </PageHeader>

    <section class="mt-8 mb-20 px-8">
      <div class="w-full mb-6">
        <search-input v-model="searchTerm" placeholder="Digite para buscar faturas" />
      </div>

      <!-- Filtros de Tipo -->
      <div class="flex flex-wrap items-center gap-3 mb-3">
        <span class="text-sm font-semibold text-base-content">Filtrar por tipo:</span>
        <button @click="setTypeFilter(null)" :class="{
          'bg-gray-800 text-white': typeFilter === null && overdueFilter === null,
          'bg-base-300 text-base-content/80 hover:bg-base-300': !(typeFilter === null && overdueFilter === null),
        }" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Todos
        </button>
        <button @click="setTypeFilter('credit')" :class="{
          'bg-info text-white': typeFilter === 'credit',
          'bg-info/10 text-info hover:bg-info/10': typeFilter !== 'credit',
        }" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-arrow-up" class="text-xs" />
          Crédito
        </button>
        <button @click="setTypeFilter('debit')" :class="{
          'bg-error text-white': typeFilter === 'debit',
          'bg-error/10 text-error hover:bg-error/10': typeFilter !== 'debit',
        }" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-arrow-down" class="text-xs" />
          Débito
        </button>
        <button @click="setOverdueFilter('overdue_credit')" :class="{
          'bg-info text-white': overdueFilter === 'overdue_credit',
          'bg-info/10 text-info hover:bg-info/10': overdueFilter !== 'overdue_credit',
        }" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-xs" />
          Crédito Vencidas
        </button>
        <button @click="setOverdueFilter('overdue_debit')" :class="{
          'bg-error text-white': overdueFilter === 'overdue_debit',
          'bg-error/10 text-error hover:bg-error/10': overdueFilter !== 'overdue_debit',
        }" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
          <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="text-xs" />
          Débito Vencidas
        </button>
      </div>



      <!-- Estado Vazio -->
      <div v-if="filteredInvoices.length === 0" class="flex items-center justify-center py-12 px-6 bg-base-100 rounded-lg border border-base-300">
        <p class="text-base-content/60 text-sm">Nenhuma fatura encontrada</p>
      </div>

      <!-- Faturas agrupadas por mês -->
      <div v-for="monthGroup in groupedInvoices" :key="monthGroup.monthKey" class="mb-8">
        <!-- Header do Mês -->
        <div class="flex items-center mb-4 sticky top-0 z-10">
          <div class="flex items-center gap-3 bg-base-100 pe-6 pb-1 pt-10">
            <span class="font-bold text-primary text-lg whitespace-nowrap">{{ monthGroup.monthLabel }}</span>
            <span class="text-xs font-semibold text-base-content/60 bg-base-200 px-2 py-1 rounded-full">
              {{ monthGroup.invoices.length }} {{ monthGroup.invoices.length === 1 ? 'fatura' : 'faturas' }}
            </span>
          </div>
        </div>

        <!-- Tabela do Mês -->
        <div class="overflow-x-auto rounded-lg border border-base-300 shadow-sm">
          <div class="flex items-center py-4 px-6 border-b border-base-300 bg-gradient-to-r from-base-200 to-base-200">
            <div class="w-12 text-base-content/80 text-center font-semibold text-sm">Tipo</div>
            <div class="w-1/12 text-base-content/80 text-center font-semibold text-sm">Status</div>
            <div class="w-1/12 text-base-content/80 text-center font-semibold text-sm">Data</div>
            <div class="w-3/12 text-base-content/80 text-center font-semibold text-sm">Oportunidade</div>
            <div class="w-2/12 text-base-content/80 text-center font-semibold text-sm">Cliente</div>
            <div class="w-1/12 text-base-content/80 text-center font-semibold text-sm">Valor</div>
            <div class="w-1/12 text-base-content/80 text-center font-semibold text-sm">Pago</div>
            <div class="w-1/12 text-base-content/80 text-center font-semibold text-sm">Saldo</div>
          </div>

          <component
            v-for="(invoice, index) in monthGroup.invoices" :key="invoice.id"
            :is="invoice.proposal?.opportunity_id ? 'router-link' : 'div'"
            :to="invoice.proposal?.opportunity_id ? { name: 'opportunityShow', params: { id: invoice.proposal.opportunity_id } } : undefined"
            class="flex items-center py-1 px-6 border-b border-base-200 bg-base-100 hover:bg-info/10 transition-colors duration-150 cursor-pointer"
            :class="{ 'bg-base-200': index % 2 === 0 }">
            <!-- Tipo -->
            <div class="w-12 flex justify-center">
              <span :class="{
                'bg-info/10': invoice.type === 'credit',
                'bg-error/10': invoice.type === 'debit',
              }" class="w-7 h-7 flex items-center justify-center rounded-full">
                <font-awesome-icon :icon="invoice.type === 'credit' ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'"
                  :class="{
                    'text-info': invoice.type === 'credit',
                    'text-error': invoice.type === 'debit',
                  }" class="text-xs" />
              </span>
            </div>

            <!-- Status -->
            <div class="w-1/12 flex justify-center">
              <invoice-status-badge :status="invoice.status" />
            </div>

            <!-- Data -->
            <div class="w-1/12 text-center text-base-content text-sm font-medium">
              {{ formatDateBr(invoice.date_due) }}
            </div>

            <!-- Oportunidade -->
            <div class="w-3/12 text-center">
              <p v-if="invoice.proposal?.opportunity?.name" class="text-base-content text-sm font-medium truncate">
                {{ invoice.proposal.opportunity.name }}
              </p>
              <p v-else class="text-base-content/50 text-sm">-</p>
            </div>

            <!-- Cliente -->
            <div class="w-2/12 text-center">
              <p v-if="!invoice.proposal" class="text-base-content/60 text-sm">sem proposta</p>
              <p v-else-if="invoice.proposal?.opportunity?.company?.business_name"
                class="text-base-content text-sm truncate">
                {{ invoice.proposal.opportunity.company.business_name }}
              </p>
              <p v-else-if="invoice.proposal?.opportunity?.company?.legal_name" class="text-base-content text-sm truncate">
                {{ invoice.proposal.opportunity.company.legal_name }}
              </p>
              <p v-else-if="invoice.proposal?.opportunity?.lead?.name" class="text-base-content text-sm truncate">
                {{ invoice.proposal.opportunity.lead.name }}
              </p>
              <p v-else class="text-base-content/50 text-sm">-</p>
            </div>

            <!-- Valor -->
            <div class="w-1/12 text-center">
              <money-field name="price" v-model="invoice.price" class="text-base-content text-sm font-semibold" />
            </div>

            <!-- Pago -->
            <div class="w-1/12 text-center">
              <money-field name="total_paid" v-model="invoice.total_paid" class="text-success text-sm font-semibold" />
            </div>

            <!-- Saldo -->
            <div class="w-1/12 text-center">
              <money-field name="balance" :modelValue="invoice.balance" class="text-sm font-semibold" :class="{
                'text-error': invoice.balance > 0,
                'text-success': invoice.balance === 0,
                'text-base-content/70': invoice.balance < 0,
              }" readonly />
            </div>
          </component>
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
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
  components: {
    PageHeader,
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
      overdueFilter: null,
    };
  },
  watch: {
    overdueFilter() {
      this.getInvoices();
    },
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
    groupedInvoices() {
      const groups = {};

      this.filteredInvoices.forEach(invoice => {
        const date = new Date(invoice.date_due);
        const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        const monthLabel = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(date);

        if (!groups[monthKey]) {
          groups[monthKey] = {
            monthKey,
            monthLabel: monthLabel.charAt(0).toUpperCase() + monthLabel.slice(1),
            invoices: []
          };
        }

        groups[monthKey].invoices.push(invoice);
      });

      return Object.values(groups).sort((a, b) => b.monthKey.localeCompare(a.monthKey));
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
    setTypeFilter(value) {
      this.typeFilter = value;
      this.overdueFilter = null;
    },
    setOverdueFilter(value) {
      if (this.overdueFilter === value) {
        this.overdueFilter = null;
      } else {
        this.overdueFilter = value;
        this.typeFilter = null;
      }
    },
    async getInvoices() {
      const params = {};
      if (this.overdueFilter) {
        params.filter = this.overdueFilter;
      }
      this.invoices = await index("invoices", params);
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