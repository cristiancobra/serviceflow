<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-clock" class="page-icon" />
        <h1>JORNADAS</h1>
      </div>
    </div>

    <journeys-filter
      @filter-change="handleFilterChange"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <JourneysList 
      :journeys="journeys"
      :paginationData="paginationData" 
    />
  </div>
</template>

<script>
import axios from "axios";
import { BACKEND_URL, JOURNEY_URL } from "@/config/apiConfig";
import JourneysList from "@/components/lists/JourneysList.vue";
import JourneysFilter from "@/components/filters/JourneysFilter.vue";
import SuccessMessage from "@/components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "@/components/forms/messages/ErrorMessage.vue";

export default {
  name: "JourneysIndexView",
  components: {
    JourneysList,
    JourneysFilter,
    SuccessMessage,
    ErrorMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      formResponse: {},
      journeys: [],
      paginationData: null,
    };
  },
  methods: {
    async getJourneys() {
      try {
        const response = await axios.get(`${BACKEND_URL}${JOURNEY_URL}`);
        this.journeys = response.data.data;
        this.paginationData = response.data;
      } catch (error) {
        console.error('Erro ao buscar jornadas:', error);
        this.isError = true;
        this.formResponse = { message: 'Erro ao carregar jornadas' };
      }
    },
    
    async handleFilterChange(filter) {
      try {
        if (!filter) {
          // Se não houver filtro, busca todas as jornadas
          await this.getJourneys();
          return;
        }
        
        // Chama o endpoint de filtro do backend
        const response = await axios.get(`${BACKEND_URL}${JOURNEY_URL}/filter`, {
          params: { filter }
        });
        
        this.journeys = response.data.data;
        this.paginationData = response.data;
      } catch (error) {
        console.error(`Erro ao filtrar jornadas (${filter}):`, error);
        this.isError = true;
        this.formResponse = { message: 'Erro ao filtrar jornadas' };
      }
    },
  },
  mounted() {
    this.getJourneys();
  },
};
</script>

<style scoped>
/* Estilos gerenciados por classes globais e Tailwind */
</style>
