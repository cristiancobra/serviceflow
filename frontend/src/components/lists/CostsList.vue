<template>
  <div class="page-container">
    <div class="page-header">
      <div class="title-container">
        <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
        <h1>Custos de produção</h1>
      </div>
      <div class="action-container">
        <cost-create-form @new-cost-event="addCostCreated" />
      </div>
    </div>
    <div class="table-row">
      <input
        type="text"
        class="form-control search-container"
        v-model="searchTerm"
        placeholder="Digite para buscar"
      />
    </div>
    <div class="table-row" v-for="cost in costs" v-bind:key="cost.id">
      <router-link
        :to="{ name: 'costShow', params: { id: cost.id } }"
        class="link-row"
      >
        <div class="icon-column">
          <font-awesome-icon icon="fa fa-cogs" />
        </div>
        <div class="column-80">
          <p class="name ps-2">
            {{ cost.name }}
          </p>
        </div>
        <div class="column-20">
          <p class="price">R$ {{ cost.price }}</p>
        </div>
      </router-link>
      <router-view />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, COST_URL } from "@/config/apiConfig";
import axios from "axios";
import CostCreateForm from "../forms/CostCreateForm.vue";
import { index } from "@/utils/requests/httpUtils";

export default {
  components: {
    CostCreateForm,
  },
  data() {
    return {
      isActive: true,
      costs: [],
      updatedCost: {
        id: null,
        name: null,
        price: null,
      },
    };
  },
  methods: {
    addCostCreated(newCost) {
      this.toggle();
      this.costs.unshift(newCost);
    },
    async getCosts() {
      const costs = await index("costs");
      console.log(costs);
      this.costs = costs;
    },
    toggle() {
      this.isActive = !this.isActive;
    },
    saveCost(cost, field) {
      if (cost.activeField === field) {
        cost.editing = false;
        cost.editingField = null;

        this.updatedCost.id = cost.id;
        this.updatedCost.name = cost.name;
        this.updatedCost.price = cost.price;

        axios
          .put(`${BACKEND_URL}${COST_URL}${cost.id}`, this.updatedCost)
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    this.getCosts();
  },
};
</script>

<style scoped>
.name {
  text-align: left;
  font-size: 20px;
  font-weight: 600;
}

.big {
  font-size: 44px;
  color: var(--green);
}

.card {
  border-style: solid;
  border-width: 2px;
  border-color: var(--green);
  border-radius: 6px;
  padding: 10px;
  background-color: var(--green-light);
  /* height: 15vh; */
}

.link-row {
  display: flex;
  align-items: center;
  justify-content: left;
  flex-basis: 100%;
  text-decoration: none;
  color: black;
}

.money {
  text-align: right;
  font-size: 16px;
  font-weight: 400;
}

.price {
  text-align: right;
  margin-top: 0px;
  margin-bottom: -0px;
  font-size: 1.2rem;
  font-weight: 800;
}

.icon {
  text-align: center;
  font-weight: 400;
}

.icon:hover {
}

.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  margin: 1rem;
  flex-basis: 0%;
  color: var(--primary);
}

.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px;
}

a {
  color: rgb(61, 61, 61);
}

a:link {
  text-decoration: none;
}

a:visited {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

a:active {
  text-decoration: none;
}

.label {
  text-align: left;
}
</style>
