<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="page-icon" />
        <h1 class="">AGENDA</h1>
      </div>
      <div class="section-action">
        <div class="text-800 text-white">
          <span>{{ dateNow }}</span>
        </div>
      </div>

    </div>

    <tasks-list-section 
      :tasks="localTasks" 
      :showOpportunityColumn="true" 
      sortOrder="asc" 
    />
  </div>
</template>

<script>
import axios from "axios";
import {
  BACKEND_URL,
  TASK_PRIORIZED_URL,
} from "@/config/apiConfig";
import "../assets/css/dashboard.css";
import TasksListSection from '../components/lists/TasksListSection.vue';

export default {
  data() {
    return {
      dateNow: "",
      localTasks: [],
    };
  },
  components: {
    TasksListSection,
  },
  methods: {
    getDateNow() {
      const data = new Date();
      const options = { year: "numeric", month: "long", day: "numeric" };
      this.dateNow = data.toLocaleDateString("pt-BR", options);
    },
    async getTasksPriorized() {
      axios
        .get(`${BACKEND_URL}${TASK_PRIORIZED_URL}`)
        .then((response) => {
          this.localTasks = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getDateNow();
    this.getTasksPriorized();
  },
};
</script>