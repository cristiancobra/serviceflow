<template>
  <div class="list-container mb-5 mt-0">
    <div class="row">
      <div class="col d-flex justify-content-left">
        <font-awesome-icon icon="fa-solid fa-tools" class="icon" />
        <h2 class="title">Custos de produção</h2>
      </div>
    </div>
    <div class="row mt-3 mb-4">
      <div class="col-10">
        <input type="text" class="form-control search-container" v-model="searchTerm"
          placeholder="Digite para buscar" />
      </div>
      <div class="col-2 d-flex justify-content-end">
        <cost-create-form @new-cost-event="addCostCreated" />  
      </div>
    </div>
    <div class="row" v-for="cost in costs" v-bind:key="cost.id">
      <div class="col cards">
        <router-link :to="{ name: 'costShow', params: { id: cost.id } }">
          <div class="row title">
            <div class="col">
              <p class="name ps-2">
                {{ cost.name }}
              </p>
            </div>
            <div class="col">
              <p class="price">R$ {{ cost.price }}</p>
            </div>
          </div>
        </router-link>
      </div>
      <router-view />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, COST_URL } from "@/config/apiConfig";
import axios from "axios";
import CostCreateForm from '../forms/CostCreateForm.vue';
import { index } from "@/utils/requests/httpUtils";

export default {
  components: {
    CostCreateForm
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
          .put(
            `${BACKEND_URL}${COST_URL}${cost.id}`,
            this.updatedCost
          )
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    this.getCosts();
  }
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

.icon:hover {}

.icon-col {
  font-size: 16px;
  display: inline-block;
  align-items: center;
  /* Centraliza verticalmente */
  justify-content: center;
  /* Centraliza horizontalmente */
  width: 35px;
  height: 35px;
  margin-right: 12px;
  margin-top: -8px;
  padding: 10px;
  background-color: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  /* Reduz a intensidade do sombreamento */
  transition: font-size 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.icon-col:hover {
  font-size: 20px;
  background-color: #f6f6f6;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
  transform: perspective(500px) rotateX(10deg);
  transform-origin: center center;
  /* Inicia a transformação a partir do centro */
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
