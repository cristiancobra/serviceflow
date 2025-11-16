<template>
    <div class="section-container">
        <div class="section-header">
            <div class="section-title">
                <font-awesome-icon icon="fas fa-coins" class="icon" />
                <h2>
                    Custos de produção
                </h2>
                </div>
                <div class="section-actions flex items-center gap-4">
                <cost-create-form @new-cost-event="addCostCreated" />
                <proposal-cost-create-form @new-proposal-cost-event="addProposalCostCreated"
                    :proposalId="proposal.id" />
            </div>
        </div>
        
        <!-- Cabeçalho das colunas -->
        <div class="flex w-full text-xs text-gray-600 font-semibold pb-2 pt-2 border-b border-gray-300 bg-gray-100">
            <div class="w-[40%] ps-2 text-left">Custo</div>
            <div class="w-[15%] text-center">Quantidade</div>
            <div class="w-[20%] text-center">Preço unitário</div>
            <div class="w-[25%] text-center">Total</div>
        </div>
        <div class="flex w-full py-2 border-b border-gray-100 hover:bg-gray-50 text-sm" 
        v-for="localCost in localCosts" 
        v-bind:key="localCost.id"
        :class="{ 'highlight': highlightProposalCostIds.includes(localCost.cost_id) }">
            <div class="w-[40%] flex items-center ps-2 gap-2">
                <font-awesome-icon icon="fa-solid fa-coins" class="primary text-black text-xs flex-shrink-0" />
                <p class="text-black truncate text-sm">
                    {{ localCost.name }}
                </p>
            </div>
            <div class="w-[15%] flex items-center justify-center text-black text-sm">
                <integer-editable-field
                    v-model="localCost.quantity"
                    @save="emitUpdateProposalCost('quantity', localCost.cost_id, $event)"
                />
            </div>
            <div class="w-[20%] flex items-center justify-end text-black pr-2 text-sm">
                <money-editable-field
                    v-model="localCost.price"
                    @save="emitUpdateProposalCost('price', localCost.cost_id, $event)"
                />
            </div>
            <div class="w-[25%] flex items-center color-primary-500 justify-end font-bold pr-2 text-sm">
                <money-field name="total_price" v-model="localCost.total_price" />
            </div>
        </div>
    </div>
</template>

<script>
import CostCreateForm from "@/components/forms/CostCreateForm.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import ProposalCostCreateForm from "../forms/ProposalCostCreateForm.vue";

export default {
  props: {
    proposal: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      localCosts: this.proposal.proposalCosts,
      highlightProposalCostIds: [],
    };
  },
  components: {
    CostCreateForm,
    MoneyField,
    MoneyEditableField,
    IntegerEditableField,
    ProposalCostCreateForm,
  },
  methods: {
    addProposalCostCreated({ proposalCosts, newTotalThirdPartyCost }) {
      proposalCosts.forEach((newCost) => {
        const index = this.localCosts.findIndex(
          (cost) => cost.id === newCost.id
        );
        if (index !== -1) {
          // Substituir o valor existente
          this.localCosts[index] = newCost;
        } else {
          // Adicionar novo item
          this.localCosts.unshift(newCost);
        }
        this.highlight(newCost.id);
        this.$emit("update-total-third-party-cost", newTotalThirdPartyCost);
      });
    },
    highlight(proposalCostId) {
      this.highlightProposalCostIds.push(proposalCostId);
      setTimeout(() => {
        this.highlightProposalCostIds = this.highlightProposalCostIds.filter(
          (id) => id !== proposalCostId
        );
      }, 2000);
    },
    async emitUpdateProposalCost(fieldName, costId, editedValue) {
      this.$emit("update-proposal-cost", fieldName, costId, editedValue);
    },
  },
  watch: {
    proposal: {
      handler(newProposal) {
        this.localCosts = newProposal.proposalCosts;
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
</style>