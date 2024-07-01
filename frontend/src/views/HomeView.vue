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
        <ProjectsList template="priorized" />
      </div>
      <div class="row">
        <TasksList template="priorized" />
      </div>
    </div>
  </div>
</template>

<script>
import ProjectsList from "../components/lists/ProjectsList.vue";
import TasksList from "../components/lists/TasksList.vue";

export default {
  data() {
    return {
      dateNow: "", // Inicializando a propriedade dateNow
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
  },
  mounted() {
    this.getDateNow();
  },
};
</script>
