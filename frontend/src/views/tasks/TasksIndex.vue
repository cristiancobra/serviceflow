<template>
  <div class="page-container">
    <div class="page-header">
      <div class="page-title">
        <font-awesome-icon icon="fa-solid fa-tasks" class="page-icon" />
        <h1>TAREFAS</h1>
      </div>
    </div>

    <TasksFilter
      @filter-change="handleFilterChange"
    />

    <ErrorMessage v-if="isError" :formResponse="formResponse" />
    <SuccessMessage v-if="isSuccess" :formResponse="formResponse" />

    <TasksListSection 
      :tasks="filteredTasks" 
      :showOpportunityColumn="true" 
      sortOrder="desc" 
    />
  </div>
</template>

<script>
import { BACKEND_URL, TASK_URL_PARAMETER } from "@/config/apiConfig";
import axios from "axios";
import TasksListSection from "@/components/lists/TasksListSection.vue";
import TasksFilter from "@/components/filters/TasksFilter.vue";
import SuccessMessage from "../../components/forms/messages/SuccessMessage.vue";
import ErrorMessage from "../../components/forms/messages/ErrorMessage.vue";

export default {
  name: "TasksIndexView",
  components: {
    TasksListSection,
    TasksFilter,
    SuccessMessage,
    ErrorMessage,
  },
  data() {
    return {
      isError: false,
      isSuccess: false,
      hasError: false,
      data: null,
      tasks: [],
      filteredTasks: [],
      newTask: null,
    };
  },
  methods: {
    async getTasks() {
      try {
        const response = await axios.get(`${BACKEND_URL}tasks`);
        this.filteredTasks = response.data.data;
      } catch (error) {
        console.error('Erro ao buscar tarefas:', error);
        this.isError = true;
      }
    },
    async handleFilterChange(status) {
      try {
        if (!status) {
          // Se status for null, mostra todas as tarefas
          await this.getTasks();
          return;
        }

        let endpoint = '';
        if (status === 'late') {
          endpoint = `${BACKEND_URL}${TASK_URL_PARAMETER}filter-date`;
        } else {
          endpoint = `${BACKEND_URL}${TASK_URL_PARAMETER}filter-status?status=${status}`;
        }

        const response = await axios.get(endpoint);
        this.filteredTasks = response.data.data;
      } catch (error) {
        console.error(`Erro ao filtrar tarefas (${status}):`, error);
        this.isError = true;
      }
    },
  },
  mounted() {
    this.getTasks();
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
