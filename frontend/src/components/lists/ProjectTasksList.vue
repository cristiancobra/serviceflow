<template>
  <TasksList :tasks="tasks" />
</template>

<script>
import { BACKEND_URL, TASK_BY_PROJECT_URL } from "@/config/apiConfig";
import axios from "axios";
import TasksList from "@/components/lists/TasksList.vue";

export default {
  name: "TasksIndexView",
  components: {
    TasksList,
  },
  data() {
    return {
      projectId: this.$route.params.id,
      tasks: [],
      filteredTasks: [],
    };
  },
  methods: {
    async getTasks(page = 1) {

      this.tasksUrl = `${BACKEND_URL}${TASK_BY_PROJECT_URL}project_id=${this.projectId}&per_page=10&page=${page}`;

      try {
        const response = await axios.get(this.tasksUrl);

        this.tasks = response.data.data.map(task => {
          return { ...task, editing: false }; // Adiciona a propriedade editing a cada task
        });

        this.paginationData = {
          links: response.data.links,
          meta: response.data.meta,
        };

      } catch (error) {
        console.error("Erro ao acessar tarefas:", error);
      }
    },
  },
  created() {
    this.getTasks();
  },
};
</script>