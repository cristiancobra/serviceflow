<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-money-bill" class="page-icon" />
        <h1>PROPOSTAS</h1>
      </div>
      <div class="page-action">
        <!-- <ProposalCreateForm
          @new-proposal-event="addProposalCreated"
          :opportunityId="opportunityId"
        /> -->
      </div>
    </div>

    <section class="section-container">
      <!-- Search Bar -->
      <div class="mb-4">
        <div class="relative">
          <input
            type="text"
            v-model="searchTerm"
            placeholder="Buscar por empresa, oportunidade ou descrição..."
            class="w-full px-4 py-3 pl-12 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          />
          <font-awesome-icon 
            icon="fa-solid fa-search" 
            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"
          />
        </div>
      </div>

      <!-- Table Container -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <!-- Table Header -->
        <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gradient-to-r from-gray-100 to-gray-50 border-b-2 border-gray-300 font-semibold text-sm text-gray-700">
          <div class="col-span-1 text-center">Status</div>
          <div class="col-span-1 text-center">Data</div>
          <div class="col-span-2 text-left">Empresa/Lead</div>
          <div class="col-span-2 text-left">Oportunidade</div>
          <div class="col-span-3 text-left">Descrição</div>
          <div class="col-span-1 text-center">Valor</div>
          <div class="col-span-2 text-center">Valor Pago</div>
        </div>

        <!-- Table Body -->
        <div v-if="filteredProposals.length === 0" class="py-12 text-center">
          <font-awesome-icon icon="fa-solid fa-inbox" class="text-gray-300 text-5xl mb-4" />
          <p class="text-gray-500 text-lg">Nenhuma proposta encontrada</p>
        </div>

        <div
          v-for="(proposal, index) in filteredProposals"
          :key="proposal.id"
          class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-150 cursor-pointer items-center"
          :class="{ 'bg-gray-50': index % 2 === 0 }"
          @click="$router.push({ name: 'proposalShow', params: { id: proposal.id } })"
        >
          <!-- Status -->
          <div class="col-span-1 flex justify-center">
            <proposal-status-badge :proposal="proposal" />
          </div>

          <!-- Data -->
          <div class="col-span-1 text-center text-gray-700 text-sm font-medium">
            {{ formatDateBr(proposal.date) }}
          </div>

          <!-- Empresa/Lead -->
          <div class="col-span-2 text-left">
            <p v-if="!proposal.opportunity" class="text-gray-400 text-sm italic">
              sem oportunidade associada
            </p>
            <p
              v-else-if="proposal.opportunity?.company?.business_name"
              class="text-gray-800 font-semibold text-sm truncate"
            >
              {{ proposal.opportunity.company.business_name }}
            </p>
            <p
              v-else-if="proposal.opportunity?.company?.legal_name"
              class="text-gray-800 font-semibold text-sm truncate"
            >
              {{ proposal.opportunity.company.legal_name }}
            </p>
            <p 
              v-else-if="proposal.opportunity?.lead?.name"
              class="text-gray-800 font-semibold text-sm truncate"
            >
              {{ proposal.opportunity.lead.name }}
            </p>
            <p v-else class="text-gray-400 text-sm italic">sem associação</p>
          </div>

          <!-- Oportunidade -->
          <div class="col-span-2 text-left">
            <p class="text-gray-700 text-sm truncate">
              {{ proposal.opportunity?.name || '---' }}
            </p>
          </div>

          <!-- Descrição -->
          <div class="col-span-3 text-left">
            <p v-html="getShortDescription(proposal)" class="text-gray-600 text-sm truncate"></p>
          </div>

          <!-- Valor -->
          <div class="col-span-1 text-center">
            <money-field 
              name="total_price" 
              v-model="proposal.total_price"
              class="text-gray-800 font-semibold text-sm"
            />
          </div>

          <!-- Valor Pago -->
          <div class="col-span-2 text-right">
            <money-field 
              name="total_paid" 
              :modelValue="getTotalPaid(proposal)" 
              :class="{
                'text-blue-600 font-bold': getTotalPaid(proposal) < proposal.total_price && !proposal.paid_at,
                'text-gray-800 font-bold': proposal.paid_at,
              }"
              class="text-sm"
              readonly 
            />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, PROPOSALS_BY_OPPORTUNITY_URL } from "@/config/apiConfig";
import { formatDateBr } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import { index, updateField } from "@/utils/requests/httpUtils";
import MoneyField from "../fields/number/MoneyField.vue";
import ProposalStatusBadge from "../badges/ProposalStatusBadge.vue";

export default {
  components: {
    MoneyField,
    ProposalStatusBadge,
  },
  props: {
    opportunityId: {
      type: Number,
      required: false,
    },
    filterStatus: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      isActive: true,
      searchTerm: "",
      allProposals: [],
    };
  },
  computed: {
    filteredProposals() {
      let filtered = this.allProposals;

      // Filtrar apenas por termo de busca (status já vem filtrado do backend)
      if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(proposal => {
          return (
            proposal.opportunity?.name?.toLowerCase().includes(term) ||
            proposal.opportunity?.company?.business_name?.toLowerCase().includes(term) ||
            proposal.opportunity?.company?.legal_name?.toLowerCase().includes(term) ||
            proposal.opportunity?.lead?.name?.toLowerCase().includes(term) ||
            proposal.description?.toLowerCase().includes(term)
          );
        });
      }

      return filtered;
    }
  },
  methods: {
    formatDateBr,
    getDeadlineClass,
    updateField,
    addProposalCreated(newProposal) {
      this.proposals.unshift(newProposal);
    },
    getShortDescription(proposal, maxLength = 50) {
      let description = "";
      if (proposal.description) {
        description = proposal.description.trim();
      } else if (proposal.opportunity.description) {
        description = proposal?.opportunity?.description?.trim?.() ?? "sem descrição";
      } else {
        return "---";
      }

      if (description.length > maxLength) {
        return description.substring(0, maxLength) + "...";
      }
      return description;
    },
    async getProposalsFromOpportunity(page = 1) {
      this.proposalsUrl = `${BACKEND_URL}${PROPOSALS_BY_OPPORTUNITY_URL}opportunity_id=${this.opportunityId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(this.proposalsUrl);

        this.allProposals = response.data.data;

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };
      } catch (error) {
        console.error("Erro ao acessar propostas:", error);
      }
    },
    async getProposals() {
      const params = {};
      
      // Adiciona o filtro de status se houver
      if (this.filterStatus) {
        params.status = this.filterStatus;
      }
      
      this.allProposals = await index("proposals", params);
    },
    async updateProposal(fieldName, proposalId, editedValue) {
      const updatedProposal = await updateField(
        "proposals",
        proposalId,
        fieldName,
        editedValue
      );
      const proposalIndex = this.allProposals.findIndex(
        (proposal) => proposal.id === proposalId
      );
      if (proposalIndex !== -1) {
        this.allProposals[proposalIndex] = updatedProposal;
      }
    },
    toggleForm() {
      this.isActive = !this.isActive;
    },
    getTotalPaid(proposal) {
      if (!proposal.invoices || proposal.invoices.length === 0) {
        return 0;
      }
      // Soma apenas o total_paid das invoices de crédito (recebimento)
      return proposal.invoices
        .filter(invoice => invoice.type === 'credit')
        .reduce((total, invoice) => total + (Number(invoice.total_paid) || 0), 0);
    },
  },
  watch: {
    filterStatus(newStatus, oldStatus) {
      // Recarrega as propostas quando o filtro mudar
      if (newStatus !== oldStatus) {
        this.getProposals();
      }
    }
  },
  mounted() {
    if (this.opportunityId) {
      this.getProposalsFromOpportunity();
    } else {
      this.getProposals();
    }
  },
};
</script>