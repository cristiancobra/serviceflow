<template>
  <div class="container">
    <div class="row mt-5 mb-5">
      <div class="col-4">
        <div class="small-container">
          <total-opportunities />
        </div>
      </div>
    </div>
    <div class="row">
      <p class="text-center">
        Bem vindo, hoje é
        <span style="font-weight: bolder">
          {{ dateNow }}
        </span>
      </p>
    </div>
    <TasksHomeList />
  </div>
</template>

<script>
import "../assets/css/dashboard.css";
import TotalOpportunities from "../components/dashboard/totalOpportunities.vue";
import TasksHomeList from "../components/lists/TasksHomeList.vue";

export default {
  data() {
    return {
      dateNow: "",
    };
  },
  components: {
    TotalOpportunities,
    TasksHomeList,
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
