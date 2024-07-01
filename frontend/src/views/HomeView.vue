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
      <div class="row mb-5">
        <ProjectsList :projects="projects" />
      </div>
      <div class="row">
        <TasksList template="priorized" />
      </div>
    </div>
  </div>
</template>

<script>
import { BACKEND_URL, PROJECTS_PRIORIZED_URL } from "@/config/apiConfig";
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
    getProjectsHome() {
      axios
        .get(`${BACKEND_URL}${PROJECTS_PRIORIZED_URL}`)
        .then((response) => {
          this.projects = response.data.data;
        })
        .catch((error) => console.log(error));
    },
  },
  mounted() {
    this.getDateNow();
    this.getProjectsHome();
  },
};
</script>
