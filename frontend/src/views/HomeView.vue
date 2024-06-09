<template>
  <div class="home pt-5 pb-5">
    <div id="welcome">
      <p>
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>
    <div class="container mt-5">
      <div class="row">
        <div class="col-6">
          <h2 class="text-center">Projetos abertos</h2>
          <ProjectsList :projects="projects" />
        </div>
        <div class="col-6">
          <h2 class="text-center">Tarefas em destaque</h2>
          <TasksList :tasks="tasks" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, PROJECTS_PRIORIZED_URL, TASK_PRIORIZED_URL } from "@/config/apiConfig";
import axios from "axios";
import ProjectsList from "../components/lists/ProjectsList.vue";
import TasksList from "../components/lists/TasksList.vue";

export default {
  data() {
    return {
      dateNow: "", // Inicializando a propriedade dateNow
      projects: [],
      tasks: [],
    };
  },
  components: {
    ProjectsList,
    TasksList,
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
    getProjectsHome() {
      axios
        .get(`${BACKEND_URL}${PROJECTS_PRIORIZED_URL}`)
        .then((response) => {
          this.projects = response.data.data;
          console.log("projects home", this.projects);
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getDateNow();
    this.getTasksHome();
    this.getProjectsHome();
  },
};
</script>
