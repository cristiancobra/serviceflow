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
      
      <!-- Header da tabela -->
      <div class="flex items-center w-full px-4 py-2 bg-gray-100 font-semibold text-sm text-gray-700 border-b border-gray-300">
        <div class="flex items-center justify-center w-12"></div>
        <div class="flex items-center justify-start w-48 px-2">Nome</div>
        <div class="flex items-center justify-start flex-1 px-2">Observações</div>
        <div class="flex items-center justify-center w-24 px-2">Horas</div>
        <div class="flex items-center justify-end w-32 px-2">Valor/Hora</div>
        <div class="flex items-center justify-end w-32 px-2">Total Horas</div>
        <div class="flex items-center justify-center w-24 px-2">Lucro %</div>
        <div class="flex items-center justify-end w-32 px-2">Lucro R$</div>
        <div class="flex items-center justify-end w-32 px-2">Preço</div>
        <div class="flex items-center justify-end w-32 px-2">Preço Final</div>
      </div>

      <div
        class="table-row"
        v-for="service in services"
        v-bind:key="service.id"
      >
        <router-link
          :to="{ name: 'serviceShow', params: { id: service.id } }"
          class="flex items-center w-full no-underline text-black hover:bg-gray-50 transition-colors py-2"
        >
          <div class="flex items-center justify-center text-xl w-12 text-primary">
            <font-awesome-icon icon="fa fa-tools" />
          </div>
          <div class="flex items-start justify-start w-48 px-2">
            {{ service.name }}
          </div>
          <div class="flex items-start justify-start flex-1 px-2 text-gray-600 text-sm">
            {{ service.observations || '-' }}
          </div>
          <div class="flex items-center justify-center w-24 px-2 text-sm">
            {{ formatLaborHours(service.labor_hours) }}
          </div>
          <div class="flex items-center justify-end w-32 px-2 text-sm">
            {{ formatCurrencySymbol(service.labor_hourly_rate) }}
          </div>
          <div class="flex items-center justify-end w-32 px-2 text-sm">
            {{ formatCurrencySymbol(service.labor_hourly_total) }}
          </div>
          <div class="flex items-center justify-center w-24 px-2 text-sm">
            {{ service.profit_percentage }}%
          </div>
          <div class="flex items-center justify-end w-32 px-2 text-sm">
            {{ formatCurrencySymbol(service.profit) }}
          </div>
          <div class="flex items-center justify-end w-32 px-2 text-sm">
            {{ formatCurrencySymbol(service.price) }}
          </div>
          <div class="flex items-center justify-end w-32 px-2">
            <p class="font-bold text-primary m-0 text-sm">{{ formatCurrencySymbol(service.final_price) }}</p>
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
/* Removido - usando Tailwind CSS */
</style>
