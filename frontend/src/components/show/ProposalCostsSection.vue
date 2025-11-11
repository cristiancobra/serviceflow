<template>
    <div class="section-container">
        <div class="section-header">
            <div class="section-title">
                <font-awesome-icon icon="fas fa-coins" class="icon" />
                <h2>
                    Custos de propdução
                </h2>
            
                <cost-create-form @new-cost-event="addCostCreated" />
                <proposal-cost-create-form @new-proposal-cost-event="addProposalCostCreated"
                    :proposalId="proposal.id" />
            </div>
        </div>
        <div class="grid grid-cols-[3rem_1fr_6rem_8rem] gap-2 items-center border-b border-gray-200 py-2" v-for="localCost in localCosts" v-bind:key="localCost.id"
            :class="{ 'highlight': highlightProposalCostIds.includes(localCost.id) }">
            <div class="flex items-center justify-center">
                <font-awesome-icon icon="fa-solid fa-coins" class="primary text-black" />
            </div>
            <div class="flex items-center justify-start">
                <p class="text-black">
                    {{ localCost.name }}
                </p>
            </div>
            <div class="flex items-center justify-center text-black">
                {{ localCost.quantity }} x
            </div>
            <div class="flex items-center justify-end">
                <money-field name="price" v-model="localCost.price" />
            </div>
        </div>
    </div>
</template>

<script>
import CostCreateForm from "@/components/forms/CostCreateForm.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";
import ProposalCostCreateForm from '../forms/ProposalCostCreateForm.vue';

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
        ProposalCostCreateForm,
    },
    methods: {
        addProposalCostCreated({ proposalCosts, newTotalThirdPartyCost }) {
            proposalCosts.forEach(newCost => {
                const index = this.localCosts.findIndex(cost => cost.id === newCost.id);
                if (index !== -1) {
                    // Substituir o valor existente
                    this.localCosts[index] = newCost;
                } else {
                    // Adicionar novo item
                    this.localCosts.unshift(newCost);
                }
                this.highlight(newCost.id);
                this.$emit('update-total-third-party-cost', newTotalThirdPartyCost);
            });
        },
        highlight(proposalCostId) {
            this.highlightProposalCostIds.push(proposalCostId);
            setTimeout(() => {
                this.highlightProposalCostIds = this.highlightProposalCostIds.filter(id => id !== proposalCostId);
            }, 2000);

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