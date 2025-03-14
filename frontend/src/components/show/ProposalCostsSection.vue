<template>
    <div class="section-container">
        <div class="section-header">
            <div class="subtitle-container">
                <font-awesome-icon icon="fas fa-coins" class="icon" />
                <h2>
                    Custos de propdução
                </h2>
            </div>
            <div class="action-container">
                <cost-create-form @new-cost-event="addCostCreated" />
                <proposal-cost-create-form @new-proposal-cost-event="addProposalCostCreated"
                    :proposalId="proposal.id" />
            </div>
        </div>
        <div class="table-row" v-for="localCost in localCosts" v-bind:key="localCost.id"
            :class="{ 'highlight': highlightProposalCostIds.includes(localCost.id) }">
            <div class="icon-column">
                <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
            </div>
            <div class="title-column">
                <p class="name">
                    {{ localCost.name }}
                </p>
            </div>
            <div class="integer-column">
                {{ localCost.quantity }} x
            </div>
            <div class="price-column">
                <money-field name="price" v-model="localCost.price" />
            </div>
            <div class="total-price-column">
                <money-field name="total_price" v-model="localCost.total_price" />
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
.icon-column {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 1rem;
    flex-basis: 0%;
}

.integer-column {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-basis: 5%;
}

.price-column {
    display: flex;
    align-items: center;
    justify-content: right;
    flex-basis: 10%;
}

.total-price-column {
    display: flex;
    align-items: center;
    justify-content: right;
    flex-basis: 15%;
    font-weight: 800;
}

.title-column {
    display: flex;
    align-items: left;
    justify-content: left;
    flex-basis: 70%;
}
</style>