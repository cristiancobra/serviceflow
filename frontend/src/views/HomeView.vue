<template>
  <div class="home pt-5 pb-5">
    <div>
      <p class="text-center">
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>
    <div class="container">
      <TasksList template="priorized" />
    </div>
  </div>
</template>

<script>
import TasksList from "../components/lists/TasksList.vue";

export default {
  data() {
    return {
      dateNow: "", // Inicializando a propriedade dateNow
    };
  },
  components: {
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
