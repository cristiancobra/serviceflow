<template>
    <div class="section-container">
        <div class="section-header">
            <div class="section-title">
                <font-awesome-icon icon="fas fa-coins" class="icon" />
                <h2>
                    Custos de produção
                </h2>
                </div>
                <div class="section-actions flex items-center gap-2">
                <button
                    type="button"
                    title="Novo Custo"
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:opacity-90 text-white transition-all duration-200"
                    @click="openCreateCostModal"
                >
                    <font-awesome-icon icon="fa-solid fa-plus" class="text-lg" />
                </button>
                <button
                    type="button"
                    title="Adicionar Custos"
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:opacity-90 text-white transition-all duration-200"
                    @click="openAddProposalCostsModal"
                >
                    <font-awesome-icon icon="fa-solid fa-coins" class="text-lg" />
                </button>
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
                    @update:modelValue="emitUpdateProposalCost('price', localCost.cost_id, $event)"
                />
            </div>
            <div class="w-[25%] flex items-center color-primary-500 justify-end font-bold pr-2 text-sm">
                <money-field name="total_price" v-model="localCost.total_price" />
            </div>
        </div>
    </div>
</template>

<script>
import { mapMutations } from "vuex";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import MoneyEditableField from "@/components/fields/number/MoneyEditableField.vue";
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";

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
    MoneyField,
    MoneyEditableField,
    IntegerEditableField,
  },
  methods: {
    ...mapMutations(["openModal"]),
    openCreateCostModal() {
      this.openModal({
        component: "CostCreateForm",
        listeners: {
          // O custo é criado no catálogo geral; precisa ser associado a esta
          // proposta depois, em "Adicionar Custos" (não entra direto em localCosts).
          "new-cost-event": () => {},
        },
      });
    },
    openAddProposalCostsModal() {
      this.openModal({
        component: "ProposalCostCreateForm",
        props: { proposalId: this.proposal.id },
        listeners: {
          "new-proposal-cost-event": this.addProposalCostCreated,
        },
      });
    },
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