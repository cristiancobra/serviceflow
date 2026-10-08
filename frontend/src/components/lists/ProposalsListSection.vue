<template>
  <div class="mb-20 mt-8 px-8">
    <!-- Sem título: a aba "Propostas" do modal da oportunidade já diz o nome da seção -->
    <div class="flex justify-end items-center mb-3">
      <button-new-form target="proposal" @open-modal="openCreateProposalModal" />
    </div>

    <div
      v-for="proposal in localProposals"
      v-bind:key="proposal.id"
      class="flex items-start justify-start text-left border-b border-base-300"
    >
      <div class="flex flex-1 items-center justify-start mr-4" id="col-user">
        <select-status-button
          :status="proposal.status"
          @update:modelValue="updateProposalStatus(proposal.id, $event)"
        />
      </div>
      <router-link
        class="flex w-full no-underline text-inherit"
        :to="{ name: 'proposalShow', params: { id: proposal.id } }"
      >
        <div class="text-base-content flex flex-[2] items-center justify-start mr-4">
          {{ formatDateBr(proposal.date) }}
        </div>
        <!-- Lista usada só dentro da oportunidade: o cliente já é o dela, então mostra as horas orçadas -->
        <div class="flex flex-[3] items-center justify-start text-sm text-base-content/80" title="Horas orçadas">
          <font-awesome-icon icon="fa-solid fa-clock" class="me-2 text-base-content/50" />
          {{ formatDuration(proposal.total_hours || 0) }}
        </div>
        <div class="flex flex-[6] items-center justify-start flex-row m-0">
          <p
            v-html="getShortDescription(proposal)"
            class="text-base-content text-left text-sm font-medium p-0 m-0 ps-2"
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
import { mapMutations } from "vuex";
import { updateField } from "@/utils/requests/httpUtils";
import { formatDateBr, formatDuration } from "@/utils/date/dateUtils";
import { getDeadlineClass } from "@/utils/card/cardUtils";
import ButtonNewForm from "../buttons/ButtonNewForm.vue";
import MoneyField from "../fields/number/MoneyField.vue";
import SelectStatusButton from "../buttons/SelectStatusButton.vue";

export default {
  components: {
    ButtonNewForm,
    MoneyField,
    SelectStatusButton,
  },
  props: {
    proposals: {
      type: Array,
      required: true,
      default: () => [],
    },
    opportunityId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      localProposals: [...this.proposals],
      searchTerm: '',
    };
  },
  watch: {
    proposals: {
      handler(newProposals) {
        this.localProposals = [...newProposals];
      },
      deep: true,
    },
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateProposalModal() {
      this.openModal({
        component: "ProposalCreateForm",
        props: { opportunityId: this.opportunityId },
        listeners: {
          "new-proposal-event": this.addProposalCreated,
        },
      });
    },
    formatDateBr,
    formatDuration,
    getDeadlineClass,
    addProposalCreated(newProposal) {
      this.localProposals.push(newProposal);
      this.$emit('proposal-added', newProposal);
    },
    async updateProposalStatus(proposalId, newStatus) {
      const updatedProposal = await updateField('proposals', proposalId, 'status', newStatus);
      
      const index = this.localProposals.findIndex(p => p.id === proposalId);
      if (index !== -1) {
        this.localProposals[index] = updatedProposal;
      }
      
      this.$emit('proposal-updated', updatedProposal);
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
  },
};
</script>