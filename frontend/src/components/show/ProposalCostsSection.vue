<template>
    <div class="mt-8 mb-20 px-8">
        <section-header title="Custos de produção" icon="fas fa-coins">
          <template #actions>
            <button
                type="button"
                title="Novo Custo"
                class="btn btn-primary btn-circle"
                @click="openCreateCostModal"
            >
                <font-awesome-icon icon="fa-solid fa-plus" class="text-lg" />
            </button>
            <button
                type="button"
                title="Adicionar Custos"
                class="btn btn-primary btn-circle"
                @click="openAddProposalCostsModal"
            >
                <font-awesome-icon icon="fa-solid fa-coins" class="text-lg" />
            </button>
          </template>
        </section-header>
        
        <!-- Cabeçalho das colunas -->
        <div class="flex w-full text-xs text-base-content/70 font-semibold pb-2 pt-2 border-b border-base-300 bg-base-200">
            <div class="w-[40%] ps-2 text-left">Custo</div>
            <div class="w-[15%] text-center">Quantidade</div>
            <div class="w-[20%] text-center">Preço unitário</div>
            <div class="w-[25%] text-center">Total</div>
        </div>
        <div class="flex w-full py-2 border-b border-base-200 hover:bg-base-200 text-sm" 
        v-for="localCost in localCosts" 
        v-bind:key="localCost.id"
        :class="{ 'highlight': highlightProposalCostIds.includes(localCost.cost_id) }">
            <div class="w-[40%] flex items-center ps-2 gap-2">
                <font-awesome-icon icon="fa-solid fa-coins" class="primary text-base-content text-xs flex-shrink-0" />
                <p class="text-base-content truncate text-sm">
                    {{ localCost.name }}
                </p>
            </div>
            <div class="w-[15%] flex items-center justify-center text-base-content text-sm">
                <integer-editable-field
                    v-model="localCost.quantity"
                    @save="emitUpdateProposalCost('quantity', localCost.cost_id, $event)"
                />
            </div>
            <div class="w-[20%] flex items-center justify-end text-base-content pr-2 text-sm">
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
import SectionHeader from "@/components/layout/SectionHeader.vue";

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
    SectionHeader,
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