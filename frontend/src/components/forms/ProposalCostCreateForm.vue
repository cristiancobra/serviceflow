<template>
    <div>
        <button
      type="button"
      @click="openModal"
      class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-500 hover:bg-gray-300 text-white hover:text-gray-900 transition cursor-pointer"
    >
      <font-awesome-icon icon="fa-solid fa-plus" class="text-lg" />
    </button>

        <div v-if="isModalVisible" class="myModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Adicionar custos</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="row mb-4 mt-4">
                                <TextAreaInput class="text-start" label="Detalhamento:" name="description"
                                    v-model="form.description" placeholder="Detalhamento da oportunidade" :rows="4" />
                            </div>
                            <div v-if="costs.length === 0" class="table-row">
                                <p>
                                    Você ainda não possui custos cadastrados.
                                </p>
                            </div>
                            <div v-else class="section-container">
                                <div class="section-title">
                                    <h3>
                                        Custos:
                                    </h3>
                                </div>
                                <div class="table-row" v-for="cost in costs" :key="cost.id">
                                    <div class="quantity-column">
                                        <input type="number" min="0" :id="cost.id" v-model.number="cost.quantity"
                                            placeholder="0" />
                                    </div>
                                    <div class="title-column">
                                        <label :for="cost.id">
                                            {{ cost.name }}
                                        </label>
                                    </div>
                                    <div class="price-column">
                                        R$ {{ cost.price }}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    @click="closeModal">Fechar</button>
                                <button type="submit" class="button-new" data-bs-dismiss="modal">criar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { index } from "@/utils/requests/httpUtils";
import { submitFormUpdate } from "@/utils/requests/httpUtils";
import TextAreaInput from "./inputs/textarea/TextAreaInput";

export default {
    components: {
        TextAreaInput,
    },
    props: {
        proposalId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            costs: [],
            form: {
                name: null,
                description: null,
                user_id: null,
                opportunity_id: this.opportunityId,
            },
            isModalVisible: false,
            modal: false,
            services: [],
            selectedServices: [],
        };
    },
    methods: {
        index,
        submitFormUpdate,
        closeModal() {
            this.isModalVisible = false;
        },
        openModal() {
            this.isModalVisible = true;
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
                this.isModalVisible = false;
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
.button-icon {
    margin-right: 0.5rem;
}

.button-text {
    font-size: 1rem;
    font-weight: 600;
}

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