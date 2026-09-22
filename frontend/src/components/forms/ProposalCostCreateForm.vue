<template>
  <ModalCard
    title="Adicionar Custos"
    icon="fa-solid fa-coins"
    size="lg"
    :compact="compact"
    @close="closeModal"
  >
    <form id="proposalCostCreateForm" @submit.prevent="submitForm">
      <div class="mb-4">
        <TextAreaInput
          label="Detalhamento:"
          name="description"
          v-model="form.description"
          placeholder="Detalhamento da oportunidade"
          :rows="4"
        />
      </div>
      <div v-if="costs.length === 0" class="table-row">
        <p>Você ainda não possui custos cadastrados.</p>
      </div>
      <div v-else class="section-container">
        <div class="section-title">
          <h3>Custos:</h3>
        </div>
        <div class="table-row" v-for="cost in costs" :key="cost.id">
          <div class="quantity-column">
            <input
              type="number"
              min="0"
              :id="cost.id"
              v-model.number="cost.quantity"
              placeholder="0"
            />
          </div>
          <div class="title-column">
            <label :for="cost.id">
              {{ cost.name }}
            </label>
          </div>
          <div class="price-column">R$ {{ cost.price }}</div>
        </div>
      </div>
    </form>

    <template #footer>
      <button
        type="button"
        class="px-6 py-2 text-base-content bg-base-100 border border-base-300 rounded-lg font-semibold hover:bg-base-200 transition-colors"
        @click="closeModal"
      >
        Cancelar
      </button>
      <button
        type="submit"
        form="proposalCostCreateForm"
        class="px-6 py-2 bg-primary hover:opacity-90 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-plus" />
        Adicionar
      </button>
    </template>
  </ModalCard>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { submitFormUpdate } from "@/utils/requests/httpUtils";
import TextAreaInput from "./inputs/textarea/TextAreaInput.vue";
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
    name: "ProposalCostCreateForm",
    emits: ["new-proposal-cost-event", "close"],
    components: {
        TextAreaInput,
        ModalCard,
    },
    props: {
        proposalId: {
            type: Number,
            required: true,
        },
        // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
        compact: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            costs: [],
            form: {
                name: null,
                description: null,
                user_id: null,
            },
            services: [],
            selectedServices: [],
        };
    },
    methods: {
        index,
        submitFormUpdate,
        closeModal() {
            this.$emit("close");
        },
        async getCosts() {
            this.costs = await this.index("costs");
        },
        async submitForm() {
            this.form.proposalCosts = this.costs
                .filter(cost => cost.quantity > 0)
                .map(cost => ({
                    id: cost.id,
                    quantity: cost.quantity,
                    price: cost.price,
                }));

            const { data, error } = await this.submitFormUpdate("proposals", this.proposalId, this.form);

            if (data) {
                this.$emit("close");
                this.$emit("new-proposal-cost-event", {
                    proposalCosts: data.proposalCosts,
                    newTotalThirdPartyCost: data.total_third_party_cost,
                });
            }
            if (error) {
                this.errors = error;
            }
        },
    },
    mounted() {
        this.getCosts();
    },
};
</script>

<style scoped>
.quantity-column {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-basis: 5%;
    text-align: right;
    margin-right: 2rem;
}

.price-column {
    display: flex;
    align-items: center;
    justify-content: right;
    flex-basis: 15%;
}

.title-column {
    display: flex;
    align-items: left;
    justify-content: left;
    flex-basis: 70%;
}
</style>
