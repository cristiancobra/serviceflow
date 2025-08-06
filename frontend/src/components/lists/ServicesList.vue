<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tools" class="page-icon" />
        <h1>SERVIÇOS</h1>
      </div>
      <div class="action-container">
        <service-create-form @new-service-event="addServiceCreated" />
      </div>
    </div>
    <div class="section-container">
      <div class="search-container">
        <input
          type="text"
          class="search-input"
          v-model="searchTerm"
          placeholder="Digite para buscar"
        />
      </div>
      <div
        class="table-row"
        v-for="service in services"
        v-bind:key="service.id"
      >
        <router-link
          :to="{ name: 'serviceShow', params: { id: service.id } }"
          class="link-row"
        >
          <div class="icon-column">
            <font-awesome-icon icon="fa fa-tools" />
          </div>
          <div class="title-column">
            {{ service.name }}
          </div>
          <div class="price-column">{{ formatCurrencySymbol(service.price) }}</div>
          <div class="price-column">
            <p class="price-active">{{ formatCurrencySymbol(service.final_price) }}</p>
          </div>
        </router-link>
        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, SERVICE_URL } from "@/config/apiConfig";
import { formatCurrencySymbol } from "@/utils/number/moneyUtils";
import axios from "axios";
import ServiceCreateForm from "../forms/ServiceCreateForm.vue";
import { index } from "@/utils/requests/httpUtils";

export default {
  name: "ServicesList",
  components: {
    ServiceCreateForm,
  },
  data() {
    return {
      isActive: true,
      services: [],
      updatedservice: {
        id: null,
        name: null,
        labor_hours: null,
        labor_hourly_rate: null,
        labor_hourly_total: null,
        profit_percentage: null,
        price: null,
      },
    };
  },
  methods: {
    formatCurrencySymbol,
    addServiceCreated(newService) {
      this.toggle();
      this.services.unshift(newService);
    },
    formatLaborHours(seconds) {
      const hours = Math.floor(seconds / 3600);
      const minutes = Math.floor((seconds % 3600) / 60);
      return `${hours}:${String(minutes).padStart(2, "0")}`;
    },
    async getServices() {
      const services = await index("services");
      this.services = services;
    },
    toggle() {
      this.isActive = !this.isActive;
    },
    saveservice(service, field) {
      if (service.activeField === field) {
        service.editing = false;
        service.editingField = null;

        this.updatedservice.id = service.id;
        this.updatedservice.name = service.name;
        this.updatedservice.labor_hours = service.labor_hours;
        this.updatedservice.labor_hourly_rate = service.labor_hourly_rate;
        this.updatedservice.labor_hourly_total = service.labor_hourly_total;
        this.updatedservice.profit_percentage = service.profit_percentage;
        this.updatedservice.price = service.price;

        axios
          .put(`${BACKEND_URL}${SERVICE_URL}${service.id}`, this.updatedservice)
          .then((response) => {
            console.log(response.data);
          });
      }
    },
  },
  mounted() {
    this.getServices();
  },
};
</script>

<style scoped>
.icon-column {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin: 1rem;
  flex-basis: 0%;
  color: var(--primary);
}

.link-row {
  display: flex;
  align-items: center;
  justify-content: left;
  flex-basis: 100%;
  text-decoration: none;
  color: black;
}

.price-active {
  font-weight: bold;
  color: var(--primary);
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
  margin: 1rem;
  flex-basis: 85%;
}
</style>
