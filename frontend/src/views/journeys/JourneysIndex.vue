<template>
  <div class="container mb-5">
    <JourneysFilter
      @filter-canceled="getJourneysCanceled"
      @filter-doing="getJourneysDoing"
      @filter-done="getJourneysDone"
      @filter-late="getJourneysLate"
      @filter-to-do="getJourneysToDo"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <div class="row">
      <JourneysList :journeys="filteredJourneys" :columns="2" @toggle="toggle" />
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, JOURNEY_URL, JOURNEY_URL_PARAMETER } from "@/config/apiConfig";
import axios from "axios";
import JourneysList from "@/components/lists/JourneysList.vue";
import JourneysFilter from "@/components/filters/JourneysFilter.vue";
import SuccessMessage from '../../components/forms/messages/SuccessMessage.vue';

export default {
  name: "JourneysIndexView",
  components: {
    JourneysList,
    JourneysFilter,
    SuccessMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      journeys: [],
      filteredJourneys: [],
      newTask: null,
    };
  },
  methods: {
    getJourneys() {
      axios
      .get(`${BACKEND_URL}${JOURNEY_URL}`)
        .then((response) => {
          this.journeys = response.data.data;
          this.filteredJourneys = this.journeys;
          console.log(this.filteredJourneys);
        })
        .catch((error) => console.log(error));
    },
    // addTaskCreated(newTask) {
    //   this.toggle();
    //   this.filteredJourneys.unshift(newTask);
    // },
    getJourneysCanceled() {
      axios
        .get(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}filter-status?status=canceled`) // Faz a requisição filtrando por status "done"
        .then((response) => {
          this.filteredJourneys = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getJourneysDoing() {
      axios
      .get(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}filter-status?status=doing`) // Faz a requisição filtrando por status "doing"
        .then((response) => {
          this.filteredJourneys = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getJourneysDone() {
      axios
      .get(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}filter-status?status=done`) // Faz a requisição filtrando por status "done"
        .then((response) => {
          this.filteredJourneys = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getJourneysLate() {
      axios
      .get(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}filter-date`) // Faz a requisição filtrando por status "late"
        .then((response) => {
          this.filteredJourneys = response.data.data;
        })
        .catch((error) => console.log(error));
    },
    getJourneysToDo() {
      axios
      .get(`${BACKEND_URL}${JOURNEY_URL_PARAMETER}filter-status?status=to-do`) // Faz a requisição filtrando por status "to-do"
        .then((response) => {
          this.filteredJourneys = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getJourneys();
  },
};
</script>

<style scoped>
.headers-line {
  margin-top: 40px;
  margin-bottom: 50px;
  margin-left: 80px;
  margin-right: 80px;
  display: flex;
  justify-content: center;
}
.slot {
  border-width: 2px;
  border-style: solid;
  border-color: white;
  border-radius: 20px 20px 20px 20px;
  padding: 10px 15px 10px 15px;
  margin: 0 4px 0 4px;
  color: white;
  font-weight: 800;
  width: 120px;
}
.new {
  border-radius: 20px 20px 20px 20px;
  background-color: white;
  border-color: #ff3eb5;
  color: #ff3eb5;
  margin-left: 50px;
  width: 60px;
  font-size: 16px;
}
.new:hover {
  background-color: #ff3eb5;
  color: white;
  margin-left: 50px;
  width: 60px;
}
.show {
  display: block;
  transition: display 2s;
}
</style>
