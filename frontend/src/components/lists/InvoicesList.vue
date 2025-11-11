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
      <div class="w-full">
        <search-input
          v-model="searchTerm"
          placeholder="Digite para buscar faturas"
        />
      </div>

      <div
        class="flex items-center py-4 px-3 border-b border-gray-200 bg-gray-50"
      >
        <div class="w-1/10 text-black text-center font-bold">Status</div>
        <div class="w-1/10 text-black text-center font-bold">Data</div>
        <div class="w-3/10 text-black text-center font-bold">Oportunidade</div>
        <div class="w-2/10 text-black text-center font-bold">Cliente</div>
        <div class="w-2/10 text-black text-center font-bold">Descrição</div>
        <div class="w-1/10 text-black text-center font-bold">Valor</div>
        <div class="w-1/10 text-black text-center font-bold">Pago</div>
        <div class="w-1/10 text-black text-center font-bold">Saldo</div>
      </div>

      <div
        v-for="invoice in filteredInvoices"
        v-bind:key="invoice.id"
        class="list-line"
      >
        <div class="w-1/10 column-icon" id="col-status">
          <invoice-status-badge :status="invoice.status" />
        </div>
        <router-link
          class="list-line-link"
          :to="{ name: 'invoiceShow', params: { id: invoice.id } }"
        >
          <div class="w-1/10 text-center text-black text-sm">
            {{ formatDateBr(invoice.date_due) }}
          </div>
          <div class="w-3/10">
            <p
              v-if="invoice.proposal?.opportunity?.name"
              class="text-black text-sm"
            >
              {{ invoice.proposal.opportunity.name }}
            </p>
            <p v-else class="text-gray-500">-</p>
          </div>
          <div class="w-2/10 text-black text-sm">
            <p class="name" v-if="!invoice.proposal">sem proposta associada</p>
            <p
              class="group-name"
              v-else-if="invoice.proposal?.opportunity?.company?.business_name"
            >
              {{ invoice.proposal.opportunity.company.business_name }}
            </p>
            <p
              class="group-name"
              v-else-if="invoice.proposal?.opportunity?.company?.legal_name"
            >
              {{ invoice.proposal.opportunity.company.legal_name }}
            </p>
            <p
              class="group-name"
              v-else-if="invoice.proposal?.opportunity?.lead?.name"
            >
              {{ invoice.proposal.opportunity.lead.name }}
            </p>
            <p class="text-black" v-else>sem associação</p>
          </div>
          <div class="w-2/10">
            <p
              v-html="getShortDescription(invoice)"
              class="text-black text-sm ps-2"
            ></p>
          </div>
          <div class="w-1/10 text-sm">
            <money-field name="price" v-model="invoice.price" />
          </div>
          <div class="w-1/10 text-sm font-bold">
            <money-field name="total_paid" v-model="invoice.total_paid" />
          </div>
          <div class="w-1/10 text-black text-sm">
            <money-field
              name="balance"
              :modelValue="invoice.price - invoice.total_paid"
              :class="{
                'text-red-600 font-bold':
                  invoice.price - invoice.total_paid > 0,
              }"
              readonly
            />
          </div>
        </router-link>
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
    };
  },
  computed: {
    filteredInvoices() {
      if (!this.searchTerm) {
        return this.invoices;
      }
      return this.invoices.filter((invoice) => {
        const searchLower = this.searchTerm.toLowerCase();

        // Busca no número da fatura
        if (
          invoice.invoice_number &&
          invoice.invoice_number.toString().toLowerCase().includes(searchLower)
        ) {
          return true;
        }

        // Busca na descrição
        if (
          invoice.description &&
          invoice.description.toLowerCase().includes(searchLower)
        ) {
          return true;
        }

        // Busca no nome da empresa/lead
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
    },
  },
  methods: {
    formatDateBr,
    getDeadlineClass,
    updateField,
    addInvoiceCreated(newInvoice) {
      this.invoices.unshift(newInvoice);
    },
    getShortDescription(invoice, maxLength = 50) {
      let description = "";
      if (invoice.description) {
        description = invoice.description.trim();
      } else if (invoice.proposal?.description) {
        description = invoice.proposal.description.trim();
      } else if (invoice.proposal?.opportunity?.description) {
        description = invoice.proposal.opportunity.description.trim();
      } else {
        return "---";
      }

      if (description.length > maxLength) {
        return description.substring(0, maxLength) + "...";
      }
      return description;
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