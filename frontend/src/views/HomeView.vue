<template>
  <div class="page-container">
    <PageHeader>
      <template #actions>
        <div class="text-800 text-white">
          <span>{{ dateNow }}</span>
        </div>
      </template>
    </PageHeader>

    <tasks-list-section 
      :tasks="localTasks" 
      :showOpportunityColumn="true" 
      sortOrder="asc" 
      :hide-closed-tasks="true"
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
import PageHeader from "@/components/layout/PageHeader.vue";

export default {
  data() {
    return {
      dateNow: "",
      localTasks: [],
    };
  },
  components: {
    PageHeader,
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