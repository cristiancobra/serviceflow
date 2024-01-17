<template>
  <div class="home pb-5">
    <div>
      <p style="margin-top: 120px">
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>
    <div class="container">
      <div class="row">
        <TasksList :tasks="tasks" />
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, TASK_PRIORIZED_URL } from "@/config/apiConfig";
import axios from "axios";
import TasksList from "../components/lists/TasksList.vue";

export default {
  data() {
    return {
      dateNow: "", // Inicializando a propriedade dateNow
      tasks: [],
    };
  },
  components: {
    TasksList,
  },
  mounted() {
    this.getDateNow();
    this.getTasksHome();
  },
  methods: {
    getDateNow() {
      const data = new Date();
      const options = { year: "numeric", month: "long", day: "numeric" };
      this.dateNow = data.toLocaleDateString("pt-BR", options);
    },
    getTasksHome() {
      axios
        .get(`${BACKEND_URL}${TASK_PRIORIZED_URL}`)
        .then((response) => {
          this.tasks = response.data.data;
          // this.filteredTasks = this.tasks; // Inicialmente, as tarefas filtradas são iguais a todas as tarefas
        })
        .catch((error) => console.log(error));
    },
  },
};
</script>

