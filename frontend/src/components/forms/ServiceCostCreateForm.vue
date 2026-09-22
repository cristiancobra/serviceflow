<template>
  <ModalCard
    title="Adicionar Custos"
    icon="fa-solid fa-coins"
    size="lg"
    :compact="compact"
    @close="closeModal"
  >
    <form id="serviceCostCreateForm" @submit.prevent="submitForm">
      <div v-if="costs.length === 0" class="table-row">
        <p>Você ainda não possui custos cadastrados.</p>
      </div>
      <div v-else class="section-container">
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
        form="serviceCostCreateForm"
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
import ModalCard from "@/components/modals/ModalCard.vue";

export default {
  name: "ServiceCostCreateForm",
  emits: ["new-service-cost-event", "close"],
  props: {
    serviceId: {
      type: Number,
      required: true,
    },
    // Passado automaticamente pelo App.vue quando há mais de um modal aberto ao mesmo tempo
    compact: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    ModalCard,
  },
  data() {
    return {
      costs: [],
      form: {
        user_id: null,
      },
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
      this.form.serviceCosts = this.costs
        .filter((cost) => cost.quantity > 0)
        .map((cost) => ({
          id: cost.id,
          quantity: cost.quantity,
          price: cost.price,
        }));

      const { data, error } = await this.submitFormUpdate(
        "services",
        this.serviceId,
        this.form
      );

      if (data) {
        this.$emit("close");
        this.$emit("new-service-cost-event", { service: data });
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
