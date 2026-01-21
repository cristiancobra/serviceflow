<template>
  <div class="mb-20 mt-8 px-8">
    <div class="flex justify-center items-center mb-8">
      <div class="flex-1 flex justify-start text-primary mb-4">
        <font-awesome-icon
          icon="fa-solid fa-money-bill"
          class="text-sm font-extrabold text-white bg-primary rounded-full mr-4 p-3"
        />
        <h2 class="text-xl font-extrabold text-left mt-1">PROPOSTAS</h2>
      </div>
      <div class="flex-[2] flex justify-center">
        <ProposalCreateForm
          @new-proposal-event="addProposalCreated"
          :opportunityId="opportunityId"
        />
      </div>
    </div>

    <div class="w-full">
      <search-input
        v-model="searchTerm"
        placeholder="Digite para buscar propostas"
      />
    </div>

    <div
      v-for="proposal in proposals"
      v-bind:key="proposal.id"
      class="flex items-start justify-start text-left border-b border-gray-300"
    >
      <div class="flex flex-1 items-center justify-start mr-4" id="col-user">
        <select-status-button
          :status="proposal.status"
          @update:modelValue="updateProposal('status', proposal.id, $event)"
        />
      </div>
      <router-link
        class="flex w-full no-underline text-inherit"
        :to="{ name: 'proposalShow', params: { id: proposal.id } }"
      >
        <div class="text-black flex flex-[2] items-center justify-start mr-4">
          {{ formatDateBr(proposal.date) }}
        </div>
        <div class="flex flex-[6] items-center justify-start flex-row m-0">
          <p
            class="text-black text-left text-sm font-medium p-0 m-0"
            v-if="!proposal.opportunity"
          >
            sem oportunidade associada
          </p>
          <p
            class="text-black text-sm font-semibold"
            v-else-if="proposal.opportunity?.company?.business_name"
          >
            {{ proposal.opportunity.company.business_name }}
          </p>
          <p
            class="text-black text-sm font-semibold"
            v-else-if="proposal.opportunity?.company?.legal_name"
          >
            {{ proposal.opportunity.company.legal_name }}
          </p>
          <p
            class="text-black text-sm font-semibold"
            v-else-if="proposal.opportunity?.lead?.name"
          >
            {{ proposal.opportunity.lead.name }}
          </p>
          <p class="text-black text-left text-sm font-medium p-0 m-0" v-else>
            sem associação
          </p>
        </div>
        <div class="flex flex-[6] items-center justify-start flex-row m-0">
          <p
            v-html="getShortDescription(proposal)"
            class="text-black text-left text-sm font-medium p-0 m-0 ps-2"
          ></p>
        </div>
        <div class="justify-end text-right text-base font-normal">
          <money-field name="total_price" v-model="proposal.total_price" />
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import { useProposals } from "@/composables/useProposals";
import { formatDateBr } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import MoneyField from "../fields/number/MoneyField.vue";
import ProposalCreateForm from "../forms/ProposalCreateForm.vue";
import SelectStatusButton from "../buttons/SelectStatusButton.vue";
import SearchInput from "../filters/SearchInput.vue";

export default {
  setup(props) {
    const { proposals, fetchProposals, updateProposal, addProposal } =
      useProposals(props.opportunityId);

    return {
      proposals,
      fetchProposals,
      updateProposal,
      addProposal,
    };
  },

  components: {
    ProposalCreateForm,
    MoneyField,
    SelectStatusButton,
    SearchInput,
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
    };
  },
  methods: {
    formatDateBr,
    getDeadlineClass,
    addProposalCreated(newProposal) {
      this.addProposal(newProposal);
    },

    getShortDescription(proposal, maxLength = 50) {
      let description = "";
      if (proposal.description) {
        description = proposal.description.trim();
      } else if (proposal.opportunity) {
        description =
          proposal?.opportunity?.description?.trim?.() ?? "sem descrição";
      } else {
        return "---";
      }

      if (description.length > maxLength) {
        return description.substring(0, maxLength) + "...";
      }
      return description;
    },
    toggleForm() {
      this.isActive = !this.isActive;
    },
  },
  mounted() {
    this.fetchProposals();
  },
};
</script>