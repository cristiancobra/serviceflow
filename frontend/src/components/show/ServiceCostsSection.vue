<template>
  <div class="section-container">
    <div class="section-header">
      <div class="section-title">
        <font-awesome-icon icon="fas fa-coins" class="icon" />
        <h2>Custos de propdução</h2>
      </div>
      <div class="action-container flex items-center gap-2">
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
          @click="openAddServiceCostsModal"
        >
          <font-awesome-icon icon="fa-solid fa-coins" class="text-lg" />
        </button>
      </div>
    </div>
    <div
      class="table-row"
      v-for="localCost in localCosts"
      v-bind:key="localCost.id"
      :class="{ highlight: highlightServiceCostIds.includes(localCost.id) }"
    >
      <div class="icon-column">
        <font-awesome-icon icon="fa-solid fa-coins" class="primary" />
      </div>
      <div class="column-50">
        <p class="name">
          {{ localCost.name }}
        </p>
      </div>
      <div class="column-10 column-integer">
        <div
          style="display: flex; align-items: center; justify-content: center"
        >
          <integer-editable-field
            name="labor_hours"
            v-model="localCost.quantity"
            placeholder="quantidade total de horas"
            @save="updateService('labor_hours', $event)"
          />
          <span class="unity">x</span>
        </div>
      </div>
      <div class="column-20 column-price">
        <money-field name="price" v-model="localCost.price" />
      </div>
      <div class="column-20 column-total-price">
        <money-field name="total_price" v-model="localCost.total_price" />
      </div>
      <div class="icon-column">
        <button
          class="button-circular delete"
          @click="deleteItem(localCost.id)"
        >
          <span class="delete">
            <font-awesome-icon icon="fa-solid fa-trash-alt" />
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
import { destroyRelationship } from "@/utils/requests/httpUtils";
import IntegerEditableField from "@/components/fields/number/IntegerEditableField.vue";
import MoneyField from "@/components/fields/number/MoneyField.vue";

export default {
  props: {
    service: {
      type: Object,
      required: false,
    },
  },
  data() {
    return {
      localCosts: this.service.serviceCosts,
      highlightServiceCostIds: [],
    };
  },
  components: {
    MoneyField,
    IntegerEditableField,
  },
  methods: {
    ...mapMutations(["openModal"]),
    destroyRelationship,
    openCreateCostModal() {
      this.openModal({
        component: "CostCreateForm",
        listeners: {
          // O custo é criado no catálogo geral; precisa ser associado a este
          // serviço depois, em "Adicionar Custos" (não entra direto em localCosts).
          "new-cost-event": () => {},
        },
      });
    },
    openAddServiceCostsModal() {
      this.openModal({
        component: "ServiceCostCreateForm",
        props: { serviceId: this.service.id },
        listeners: {
          "new-service-cost-event": this.addServiceCostCreated,
        },
      });
    },
    addServiceCostCreated({ service }) {
        console.log("addServiceCostCreated", service);
        service.costs.forEach((newCost) => {
        const index = this.localCosts.findIndex(
          (cost) => cost.id === newCost.id
        );
        const updatedCost = {
          ...newCost,
          total_price: newCost.quantity * newCost.price, // Calcula o total_price
        };

        if (index !== -1) {
          // Substituir o valor existente
          this.localCosts[index] = updatedCost;
        } else {
          // Adicionar novo item
          this.localCosts.unshift(updatedCost);
          this.highlight(newCost.id);
        }
        this.$emit("update-service-from-cost", service);
      });
    },
    async deleteItem(costId) {
      try {
        // Envia a requisição para desassociar o custo do serviço
        await destroyRelationship("services", this.service.id, "costs", costId);

        // Remove o custo localmente
        this.localCosts = this.localCosts.filter((cost) => cost.id !== costId);
      } catch (error) {
        console.error("Erro ao deletar o custo:", error);
      }
    },
    highlight(serviceCostId) {
      this.highlightServiceCostIds.push(serviceCostId);
      setTimeout(() => {
        this.highlightServiceCostIds = this.highlightServiceCostIds.filter(
          (id) => id !== serviceCostId
        );
      }, 2000);
    },
    updateService(field, value) {
      this.$emit("update-service-from-cost", field, value);
    },
  },
  watch: {
    service: {
      handler(newService) {
        this.localCosts = newService.costs.map((cost) => ({
          ...cost,
          total_price: cost.quantity * cost.price, // Calcula o total_price
        }));
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