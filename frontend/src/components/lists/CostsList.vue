<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tools" class="page-icon" />
        <h1>CUSTOS DE PRODUÇÃO</h1>
      </div>
      <div class="action-container">
        <cost-create-form @new-cost-event="addCostCreated" />
      </div>
    </div>

    <section class="section-container">
      <div class="mb-4">
        <input
          type="text"
          class="w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
          v-model="searchTerm"
          placeholder="🔍 Buscar por nome ou descrição..."
        />
      </div>
      
      <div v-if="!costs.length" class="text-center py-12">
        <font-awesome-icon icon="fa-solid fa-inbox" class="text-gray-300 text-6xl mb-4" />
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum custo cadastrado</h3>
        <p class="text-gray-500">Comece criando seu primeiro custo de produção</p>
      </div>
      
      <div v-else>
        <!-- Header da Tabela -->
        <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-100 rounded-t-lg font-semibold text-sm text-gray-700 border-b border-gray-300">
          <div class="col-span-1 flex items-center justify-center">
            <font-awesome-icon icon="fa fa-cogs" class="text-primary" />
          </div>
          <div class="col-span-5">Nome</div>
          <div class="col-span-3 text-center">Criado em</div>
          <div class="col-span-3 text-right">Preço</div>
        </div>

        <!-- Linhas da Tabela -->
        <router-link
          v-for="cost in filteredCosts"
          :key="cost.id"
          :to="{ name: 'costShow', params: { id: cost.id } }"
          class="grid grid-cols-12 gap-4 px-4 py-4 bg-white hover:bg-gray-50 border-b border-gray-200 transition-colors duration-150 items-center no-underline text-gray-900"
        >
          <!-- Ícone -->
          <div class="col-span-12 md:col-span-1 flex items-center justify-center mb-3 md:mb-0">
            <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center">
              <font-awesome-icon icon="fa fa-cogs" class="text-primary text-lg" />
            </div>
          </div>

          <!-- Nome -->
          <div class="col-span-12 md:col-span-5">
            <p class="font-semibold text-gray-900 text-base mb-1">
              {{ cost.name }}
            </p>
            <p v-if="cost.observations" class="text-sm text-gray-500 line-clamp-1">
              {{ cost.observations }}
            </p>
          </div>

          <!-- Data de Criação -->
          <div class="col-span-6 md:col-span-3 text-left md:text-center">
            <p class="text-sm text-gray-600">
              {{ formatDate(cost.created_at) }}
            </p>
            <p class="text-xs text-gray-400">
              {{ formatTime(cost.created_at) }}
            </p>
          </div>

          <!-- Preço -->
          <div class="col-span-6 md:col-span-3 text-right">
            <p class="text-base font-bold text-primary">
              {{ formatCurrency(cost.price) }}
            </p>
          </div>
        </router-link>
      </div>
    </section>
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
      searchTerm: "",
      updatedCost: {
        id: null,
        name: null,
        price: null,
      },
    };
  },
  computed: {
    filteredCosts() {
      if (!this.searchTerm) {
        return this.costs;
      }
      const search = this.searchTerm.toLowerCase();
      return this.costs.filter(cost => 
        cost.name.toLowerCase().includes(search) ||
        (cost.observations && cost.observations.toLowerCase().includes(search))
      );
    }
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
    formatCurrency(value) {
      if (!value) return 'R$ 0,00';
      return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      }).format(parseFloat(value));
    },
    formatDate(datetime) {
      if (!datetime) return '---';
      const date = new Date(datetime);
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    formatTime(datetime) {
      if (!datetime) return '';
      const date = new Date(datetime);
      return date.toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },
  },
  mounted() {
    this.getCosts();
  },
};
</script>

<style scoped>
/* Mantém apenas estilos necessários que não são Tailwind */
.name {
  text-align: left;
  font-size: 16px;
  font-weight: 400;
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
  color: black.
}

.money {
  text-align: right;
  font-size: 16px;
  font-weight: 400.
}

.price {
  text-align: right;
  margin-top: 0px;
  margin-bottom: -0px;
  font-size: 1rem;
  font-weight: 600.
}

.icon {
  text-align: center;
  font-weight: 400.
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
  color: var(--primary).
}

.comments {
  text-align: left;
  font-size: 14px;
  margin-top: 20px.
}

a {
  color: rgb(61, 61, 61).
}

a:link {
  text-decoration: none.
}

a:visited {
  text-decoration: none.
}

a:hover {
  text-decoration: none.
}

a:active {
  text-decoration: none.
}

.label {
  text-align: left.
}
</style>
