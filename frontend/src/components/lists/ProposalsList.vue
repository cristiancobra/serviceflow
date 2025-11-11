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
      <div class="search-container">
        <input
          type="text"
          class="search-input"
          v-model="searchTerm"
          placeholder="Digite para buscar"
        />
      </div>

      <div
        v-for="proposal in proposals"
        v-bind:key="proposal.id"
        class="list-line flex w-full"
      >
        <div class="w-1/10 flex justify-center" id="col-user">
          <select-status-button
            :status="proposal.status"
            @update:modelValue="updateProposal('status', proposal.id, $event)"
          />
        </div>
        <router-link
          class="list-line-link flex flex-1"
          :to="{ name: 'proposalShow', params: { id: proposal.id } }"
        >
          <div class="w-1/9 text-center text-black">
            {{ formatDateBr(proposal.date) }}
          </div>
          <div class="w-3/9 column-name text-black">
            <p class="name" v-if="!proposal.opportunity">
              sem oportunidade associada
            </p>
            <p
              class="group-name text-black"
              v-else-if="proposal.opportunity?.company?.business_name"
            >
              {{ proposal.opportunity.company.business_name }}
            </p>
            <p
              class="group-name text-black"
              v-else-if="proposal.opportunity?.company?.legal_name"
            >
              {{ proposal.opportunity.company.legal_name }}
            </p>
            <p class="group-name text-black" v-else-if="proposal.opportunity?.lead?.name">
              {{ proposal.opportunity.lead.name }}
            </p>
            <p class="text-black" v-else>sem associação</p>
          </div>
          <div class="w-4/9 text-black">
            <p v-html="getShortDescription(proposal)" class="name ps-2"></p>
          </div>
          <div class="w-1/9 column-price text-black">
            <money-field name="total_price" v-model="proposal.total_price" />
          </div>
        </router-link>
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
// import ProposalCreateForm from "../forms/ProposalCreateForm.vue";
import SelectStatusButton from "../buttons/SelectStatusButton.vue";

export default {
  components: {
    // ProposalCreateForm,
    MoneyField,
    SelectStatusButton,
  },
  props: {
    opportunityId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      isActive: true,
      searchTerm: "",
      proposals: [],
      filteredProposals: [],
    };
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
        description = proposal.opportunity.description.trim();
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

        this.proposals = response.data.data;

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };
      } catch (error) {
        console.error("Erro ao acessar propostas:", error);
      }
    },
    async getProposals() {
      this.proposals = await index("proposals");
      console.log("proposals", this.proposals);
    },
    async updateProposal(fieldName, proposalId, editedValue) {
      const updatedProposal = await updateField(
        "proposals",
        proposalId,
        fieldName,
        editedValue
      );
      const proposalIndex = this.proposals.findIndex(
        (proposal) => proposal.id === proposalId
      );
      if (proposalIndex !== -1) {
        this.proposals[proposalIndex] = updatedProposal;
      }
    },
    toggleForm() {
      this.isActive = !this.isActive;
    },
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